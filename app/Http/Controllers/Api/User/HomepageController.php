<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{

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



    //upcomming taski 
    public function upcomingTask()
    {
        try {
            $userId = Auth::id();
            $today = now()->toDateString();

            $meetings = Task::where('user_id', $userId)->where('date', '>=', $today)->orderBy('date', 'asc')->get();

            return Helper::jsonResponse(true, 'Upcoming meetings fetched Successfully .', 200, $meetings);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
