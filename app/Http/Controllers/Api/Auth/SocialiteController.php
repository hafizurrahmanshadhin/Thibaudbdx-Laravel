<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\Api\Auth\SocialiteService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SocialiteController extends Controller
{
    protected SocialiteService $socialiteService;
    private Helper $helper;

    public function __construct(SocialiteService $socialiteService, Helper $helper)
    {
        $this->socialiteService = $socialiteService;
        $this->helper           = $helper;
    }

    /**
     * Handle socialite login.
     */
    public function socialiteLogin(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'provider' => 'required|string|in:google,facebook,apple',
        ]);

        try {
            $token    = $request->input('token');
            $provider = $request->input('provider');

            $response = $this->socialiteService->loginWithSocialite($provider, $token);

            // Extract parts from the response array
            return response()->json([
                'status'     => true,
                'message'    => $response['message'],
                'code'       => $response['code'],
                'token_type' => $response['token_type'],
                'token'      => $response['token'],
                'data'       => $response['data'],
            ], $response['code']);
        } catch (UnauthorizedHttpException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized',
                'code'    => 401,
                'data'    => null,
                'error'   => $e->getMessage(),
            ], 401);
        } catch (Exception $e) {
            Log::error('Socialite Login Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'code'    => 500,
                'data'    => null,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
