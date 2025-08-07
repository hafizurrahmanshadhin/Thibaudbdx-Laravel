<?php

namespace App\Http\Controllers\Api\Customer;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class CustomerController extends Controller
{
    /**
     * customer list api
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $perPage = $request->query('per_page', 50);
            $userId = Auth::id();

            // Start query builder
            $query = Customer::where('user_id', $userId)->where('contact_type', 'prospect');

            if (!empty(trim($search))) {
                $query->where(function ($q) use ($search) {
                    $q->where('owner_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $customers = $query->paginate($perPage);


            if ($customers->isEmpty()) {
                return Helper::jsonResponse(false, 'No customers found.', 404);
            }
            //hidden filled
            $customers->makeHidden(['updated_at', 'created_at', 'tag_id']);

            // return Helper::jsonResponse(true, 'Customer list retrieved successfully.', 200, $customers);
            return Helper::jsonResponse(true, 'Customer list retrieved successfully.', 200, [
                'customers' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'last_page' => $customers->lastPage(),
                    'total' => $customers->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, ['error' => $e->getMessage()]);
        }
    }


    /**
     * customer create
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_type' => 'required|in:prospect,customer,inactive',
            'company_name' => 'required|string|max:200',
            'owner_name' => 'required|string|max:100',
            'address' => 'required|string|max:150',
            'city' => 'required|string|max:100',
            'zip_code' => 'required|string|max:25',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:customers,email',
            'website' => 'required|url|max:300',
            'tag_id' => 'required|array',
            'tag_id.*' => 'exists:tags,id',
            'description' => 'required|string|max:600',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();


        $customer = Customer::create($data);
        $tagIds = $data['tag_id'];
        $tags = Tag::whereIn('id', $tagIds)->get(['id', 'name', 'color']);

        return Helper::jsonResponse(true, 'Customer Created Successfully !', 201, [
            'customer' => $customer,
            ' Tags' =>  $tags,
            'TagCount' => count($tags),

        ]);
    }


    /**
     * customer-details
     */
    public function details($id)
    {
        try {
            $user = Auth::id();
            $customer = Customer::where('user_id', $user)->where('contact_type', 'prospect')->find($id);

            if (!$customer) {
                return Helper::jsonResponse(false, 'Customer Not Found!', 404);
            }
            $customer->makeHidden(['updated_at', 'created_at', 'latitude', 'longitude', 'tag_id']);

            //tag name and color show 
            $tagIdValue = $customer->tag_id ?? '[]';
            $tagIds = is_array($tagIdValue) ? $tagIdValue : json_decode($tagIdValue, true) ?? [];
            $tags = Tag::whereIn('id', $tagIds)->get(['id', 'name', 'color']);

            return Helper::jsonResponse(true, 'Customer Details Successfully!', 200, [
                'customer' => $customer,
                'tag' => $tags,
                'tagCount' => count($tags),
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed :', 500, [$e->getMessage()]);
        }
    }

    /**
     * customer update
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'contact_type' => 'nullable|in:prospect,customer,inactive',
                'company_name' => 'nullable|string|max:200',
                'owner_name' => 'nullable|string|max:150',
                'address' => 'nullable|string|max:150',
                'city' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:25',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:60',
                'website' => 'nullable|url',
                'description' => 'nullable|string|max:600',
                'longitude' => 'nullable|numeric',
                'latitude' => 'nullable|numeric',
                'tag_id' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();

            $customer = Customer::where('user_id', Auth::id())->find($id);
            if (!$customer) {
                return Helper::jsonResponse(false, 'Customer Not Found!', 404);
            }

            $customer->update($data);
            //tag get and show
            $tags = collect($customer->tag_id)->isNotEmpty() ? Tag::whereIn('id', $customer->tag_id)->get(['id', 'name', 'color']) : [];


            return Helper::jsonResponse(true, 'Customer Updated Successfully!', 200, [
                'customer' => $customer,
                'Tag' => $tags,
                'TagCount' => count($tags),
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed :', 500, [$e->getMessage()]);
        }
    }


    /**
     * Customer delete
     */
    public function destroy($id)
    {
        try {
            $userId = Auth::id();
            $customer = Customer::where('user_id', $userId)->where('contact_type', 'prospect')->find($id);

            if (!$customer) {
                return Helper::jsonResponse(false, 'Customer Not Found!', 404);
            }
            $customer->delete();

            return Helper::jsonResponse(true, 'Customer Deleted Successfully!', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed :', 500, [$e->getMessage()]);
        }
    }
}
