<?php

namespace App\Services\Api\Auth;

use App\Mail\OTPMail;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetService {
    /**
     * Send an OTP to the provided email address.
     */
    public function sendOtpToEmail(string $email): array {
        try {
            $user = User::where('email', $email)->first();
            if (!$user) {
                throw new Exception('Invalid Email Address');
            }

            $otp = rand(1000, 9999);
            Mail::to($email)->send(new OTPMail($otp));

            PasswordReset::updateOrCreate(
                ['email' => $email],
                ['otp' => $otp, 'created_at' => Carbon::now()]
            );

            return [
                'message' => 'OTP Code Sent Successfully',
                'otp'     => $otp,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify the provided OTP code.
     */
    public function verifyOtp(array $data): array {
        try {
            $passwordReset = PasswordReset::where('email', $data['email'])
                ->where('otp', $data['otp'])
                ->where('created_at', '>=', Carbon::now()->subMinutes(15))
                ->first();

            if (!$passwordReset) {
                throw new Exception('Invalid OTP Code');
            }

            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                throw new Exception('User not found');
            }

            $user->update(['otp_verified_at' => Carbon::now()]);
            $passwordReset->delete();

            return ['message' => 'OTP Verified Successfully'];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(array $data): array {
        try {
            $user = User::where('email', $data['email'])->first();
            if (!$user) {
                throw new Exception('Invalid Email Address');
            }

            if (is_null($user->otp_verified_at)) {
                throw new Exception('OTP not verified');
            }

            $user->update([
                'password'        => Hash::make($data['password']),
                'otp_verified_at' => null,
            ]);

            return ['message' => 'Password Reset Successfully'];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
