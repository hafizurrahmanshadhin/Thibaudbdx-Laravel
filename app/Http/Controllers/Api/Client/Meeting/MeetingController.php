<?php

namespace App\Http\Controllers\Api\Client\Meeting;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MeetingController extends Controller
{
    // -- meeting list api
    public function index(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $per_page = $request->query('per_page', 50);
            $customerId = $request->query('customer_id');

            $meeting = Meeting::with('customer:id,contact_type,user_id')
                ->whereHas('customer', function ($q) {
                    $q->where('contact_type', 'customer')->where('user_id', Auth::user()->id);
                })->where('customer_id', $customerId)

                ->when(!empty(trim($search)), function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->select('id', 'name', 'customer_id', 'reminder_time', 'reminder', 'description', 'date', 'time')
                ->paginate($per_page);

            if (!empty($search) && $meeting->isEmpty()) {
                return Helper::jsonResponse(false, 'No meeting found for the given search.', 404);
            }

            if ($meeting->isEmpty()) {
                return Helper::jsonResponse(true, 'meetings Data Empty ', 200, [
                    'meetings' => [],
                ]);
            }
            // return Helper::jsonResponse(true, 'Meeting list retrieved successfully.', 200, $meeting);
            return Helper::jsonResponse(true, 'Meeting list retrieved successfully.', 200, [
                'meetings' => $meeting->items(),
                'pagination' => [
                    'current_page' => $meeting->currentPage(),
                    'last_page' => $meeting->lastPage(),
                    'total' => $meeting->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    //--- create meeting
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'customer_id' => 'required|exists:customers,id',
                'description' => 'required|string|max:500',
                'date' => 'required|date|after_or_equal:today',
                'time' => 'required',
                'reminder' => 'nullable|boolean',
                'reminder_time' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            // Check customer contact_type customer
            $customer = Customer::where('id', $data['customer_id'])
                ->where('contact_type', 'customer')->first();

            if (!$customer) {
                return Helper::jsonResponse(false, 'Invalid Customer or Not Allowed Type Client', 403);
            }

            $meeting = Meeting::create($data);

            //Reminder scheduling
            if ($meeting->reminder) {
                $this->scheduleReminder($meeting);
            }

            return Helper::jsonResponse(true, 'Meeting Created Successfully!', 200, [
                'data' => $meeting
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Meeting Create Failed!', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    //remainder time 
    public function scheduleReminder(Meeting $meeting)
    {
        $dateOnly = Carbon::parse($meeting->date)->format('Y-m-d');
        $meetingDateTime = Carbon::parse($dateOnly . ' ' . $meeting->time);  // date + time 

        $remindAt = $meetingDateTime->copy()->subMinutes($meeting->reminder_time);
        Log::info("Reminder scheduled for meeting: {$meeting->name} at {$remindAt}");
    }




    public function details(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');
            $meeting = Meeting::where([['id', '=', $id], ['customer_id', '=', $customerId], ['status', '=', 'active'],])->whereHas('customer', function ($query) {
                $query->where('contact_type', 'customer')
                    ->where('user_id', Auth::id());
            })->first();

            if (!$meeting) {
                return Helper::jsonResponse(false, 'Meeting Not Found .', 404);
            }

            return Helper::jsonResponse(true, 'Meeting Details Retrieved Successfully!', 200, $meeting);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed:', 500, [$e->getMessage()]);
        }
    }



    //update functon
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'customer_id' => 'nullable|exists:customers,id',
                'description' => 'nullable|string|max:500',
                'date' => 'nullable|date|after_or_equal:today',
                'time' => 'nullable',
                'reminder' => 'nullable|boolean',
                'reminder_time' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $userId = Auth::id();

            //Meeting validation
            $meeting = Meeting::where('id', $id)->where('customer_id', $data['customer_id'])
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

            if (!$meeting) {
                return Helper::jsonResponse(false, 'Meeting not found .', 404);
            }

            //Update the meeting
            $meeting->update($data);

            //reminder
            if ($meeting->reminder) {
                $this->scheduleReminder($meeting);
            }

            return Helper::jsonResponse(true, 'Meeting updated successfully.', 200, $meeting);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');
            $userId = Auth::id();

            $meeting = Meeting::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

            if (!$meeting) {
                return Helper::jsonResponse(false, 'Meeting Failed ? or access denied.', 404);
            }

            $meeting->delete();

            return Helper::jsonResponse(true, 'Meeting deleted successfully.', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
