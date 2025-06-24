<?php

namespace App\Http\Controllers\Api\Client\Task;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    // -- task list api
    public function index(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $per_page = $request->query('per_page', 50);
            $customerId = $request->query('customer_id');

            $task = Task::with('customer:id,contact_type,user_id')
                ->whereHas('customer', function ($q) {
                    $q->where('contact_type', 'customer')->where('user_id', Auth::user()->id);
                })->where('customer_id', $customerId)

                ->when(!empty(trim($search)), function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->select('id', 'name', 'description', 'date', 'created_at')
                ->paginate($per_page);

            if (!empty($search) && $task->isEmpty()) {
                return Helper::jsonResponse(false, 'No task found for the given search.', 404);
            }

            if ($task->isEmpty()) {
                return Helper::jsonResponse(true, 'Tasks Data Empty ', 200, [
                    'tasks' => [],
                ]);
            }

            return Helper::jsonResponse(true, 'Task list retrieved successfully.', 200, [
                'tasks' => $task->items(),
                'pagination' => [
                    'current_page' => $task->currentPage(),
                    'last_page' => $task->lastPage(),
                    'total' => $task->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    //task create ---
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:200',
                'customer_id' => 'required|exists:customers,id',
                'description' => 'required|string|max:600',
                'date' => 'required|date|after_or_equal:today'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();

            // Check customer contact_type prospect
            $customer = Customer::where('id', $data['customer_id'])
                ->where('contact_type', 'customer')->first();

            if (!$customer) {
                return Helper::jsonResponse(false, 'Invalid Customer or Not Allowed Type Client', 403);
            }

            $task = Task::create($data);

            return Helper::jsonResponse(true, 'Task Created Successfully!', 200, [
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Task Create Failed!', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    //task details --
    public function details(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');

            $task = Task::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($query) {
                    $query->where('contact_type', 'customer')->where('user_id', Auth::user()->id);
                })->first();

            if (!$task) {
                return Helper::jsonResponse(false, 'Task Not Found .', 404);
            }

            return Helper::jsonResponse(true, 'Task Details Retrieved Successfully!', 200, $task);
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
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $userId = Auth::id();

            //Task validation
            $task = Task::where('id', $id)->where('customer_id', $data['customer_id'])
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

            if (!$task) {
                return Helper::jsonResponse(false, 'Task not found .', 404);
            }

            $task->update($data);

            return Helper::jsonResponse(true, 'task updated successfully.', 200, $task);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error :', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }


    //--delete task
    public function destroy(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');
            $userId = Auth::id();

            $task = Task::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

            if (!$task) {
                return Helper::jsonResponse(false, 'Task Not Found ? or access denied.', 404);
            }

            $task->delete();

            return Helper::jsonResponse(true, 'Task deleted successfully.', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
