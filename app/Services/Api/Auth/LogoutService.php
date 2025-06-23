<?php

namespace App\Services\Api\Auth;

use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutService {
    /**
     * Invalidate the JWT token (logout).
     */
    public function logout(): bool {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return true;
        } catch (UnauthorizedHttpException $e) {
            throw new UnauthorizedHttpException('Token is invalid or already blacklisted.');
        } catch (Exception $e) {
            throw new Exception('An error occurred during logout: ' . $e->getMessage());
        }
    }
}
