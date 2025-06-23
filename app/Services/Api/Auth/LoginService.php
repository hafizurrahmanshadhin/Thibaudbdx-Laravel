<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginService {
    /**
     * Handle the login process.
     */
    public function login(array $data): array {
        try {
            $token = JWTAuth::attempt([
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            if (!$token) {
                throw new Exception('Unauthorized');
            }

            $user = auth()->user();

            return [
                'user'  => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}
