<?php

namespace App\Http\Controllers\Api\CMS;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function ServiceList(Request $request)
    {
        try {
            $service = Service::where('status', 'active')->get();

            if ($service->isEmpty()) {
                return response()->json(['status' => true, 'message' => 'No Support found ', 'data' => [],], 200);
            }
            //makehidden for hide the fields
            $service->makeHidden(['created_at', 'updated_at', 'deleted_at', 'status']);
            return response()->json([
                'status' => true,
                'message' => 'Support fetched successfully',
                'data' => $service,
            ]);
        } catch (Exception $e) {
            return Helper::jsonResponse(true, 'Failed :', 500, [$e->getMessage(),]);
        }
    }
}
