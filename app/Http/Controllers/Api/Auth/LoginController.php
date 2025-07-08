<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Api\Auth\LoginResource;
use App\Services\Api\Auth\LoginService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private LoginService $loginService;
    private Helper $helper;

    public function __construct(LoginService $loginService, Helper $helper)
    {
        $this->loginService = $loginService;
        $this->helper       = $helper;
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            Log::info('Login data:', $credentials);
            $result = $this->loginService->login($credentials);

            if (!empty($result['user']->otp_expires_at)) {
                return $this->helper->jsonResponse(false, 'A verification email has been successfully sent to your email address. Please check your inbox to complete the verification.', 403);
            }

            return response()->json([
                'status'     => true,
                'message'    => 'User logged in successfully.',
                'code'       => 200,
                'token_type' => 'bearer',
                'token'      => $result['token'],
                'data'       => new LoginResource($result['user']),
            ]);
        } catch (Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            if ($e->getMessage() === 'Unauthorized') {
                return $this->helper->jsonResponse(false, 'Unauthorized', 401, [
                    'error' => $e->getMessage(),
                ],401);
            }

            return $this->helper->jsonResponse(false, 'An error occurred during login.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
