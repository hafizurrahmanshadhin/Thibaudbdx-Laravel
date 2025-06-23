<?php

namespace App\Services\Api\Auth;

use App\Mail\OTPMail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterService
{
    /**
     * Handle the registration process.
     */
    public function register(array $data): array
    {
        $existingUser = User::where('email', $data['email'])->exists();
        if ($existingUser) {
            throw new Exception('The email has already been taken.');
        }

        try {
            // DB::beginTransaction();
            // $otp = rand(1000, 9999);
            $otp = "1234";
            $otpExpiresAt = Carbon::now()->addMinutes(60); // 1 hour
            $user = User::create([
                // 'first_name' => $data['first_name'],
                // 'last_name'  => $data['last_name'],
                'name' => $data['name'],
                'email'      => $data['email'],
                'password'   => bcrypt($data['password']),
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
            ]);

            //sending otp mail address
            Mail::to($user->email)->send(new OTPMail($otp));
        } catch (Exception $e) {
            throw new Exception('User registration failed: ' . $e->getMessage());
        }

        try {
            $token = JWTAuth::attempt(['email' => $data['email'], 'password' => $data['password']]);
            if (!$token) {
                throw new Exception('Authentication failed.');
            }
        } catch (JWTException $e) {
            throw new Exception('Could not create token: ' . $e->getMessage());
        }

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}
