<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\OTPRequest;
use App\Http\Requests\Api\Auth\OTPVerificationRequest;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use App\Services\Api\Auth\PasswordResetService;
use Exception;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller {
    private PasswordResetService $passwordResetService;
    private Helper $helper;

    public function __construct(PasswordResetService $passwordResetService, Helper $helper) {
        $this->passwordResetService = $passwordResetService;
        $this->helper               = $helper;
    }

    /**
     * Send OTP code to the user's email.
     */
    public function sendOtpToEmail(OTPRequest $request): JsonResponse {
        try {
            $email    = $request->input('email');
            $response = $this->passwordResetService->sendOtpToEmail($email);

            return $this->helper->jsonResponse(true, $response['message'], 200, ['otp' => $response['otp']]);
        } catch (Exception $e) {
            return $this->helper->jsonResponse(false, $e->getMessage(), 400);
        }
    }

    /**
     * Verify the provided OTP code.
     */
    public function verifyOTP(OTPVerificationRequest $request): JsonResponse {
        try {
            $email    = $request->header('email') ?: $request->input('email');
            $otp      = $request->input('otp');
            $response = $this->passwordResetService->verifyOtp([
                'email' => $email,
                'otp'   => $otp,
            ]);

            return $this->helper->jsonResponse(true, $response['message'], 200);
        } catch (Exception $e) {
            return $this->helper->jsonResponse(false, $e->getMessage(), 400);
        }
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(PasswordResetRequest $request): JsonResponse {
        try {
            $email    = $request->header('email') ?: $request->input('email');
            $password = $request->input('password');
            $response = $this->passwordResetService->resetPassword([
                'email'    => $email,
                'password' => $password,
            ]);

            return $this->helper->jsonResponse(true, $response['message'], 200);
        } catch (Exception $e) {
            return $this->helper->jsonResponse(false, $e->getMessage(), 400);
        }
    }
}
