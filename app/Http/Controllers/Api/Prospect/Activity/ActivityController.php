<?php

namespace App\Http\Controllers\Api\Prospect\Activity;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\Tasting;
use App\Models\Voice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    /**
     * Activity list for a specific 
     */
    public function ActivityList(Request $request)
    {
        try {
            $customerId = $request->query('customer_id');

            $filterCustomer = fn($q) => $q->where('contact_type', 'prospect')->where('user_id', Auth::id());
            // Meetings
            $meetings = Meeting::where('customer_id', $customerId)->where('status', 'active')
                ->with('customer:id,contact_type,user_id')
                ->whereHas('customer', $filterCustomer)
                ->get()
                ->map(function ($item) {
                    $item->type = 'meeting';
                    return $item;
                });

            // Tasks
            $tasks = Task::where('customer_id', $customerId)->where('status', 'active')
                ->with('customer:id,contact_type,user_id')
                ->whereHas('customer', $filterCustomer)
                ->get()
                ->makeHidden(['customer'])
                ->map(function ($item) {
                    $item->type = 'task';
                    return $item;
                });

            // Voices
            $voices = Voice::with('customer:id,contact_type,user_id')->where('status', 'active')
                ->where('customer_id', $customerId)
                ->get()
                ->map(function ($item) {
                    $item->type = 'voice';
                    return $item;
                });

            // Tastings
            $tastings = Tasting::where('customer_id', $customerId)->where('status', 'active')
                ->with('customer:id,contact_type,user_id')
                ->whereHas('customer', $filterCustomer)
                ->get()
                ->map(function ($item) {
                    $item->type = 'tasting';
                    return $item;
                });

            $data = collect()->merge($meetings)->merge($tasks)->merge($voices)->merge($tastings)->sortByDesc('created_at')->values();

            return Helper::jsonResponse(true, 'Activities retrieved successfully', 200, $data);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
