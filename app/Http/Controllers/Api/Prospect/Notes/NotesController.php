<?php

namespace App\Http\Controllers\Api\Prospect\Notes;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Voice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{
    /**
     * List Notes and Voices for a specific customer
     */
    public function index(Request $request)
    {
        try {
            $search      = trim($request->query('search', ''));
            $customerId  = $request->query('customer_id');
            $userId      = Auth::id();

            $voices = Voice::where('user_id', $userId)
                ->where('customer_id', $customerId)
                ->when($search, function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->get()
                ->map(function ($item) {
                    $item->type = 'voices';
                    return $item;
                });

            $notes = Note::where('user_id', $userId)
                ->where('customer_id', $customerId)
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })->get()->map(function ($item) {
                    $item->type = 'notes';
                    return $item;
                });

            $data = collect()->merge($notes)->merge($voices)->sortByDesc('created_at')->values();

            return Helper::jsonResponse(true, 'Notes list retrieved successfully.', 200, $data);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }


    //  Create a Note
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'         => 'required|string|max:200',
                'customer_id'  => 'required|exists:customers,id',
                'description'  => 'required|string|max:600',
                'date'         => 'required|date|after_or_equal:today'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();

            $note = Note::create($data);

            return Helper::jsonResponse(true, 'Note created successfully.', 201, $note);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Note creation failed.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    //  Show a specific Note
    public function details(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');
            $userId     = Auth::id();

            $note = Note::where('id', $id)
                ->where('user_id', $userId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$note) {
                return Helper::jsonResponse(false, 'Note not found.', 404);
            }

            return Helper::jsonResponse(true, 'Note details retrieved successfully.', 200, $note);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed to retrieve note.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    //  Update a Note
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'         => 'nullable|string|max:255',
                'description'  => 'nullable|string|max:600',
                'customer_id'  => 'required|exists:customers,id',
                'date'         => 'nullable|date|after_or_equal:today',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data       = $validator->validated();
            $userId     = Auth::id();
            $customerId = $request->query('customer_id');

            $note = Note::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$note) {
                return Helper::jsonResponse(false, 'Note not found or access denied.', 404);
            }

            $note->update($data);

            return Helper::jsonResponse(true, 'Note updated successfully.', 200, $note);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Note update failed.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    //  Delete a Note
    public function destroy(Request $request, $id)
    {
        try {
            $userId     = Auth::id();
            $customerId = $request->query('customer_id');

            $note = Note::where('id', $id)
                ->where('user_id', $userId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$note) {
                return Helper::jsonResponse(false, 'Note not found or access denied.', 404);
            }

            $note->delete();

            return Helper::jsonResponse(true, 'Note deleted successfully.', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Note deletion failed.', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
