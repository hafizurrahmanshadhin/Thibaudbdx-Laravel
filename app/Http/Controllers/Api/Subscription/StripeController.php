<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Exception;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Illuminate\Http\JsonResponse;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function checkout(Request $request, $id): JsonResponse
    {
        $plan = Plan::find($id);
        $user = $request->user();
        // dd($plan);

        // Free plan: assign directly
        if ($plan->price == 0) {
            Subscription::updateOrCreate([
                'user_id' => $user->id,
            ], [
                'plan_id' => $plan->id,
                'status'               => 'active',
                'starts_at'            => now(),
                'ends_at'              => null,
                'amount_paid'          => 0,
                'payment_method'       => null,
                'transaction_id'       => null,
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Free plan activated.',
            ]);
        }

        // Get URLs from request or use default
        // $frontendBaseUrl = 'http://localhost:3000';
        $successUrl = route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('subscription.cancel');


        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode'                 => 'payment',
            'line_items'           => [[
                'price_data' => [
                    'currency'     => strtolower($plan->currency),
                    'unit_amount'  => intval($plan->price * 100),
                    'product_data' => [
                        'name'        => $plan->name . ' Plan',
                        'description' => $plan->description,
                    ],
                ],
                'quantity'   => 1,
            ]],
            'customer_email'       => $user->email,
            'success_url'          => $successUrl,
            'cancel_url'           => $cancelUrl,
            'metadata'             => [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ],
            'payment_intent_data'  => [
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ],
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Checkout session created.',
            'data'    => ['id' => $session->id, 'url' => $session->url],
        ], 201);
    }

    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (Exception $e) {
            return response('Invalid payload', 400);
        }

        // 1. Handle payment_intent.succeeded (RECOMMENDED for one-time payments)
        if ($event->type === 'payment_intent.succeeded') {
            $intent   = $event->data->object;
            $metadata = $intent->metadata ?? [];
            $userId   = $metadata->user_id ?? null;
            $planId   = $metadata->plan_id ?? null;

            if ($userId && $planId) {
                // Expire previous active subscriptions
                Subscription::where('user_id', $userId)
                    ->where('status', 'active')
                    ->update(['status' => 'expired', 'cancelled_at' => now()]);

                // Create new active subscription
                $plan   = Plan::find($planId);
                $endsAt = null;
                if ($plan) {
                    $endsAt = $plan->billing_interval === 'year'
                        ? now()->addYear()
                        : now()->addMonth();
                }

                Subscription::create([
                    'user_id'              => $userId,
                    'plan_id' => $planId,
                    'status'               => 'active',
                    'starts_at'            => now(),
                    'ends_at'              => $endsAt,
                    'amount_paid'          => $intent->amount_received / 100,
                    'payment_method'       => 'stripe',
                    'transaction_id'       => $intent->id,
                ]);
            }
        }

        // 2. Optionally, still handle checkout.session.completed for safety
        if ($event->type === 'checkout.session.completed') {
            $session  = $event->data->object;
            $metadata = $session->metadata ?? [];
            $userId   = $metadata->user_id ?? null;
            $planId   = $metadata->plan_id ?? null;

            if ($userId && $planId) {
                $plan   = Plan::find($planId);
                $endsAt = null;
                if ($plan) {
                    $endsAt = $plan->billing_interval === 'year'
                        ? now()->addYear()
                        : now()->addMonth();
                }

                Subscription::updateOrCreate([
                    'user_id' => $userId,
                ], [
                    'plan_id' => $planId,
                    'status'               => 'active',
                    'starts_at'            => now(),
                    'ends_at'              => $endsAt,
                    'amount_paid'          => isset($session->amount_total) ? $session->amount_total / 100 : null,
                    'payment_method'       => 'stripe',
                    'transaction_id'       => $session->payment_intent ?? null,
                ]);
            }
        }

        return response('Webhook handled', 200);
    }
}
