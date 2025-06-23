<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\Api\Auth\LogoutService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class LogoutController extends Controller {
    private LogoutService $logoutService;
    private Helper $helper;

    public function __construct(LogoutService $logoutService, Helper $helper) {
        $this->logoutService = $logoutService;
        $this->helper        = $helper;
    }

    /**
     * Handle user logout.
     */
    public function logout(): JsonResponse {
        try {
            $this->logoutService->logout();
            return $this->helper->jsonResponse(true, 'Logged out successfully.', 200);
        } catch (UnauthorizedHttpException $e) {
            return $this->helper->jsonResponse(false, 'The token has been blacklisted or is invalid.', 401);
        } catch (Exception $e) {
            return $this->helper->jsonResponse(false, 'An error occurred during logout.', 500, $e->getMessage());
        }
    }
}
