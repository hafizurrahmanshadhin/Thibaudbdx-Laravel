<?php

namespace App\Http\Controllers\Api\Product;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ProductControler extends Controller
{
    //product list api---
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 50);
            $userId = Auth::id();
            // Start query builder
            $products = Product::where('user_id', $userId)->paginate($perPage);;
            if ($products->isEmpty()) {
                return Helper::jsonResponse(false, 'No products found.', 404);
            }
            return Helper::jsonResponse(true, 'Product list retrieved successfully.', 200, $products);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, ['error' => $e->getMessage()]);
        }
    }


    //-- product create api
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wine_name' => 'required|string|max:255|unique:products,wine_name',
            'cuvee' => 'required|string|max:255',
            'type' => 'required|in:Appellation,AOC,IGP',
            'color' => 'required|string|max:255',
            'soil_type' => 'required|string|max:255',
            'harvest_ageing' => 'required|string|max:300',
            'food' => 'required|string|max:255',
            'tasting_notes' => 'required|string|max:500',
            'awards' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10048',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'website' => 'required|url|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $uploadedImage = Helper::fileUpload($request->file('image'), 'products', $request->input('wine_name'));
            $data['image'] = $uploadedImage;
        }
        $wine = Product::create($data);
        return Helper::jsonResponse(true, 'Product Created Successfully !', 201, $wine);
    }


    //--product-details
    public function details($id)
    {
        try {
            $user = Auth::id();
            $product = Product::where('user_id', $user)->find($id);

            if (!$product) {
                return Helper::jsonResponse(false, 'Product Not Found!', 404);
            }

            return Helper::jsonResponse(true, 'Product Details Successfully !', 200, $product);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed :', 500, [$e->getMessage()]);
        }
    }

    //--product update
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'wine_name' => 'nullable|string|max:255',
                'cuvee' => 'nullable|string|max:255',
                'type' => 'nullable|in:Appellation,AOC,IGP',
                'color' => 'nullable|string|max:255',
                'soil_type' => 'nullable|string|max:255',
                'harvest_ageing' => 'nullable|string|max:300',
                'food' => 'nullable|string|max:255',
                'tasting_notes' => 'nullable|string|max:500',
                'awards' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10048',
                'company_name' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'website' => 'nullable|url|max:300',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();

            $Product = Product::where('user_id', Auth::id())->find($id);
            if (!$Product) {
                return Helper::jsonResponse(false, 'Product Not Found!', 404);
            }

            // image 
            if ($request->hasFile('image')) {
                if ($Product->image) {
                    $parsedUrl = parse_url($Product->image, PHP_URL_PATH);
                    $oldImagePath = ltrim($parsedUrl, '/');
                    Helper::fileDelete($oldImagePath);
                }

                $uploadedImage = Helper::fileUpload($request->file('image'), 'products', $request->input('wine_name'));
                $data['image'] = $uploadedImage;
            }
            $Product->update($data);

            return Helper::jsonResponse(true, 'Product Updated Successfully!', 200, $Product);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed :', 500, [$e->getMessage()]);
        }
    }

    //--Product delete
    public function destroy($id)
    {
        try {
            $userId = Auth::user()->id;
            $Product = Product::where('user_id', $userId)->find($id);

            if (!$Product) {
                return Helper::jsonResponse(false, 'Product Not Found!', 404);
            }

            if ($Product->image) {
                $parsedUrl = parse_url($Product->image, PHP_URL_PATH);
                $oldImagePath = ltrim($parsedUrl, '/');
                Helper::fileDelete($oldImagePath);
            }
            $Product->delete();
            return Helper::jsonResponse(true, 'Product Deleted Successfully!', 200,);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed :', 500, [$e->getMessage()]);
        }
    }


    // get product by id and generate pdf
    public function ProductPDF($id)
    {
        $userId = Auth::user()->id;
        $product = Product::where('user_id', $userId)->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product Not found'], 404);
        }

        $filename = 'product_' . Str::slug($product->wine_name) . '.pdf';
        $filepath = public_path('pdf/' . $filename);

        if (!File::exists(public_path('pdf'))) {
            File::makeDirectory(public_path('pdf'));
        }

        PDF::loadView('pdf.product', compact('product'))->save($filepath);

        return response()->json([
            'success' => true,
            'message' => 'PDF Generated Successfully !',
            'download_url' => asset('pdf/' . $filename)
        ]);
    }
}
