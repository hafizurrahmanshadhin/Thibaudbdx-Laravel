<?php

namespace App\Http\Controllers\Api\Client\Activity;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\Tasting;
use App\Models\Voice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function ActivityList(Request $request)
    {
        try {
            $customerId = $request->query('customer_id');

            $filterCustomer = fn($q) => $q->where('contact_type', 'customer')->where('user_id', Auth::id());

            $meetings = Meeting::where('customer_id', $customerId)->where('status', 'active')
                ->with('customer:id,contact_type,user_id')
                ->whereHas('customer', $filterCustomer)
                ->get();

            $tasks = Task::where('customer_id', $customerId)->where('status', 'active')
                ->with('customer:id,contact_type,user_id')
                ->whereHas('customer', $filterCustomer)
                ->get()->makeHidden(['customer']);

            $voices = Voice::with('customer:id,contact_type,user_id')->where('status', 'active')
                ->whereHas('customer', function ($q) {
                    $q->where('contact_type', 'customer')->where('user_id', Auth::user()->id);
                })->where('customer_id', $customerId)->get();

            $tastings = Tasting::where('customer_id', $customerId)->where('status', 'active')
                ->with('customer:id,contact_type,user_id')
                ->whereHas('customer', $filterCustomer)
                ->get();

            if ($meetings->isEmpty() && $tasks->isEmpty() && $voices->isEmpty() && $tastings->isEmpty()) {
                return Helper::jsonResponse(false, 'No activities Found .', 404);
            }

            return Helper::jsonResponse(true, 'Activities  retrieved successfully', 200, [
                'meetings' => $meetings,
                'tasks' => $tasks,
                'voices' => $voices,
                'tastings' => $tastings,
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
