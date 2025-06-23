<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PageController extends Controller {
    /**
     * Display the privacy policy page.
     *
     * @param $type
     * @return JsonResponse|View
     */
    public function dynamicPage($type): JsonResponse | View {
        try {
            $content       = Content::where('type', $type)->where('status', 'active')->first();
            $systemSetting = SystemSetting::first();

            return view('pages.dynamic-content', compact('content', 'systemSetting'));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
