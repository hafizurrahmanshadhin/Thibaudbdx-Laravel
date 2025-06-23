<?php

namespace App\Http\Controllers\Api\Tag;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    //list Tags
    public function index(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $per_page = $request->query('per_page', 10);

            $tag = Tag::select('name', 'color', 'created_at');

            if (!empty(trim($search))) {
                $tag->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }
            // Pagination
            $tags = $tag->paginate($per_page);

            if (!empty($search) && $tags->isEmpty()) {
                return Helper::jsonResponse(false, 'No tags found for the given search.', 404);
            }

            return Helper::jsonResponse(true, 'Tag list retrieved successfully.', 200, [
                'data' => $tags,
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }



    //Create Tags
    public function create(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                "name" => "required|max:150|unique:tags,name",
                "color" => "required|max:100"
            ]);

            $data = Tag::create([
                "name" => $request->input("name"),
                "color" => $request->input("color"),
            ]);
            return Helper::jsonResponse(true, 'Tag Created Successfully !', 200, [
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Tag Create Failed', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    //update Tag 
    public function details(Request $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            return Helper::jsonResponse(true, 'Tag Created Successfully !', 200, [
                'data' => $tag
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed', 500, [
                $e->getMessage()
            ]);
        }
    }

    //update Tag
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|max:100',
                'color' => 'nullable|max:100',
            ]);

            $tag = Tag::findOrFail($id);
            $tag->update($validated);

            return Helper::jsonResponse(true, 'Tag updated successfully.', 200, [
                'tag' => $tag
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    //destory
    public function destroy(Request $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return Helper::jsonResponse(true, 'Tag Deleted successfully.', 200,);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
