<?php

namespace App\Http\Controllers\Api\CMS;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Exception;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function FaqList(Request $request)
    {
        try {
            $faq = FAQ::all();

            if ($faq->isEmpty()) {
                return response()->json(['status' => true, 'message' => 'No FAQs found', 'data' => [],], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'FAQs fetched successfully',
                'data' => $faq,
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(true, 'Failed :', 500, [
                $e->getMessage(),
            ]);
        }
    }
}
