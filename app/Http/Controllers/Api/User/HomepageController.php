<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\Tasting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{
    /**
     * upcomming meeting api endpoint 
     */
    public function upcomingMeetings()
    {
        try {
            $userId = Auth::id();
            $today = now()->toDateString();

            $meetings = Meeting::where('user_id', $userId)->where('date', '>=', $today)->orderBy('date', 'asc')->get();

            return Helper::jsonResponse(true, 'Upcoming meetings fetched Successfully .', 200, $meetings);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }



    /**
     * upcomming task api endpoint 
     */
    public function upcomingTask()
    {
        try {
            $userId = Auth::id();
            $today = now()->toDateString();

            $task = Task::where('user_id', $userId)->where('date', '>=', $today)->orderBy('date', 'asc')->get();

            return Helper::jsonResponse(true, 'Upcoming task fetched Successfully .', 200, $task);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * upcoming tasting list api
     */
    public function upcomingTasting()
    {
        try {
            $userId = Auth::id();
            $today = now()->toDateString();
            $tastings = Tasting::where('user_id', $userId)
                ->whereDate('date', '>=', $today)
                ->orderBy('date', 'asc')
                ->get();
            return Helper::jsonResponse(true, 'Upcoming Tastings fetched successfully.', 200, $tastings);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
