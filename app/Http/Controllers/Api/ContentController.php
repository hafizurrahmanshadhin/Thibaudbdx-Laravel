<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContentRequest;
use App\Http\Resources\Api\ContentResource;
use App\Models\Content;
use Exception;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller {
    /**
     * Display the specified resource.
     *
     * @param ContentRequest $request
     * @return JsonResponse
     */
    public function index(ContentRequest $request): JsonResponse {
        try {
            $validated = $request->validated();

            $content = Content::where('type', $validated['type'])->where('status', 'active')->first();

            if (!$content) {
                return Helper::jsonResponse(false, "No active content found for type: {$validated['type']}", 404);
            }

            return Helper::jsonResponse(true, 'Content retrieved successfully.', 200, new ContentResource($content));
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'An error occurred', 500, ['error' => $e->getMessage()]);
        }
    }
}
