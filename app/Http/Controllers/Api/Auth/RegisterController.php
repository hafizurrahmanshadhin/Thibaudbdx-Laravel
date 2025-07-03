<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\Api\Auth\RegisterResource;
use App\Mail\OTPMail;
use App\Models\User;
use App\Services\Api\Auth\RegisterService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;

class RegisterController extends Controller
{
    private RegisterService $registerService;
    private Helper $helper;

    public function __construct(RegisterService $registerService, Helper $helper)
    {
        $this->registerService = $registerService;
        $this->helper          = $helper;
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data   = $request->validated();
            $result = $this->registerService->register($data);

            // return response()->json([
            //     'status'     => true,
            //     'message'    => 'User registered successfully.',
            //     'code'       => 201,
            //     'token_type' => 'bearer',
            //     'token'      => $result['token'],
            //     'data'       => new RegisterResource($result['user']),
            // ], 201);
            return response()->json([
                'status'     => true,
                'message'    => 'User registered successfully.',
                'code'       => 201,
                'token_type' => 'bearer',
                'token'      => $result['token'],
                'data'       => [
                    'name'  => $result['user']->name,
                    'email' => $result['user']->email,
                ],
            ], 201);
        } catch (JWTException $e) {
            Log::error('JWT Error: ' . $e->getMessage());
            return $this->helper->jsonResponse(false, 'JWT error occurred during registration.', 500, ['error' => $e->getMessage()]);
        } catch (ModelNotFoundException $e) {
            Log::error('Model not found: ' . $e->getMessage());
            return $this->helper->jsonResponse(false, 'User model not found.', 404, ['error' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return $this->helper->jsonResponse(false, 'An error occurred during registration.', 500, ['error' => $e->getMessage()]);
        }
    }

    //verify Mail
    public function VerifyEmail(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4',
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();
            if ($user->email_verified_at !== null) {
                return $this->helper->jsonResponse(true, 'Your email has already been verified. Please login to continue.', 200);
            }

            if ($user->otp !== $request->input('otp')) {
                return $this->helper->jsonResponse(false, 'Invalid OTP. Please try again.', 422);
            }
            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return $this->helper->jsonResponse(false, 'OTP has expired. Please request a new OTP.', 422);
            }

            //Verify the email
            $user->email_verified_at = now();
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            $token = auth('api')->login($user);
            return $this->helper->jsonResponse(true, 'Email verified successfully.Please login to continue.', 200);
        } catch (Exception $e) {
            Log::error('RegisterController::VerifyEmail' . $e->getMessage());
            return $this->helper->jsonResponse(false, 'An error occurred during email verification.', 403, ['error' => $e->getMessage()]);
        }
    }

    // resent otp
    public function ResendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        try {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return $this->helper->jsonResponse(false, 'User not found !', 404);
            }

            if ($user->email_verified_at) {
                return $this->helper->jsonResponse(true, 'Email already verified .Login Here ?', 200);
            }

            // $newOtp = rand(1000, 9999);
            $newOtp = "1234";
            $otpExpiresAt = Carbon::now()->addMinutes(60);
            $user->otp = $newOtp;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();

            try {
                // Mail::to($user->email)->send(new OTPMail($newOtp));
                return $this->helper->jsonResponse(true, 'A new OTP has been sent to your email.', 200);
            } catch (Exception $e) {
                return $this->helper->jsonResponse(false, 'Something worng', 500, ['error' => $e->getMessage()]);
            }
        } catch (Exception $e) {
            Log::error('RegisterController::ResendOtp' . $e->getMessage());
            return $this->helper->jsonResponse(false, 'Something worng', 500, ['error' => $e->getMessage()]);
        }
    }
}
