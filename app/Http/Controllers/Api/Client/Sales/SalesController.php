<?php

namespace App\Http\Controllers\Api\Client\Sales;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $per_page = $request->query('per_page', 50);
            $customerId = $request->query('customer_id');
            $userId = Auth::user()->id;

            $sale = Sale::where('user_id', $userId)->where('customer_id', $customerId)
                ->whereHas('customer', function ($q) {
                    $q->where('contact_type', 'customer');
                })->paginate($per_page);


            if ($sale->isEmpty()) {
                return Helper::jsonResponse(true, 'Sales Data Empty ', 200, [
                    'sales' => [],
                ]);
            }

            return Helper::jsonResponse(true, 'Sales list retrieved successfully.', 200, [
                'sales' => $sale->items(),
                'pagination' => [
                    'current_page' => $sale->currentPage(),
                    'last_page' => $sale->lastPage(),
                    'total' => $sale->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }



    //create sales api
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'date' => 'required|date',
                'price' => 'required|numeric|min:0',
                'status' => 'nullable|in:pending,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();
            $data['status'] = $data['status'] ?? 'completed';


            $customer = Customer::where('id', $data['customer_id'])
                ->where('contact_type', 'customer')
                ->first();

            if (!$customer) {
                return Helper::jsonResponse(false, 'Only customers can be converted to sales!', 403);
            }

            $sales = Sale::create($data);
            return Helper::jsonResponse(true, 'Sales Created Successfully!', 201, [
                'data' => $sales
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Sales Create Failed!', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    //sales details --
    public function details(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');
            $userId = Auth::id();
            $salse = Sale::where('user_id', $userId)->where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($query) {
                    $query->where('contact_type', 'customer')->where('user_id', Auth::user()->id);
                })->first();

            if (!$salse) {
                return Helper::jsonResponse(false, 'Salse Not Found .', 404);
            }

            return Helper::jsonResponse(true, 'Salse Details Retrieved Successfully!', 200, $salse);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed :', 500, [$e->getMessage()]);
        }
    }


    //update functon
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'nullable|exists:customers,id',
                'date' => 'nullable|date',
                'price' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:pending,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $userId = Auth::id();

            //Sale validation
            $sale = Sale::where('id', $id)->where('customer_id', $data['customer_id'])
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

            if (!$sale) {
                return Helper::jsonResponse(false, 'Sale not found .', 404);
            }

            $sale->update($data);

            return Helper::jsonResponse(true, 'Sale updated successfully.', 200, $sale);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error :', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }


    //--delete sales
    public function destroy(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');
            $userId = Auth::id();

            $sale = Sale::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

            if (!$sale) {
                return Helper::jsonResponse(false, 'Sale Not Found !', 404);
            }

            $sale->delete();

            return Helper::jsonResponse(true, 'Sale deleted successfully.', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
