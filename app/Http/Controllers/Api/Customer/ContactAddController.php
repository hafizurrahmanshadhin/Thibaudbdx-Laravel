<?php

namespace App\Http\Controllers\Api\Customer;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactAddController extends Controller
{
    /**
     * import Form Contact
     */
    public function importFormContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_type' => 'nullable|in:prospect,customer,inactive',
            'company_name' => 'nullable|string|max:200',
            'owner_name' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:25',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'website' => 'nullable|url|max:300',
            'tag_id' => 'nullable|array',
            'tag_id.*' => 'exists:tags,id',
            'description' => 'nullable|string|max:3000',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        $customer = Customer::create($data);

        $tagIds = $data['tag_id'] ?? [];
        $tags = Tag::whereIn('id', $tagIds)->get(['id', 'name', 'color']);

        return Helper::jsonResponse(true, 'Form contact imported successfully!', 201, [
            'customer' => $customer,
            'tags' => $tags,
            'tag_count' => count($tags),
        ]);
    }
    /**
     * import Form google 
     */
    public function importFormGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_type' => 'nullable|in:prospect,customer,inactive',
            'company_name' => 'nullable|string|max:200',
            'owner_name' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:25',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'website' => 'nullable|url|max:300',
            'tag_id' => 'nullable|array',
            'tag_id.*' => 'exists:tags,id',
            'description' => 'nullable|string|max:3000',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        $customer = Customer::create($data);

        $tagIds = $data['tag_id'] ?? [];
        $tags = Tag::whereIn('id', $tagIds)->get(['id', 'name', 'color']);

        return Helper::jsonResponse(true, 'imported From Google Data  successfully!', 201, [
            'customer' => $customer,
            'tags' => $tags,
            'tag_count' => count($tags),
        ]);
    }


    /**
     * import Form Scan a bussiness Card  
     */
    public function importFormCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_type' => 'nullable|in:prospect,customer,inactive',
            'company_name' => 'nullable|string|max:200',
            'owner_name' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:25',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|unique:customers,email',
            'website' => 'nullable|url|max:300',
            'tag_id' => 'nullable|array',
            'tag_id.*' => 'exists:tags,id',
            'description' => 'nullable|string|max:3000',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        $customer = Customer::create($data);

        $tagIds = $data['tag_id'] ?? [];
        $tags = Tag::whereIn('id', $tagIds)->get(['id', 'name', 'color']);

        return Helper::jsonResponse(true, 'Scan a Business card successfully!', 201, [
            'customer' => $customer,
            'tags' => $tags,
            'tag_count' => count($tags),
        ]);
    }
}
