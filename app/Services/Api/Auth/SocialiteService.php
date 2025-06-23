<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SocialiteService {
    /**
     * Handle socialite login.
     */
    public function loginWithSocialite(string $provider, string $token): array {
        if (!in_array($provider, ['google', 'facebook', 'apple'])) {
            throw new UnauthorizedHttpException('', 'Provider not supported');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($token);
        } catch (Exception $e) {
            Log::error('Apple login error: ' . $e->getMessage());
            throw new UnauthorizedHttpException('', 'Invalid token or provider');
        }

        if (!$socialUser || !$socialUser->getEmail()) {
            throw new UnauthorizedHttpException('', 'Invalid social user data');
        }

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'                 => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Unknown',
                'password'             => Hash::make(Str::random(16)),
                'email_verified_at'    => now(),
                'terms_and_conditions' => true,
            ]
        );

        $isNewUser = $user->wasRecentlyCreated;
        $token     = $user->createToken('auth_token')->plainTextToken;

        return [
            'status'     => true,
            'message'    => $isNewUser ? 'User registered successfully' : 'User logged in successfully',
            'code'       => 200,
            'token_type' => 'bearer',
            'token'      => $token,
            'data'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ];
    }
}
