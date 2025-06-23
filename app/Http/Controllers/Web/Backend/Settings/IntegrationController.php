<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class IntegrationController extends Controller {
    /**
     * Display integration settings page.
     *
     * @return View
     */
    public function index(): View {
        return view('backend.layouts.settings.integration_settings');
    }

    /**
     * Update google credentials settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateGoogleCredentials(Request $request): RedirectResponse {
        $request->validate([
            'GOOGLE_CLIENT_ID'     => 'nullable|string',
            'GOOGLE_CLIENT_SECRET' => 'nullable|string',
        ]);
        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/GOOGLE_CLIENT_ID=(.*)\s/',
                '/GOOGLE_CLIENT_SECRET=(.*)\s/',
            ], [
                'GOOGLE_CLIENT_ID=' . $request->GOOGLE_CLIENT_ID . $lineBreak,
                'GOOGLE_CLIENT_SECRET=' . $request->GOOGLE_CLIENT_SECRET . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return redirect()->back()->with('t-success', 'Google Setting Update Successfully.');
        } catch (Exception) {
            return redirect()->back()->with('t-error', 'Google Setting Update Failed');
        }
    }

    /**
     * Update facebook credentials settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFacebookCredentials(Request $request): RedirectResponse {
        $request->validate([
            'FACEBOOK_CLIENT_ID'     => 'nullable|string',
            'FACEBOOK_CLIENT_SECRET' => 'nullable|string',
        ]);
        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/FACEBOOK_CLIENT_ID=(.*)\s/',
                '/FACEBOOK_CLIENT_SECRET=(.*)\s/',
            ], [
                'FACEBOOK_CLIENT_ID=' . $request->FACEBOOK_CLIENT_ID . $lineBreak,
                'FACEBOOK_CLIENT_SECRET=' . $request->FACEBOOK_CLIENT_SECRET . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return redirect()->back()->with('t-success', 'FACEBOOK Setting Update Successfully.');
        } catch (Exception) {
            return redirect()->back()->with('t-error', 'FACEBOOK Setting Update Failed');
        }
    }

    /**
     * Update apple credentials settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAppleCredentials(Request $request): RedirectResponse {
        $request->validate([
            'APPLE_CLIENT_ID' => 'required|string',
            'APPLE_TEAM_ID'   => 'required|string',
            'APPLE_KEY_ID'    => 'required|string',
        ]);
        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/APPLE_CLIENT_ID=(.*)\s/',
                '/APPLE_TEAM_ID=(.*)\s/',
                '/APPLE_KEY_ID=(.*)\s/',
            ], [
                'APPLE_CLIENT_ID=' . $request->APPLE_CLIENT_ID . $lineBreak,
                'APPLE_TEAM_ID=' . $request->APPLE_TEAM_ID . $lineBreak,
                'APPLE_KEY_ID=' . $request->APPLE_KEY_ID . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return redirect()->back()->with('t-success', 'Apple Setting Update Successfully.');
        } catch (Exception) {
            return redirect()->back()->with('t-error', 'Apple Setting Update Failed');
        }
    }

    /**
     * Update twilio credentials settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateTwilioCredentials(Request $request): RedirectResponse {
        $request->validate([
            'TWILIO_SID'          => 'required|string',
            'TWILIO_AUTH_TOKEN'   => 'required|string',
            'TWILIO_PHONE_NUMBER' => 'required|string',
        ]);
        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/TWILIO_SID=(.*)\s/',
                '/TWILIO_AUTH_TOKEN=(.*)\s/',
                '/TWILIO_PHONE_NUMBER=(.*)\s/',
            ], [
                'TWILIO_SID=' . $request->TWILIO_SID . $lineBreak,
                'TWILIO_AUTH_TOKEN=' . $request->TWILIO_AUTH_TOKEN . $lineBreak,
                'TWILIO_PHONE_NUMBER=' . $request->TWILIO_PHONE_NUMBER . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return redirect()->back()->with('t-success', 'Twilio Setting Update Successfully.');
        } catch (Exception) {
            return redirect()->back()->with('t-error', 'Twilio Setting Update Failed');
        }
    }

    /**
     * Update stripe credentials settings.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateStripeCredentials(Request $request): RedirectResponse {
        $request->validate([
            'STRIPE_KEY'    => 'nullable|string',
            'STRIPE_SECRET' => 'nullable|string',
        ]);
        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";
            $envContent = preg_replace([
                '/STRIPE_KEY=(.*)\s/',
                '/STRIPE_SECRET=(.*)\s/',
            ], [
                'STRIPE_KEY=' . $request->STRIPE_KEY . $lineBreak,
                'STRIPE_SECRET=' . $request->STRIPE_SECRET . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return redirect()->back()->with('t-success', 'Stripe Setting Update successfully.');
        } catch (Exception) {
            return redirect()->back()->with('t-error', 'Stripe Setting Update Failed');
        }
    }
}
