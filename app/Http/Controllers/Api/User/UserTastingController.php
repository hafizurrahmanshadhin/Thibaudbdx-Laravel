<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Tasting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserTastingController extends Controller
{
    //list api 
    public function tastingList(Request $request)
    {
        try {
            $per_page = $request->query('per_page', 50);
            $userId = Auth::user()->id;
            $Tasting =  Tasting::where('user_id', $userId)->whereNull('customer_id')->paginate($per_page);

            if (!$Tasting) {
                return Helper::jsonResponse(false, 'Not Found!', 404);
            }

            return Helper::jsonResponse(true, 'Task list retrieved successfully.', 200, [
                'Tasting' => $Tasting->items(),
                'pagination' => [
                    'current_page' => $Tasting->currentPage(),
                    'per_page' => $Tasting->perPage(),
                    'total' => $Tasting->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Error occurred:', 500, [$e->getMessage()]);
        }
    }

    //---Testing create
    public function tastingCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tastings,name',
            'product_id' => 'required|array',
            'description' => 'required|string|max:600',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();



        $customer = Tasting::create($data);
        $productId = $data['product_id'];
        $Product = Product::whereIn('id', $productId)->get(['id', 'wine_name']);

        return Helper::jsonResponse(true, 'Tasting Created Successfully !', 201, [
            'customer' => $customer,
            ' Product' =>  $Product,
        ]);
    }

    //--tasting-details
    public function tastingdetails(Request $request, $id)
    {
        try {
            $userId = Auth::user()->id;
            $tasting = Tasting::where('id', $id)->where('user_id', $userId)->whereNull('customer_id')->first();

            if (!$tasting) {
                return Helper::jsonResponse(false, 'Tasting Not Found!', 404);
            }

            // Safely decode product_id
            $productIds = is_array($tasting->product_id) ? $tasting->product_id : json_decode($tasting->product_id ?? '[]', true);

            $products = Product::whereIn('id', $productIds)->get(['id', 'wine_name']);

            return Helper::jsonResponse(true, 'Tasting Details Retrieved Successfully!', 200, [
                'tasting' => $tasting,
                'Product List' => $products,
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Error occurred:', 500, [$e->getMessage()]);
        }
    }

    // User Tasting update 
    public function tastingUpdate(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:200',
                'description' => 'nullable|string|max:500',
                'product_id' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $userId = Auth::id();

            //Tasting validation
            $tasting = Tasting::where('id', $id)->where('user_id', $userId)->whereNull('customer_id')->first();

            if (!$tasting) {
                return Helper::jsonResponse(false, 'Tasting not found.', 404);
            }

            $tasting->update($data);
            $productIds = $data['product_id'];

            $products = Product::whereIn('id', $productIds)->get(['id', 'wine_name']);
            return Helper::jsonResponse(true, 'Tasting updated successfully.', 200, [
                'Tasting' => $tasting,
                'product_Name' => $products,
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error:', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    //--User Tasting delete
    public function tastingDestroy(Request $request, $id)
    {
        try {
            $userId = Auth::id();

            $tasting = Tasting::where('id', $id)->where('user_id', $userId)->whereNull('customer_id')->first();

            if (!$tasting) {
                return Helper::jsonResponse(false, 'Tasting Not Found ?', 404);
            }

            $tasting->delete();
            return Helper::jsonResponse(true, 'Tasting deleted successfully.', 200, $tasting);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
