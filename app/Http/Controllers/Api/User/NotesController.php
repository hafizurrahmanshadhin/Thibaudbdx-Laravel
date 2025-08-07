<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Voice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{

    /**
     * List Notes and Voices for the user
     */
    public function NotesList(Request $request)
    {
        try {
            $search      = trim($request->query('search', ''));
            $userId      = Auth::id();

            $voices = Voice::where('user_id', $userId)
                ->whereNull('customer_id')
                ->when($search, function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->get()
                ->map(function ($item) {
                    $item->type = 'voices';
                    return $item;
                });

            $notes = Task::where('user_id', $userId)
                ->whereNull('customer_id')
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


    // ============================> Notes <=========================
    //user-notes-create
    public function notesCreate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:200',
                'description' => 'required|string|max:600',
                'date' => 'required|date|after_or_equal:today'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $data['user_id'] = Auth::id();

            $Notes = Task::create($data);

            return Helper::jsonResponse(true, 'Notes Created Successfully!', 200, $Notes);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Notes Create Failed!', 500, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    // user Notes details --
    public function noteDdetails($id)
    {
        try {
            $userId = Auth::user()->id;

            $task = Task::where('id', $id)->where('user_id', $userId)->first();

            if (!$task) {
                return Helper::jsonResponse(false, 'Notes Not Found .', 404);
            }

            return Helper::jsonResponse(true, 'Notes Details Retrieved Successfully!', 200, $task);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed:', 500, [$e->getMessage()]);
        }
    }

    //user-update functon
    public function notesUpdate(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
                'date' => 'nullable|date|after_or_equal:today',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $userId = Auth::id();

            $notes = Task::where('id', $id)->where('user_id', $userId)->first();

            if (!$notes) {
                return Helper::jsonResponse(false, 'Notes not found.', 404);
            }

            $notes->update($data);

            return Helper::jsonResponse(true, 'Notes updated successfully.', 200, $notes);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error:', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    //--delete notes 
    public function notesDestroy($id)
    {
        try {
            $userId = Auth::id();
            $notes = Task::where('id', $id)->where('user_id', $userId)->first();
            if (!$notes) {
                return Helper::jsonResponse(false, 'Notes Not Found ? Or access denied.', 404);
            }

            $notes->delete();
            return Helper::jsonResponse(true, 'Notes deleted successfully.', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }


    //====================> Voice <=================================
    // voice create api
    public function voiceCreate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:600',
                'date' => 'required|date|after_or_equal:today',
                'voice_file' => 'required|file|mimes:mp3,wav,aac|max:40000',
                'duration' => 'required|integer|min:1|max:600',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            //voice upload
            $filePath = Helper::fileUpload($request->file('voice_file'), 'voices', $request->title ?? 'voice');

            if (!$filePath) {
                return response()->json(['error' => 'Voice file upload failed.'], 400);
            }


            $voice = Voice::create([
                'title'        => $request->input('title'),
                'user_id'  => Auth::user()->id,
                'description'  => $request->input('description'),
                'date'         => $request->input('date'),
                'voice_file'   => $filePath,
                'duration'     => $request->input('duration'),
            ]);

            return Helper::jsonResponse(true, 'Voice Created Successfully!', 201, $voice);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }

    //use  details voice api
    public function voiceDetails($id)
    {
        try {
            $userId = Auth::user()->id;
            $voice = Voice::where('id', $id)->where('user_id', $userId)->first();

            if (!$voice) {
                return Helper::jsonResponse(false, 'voice Not Found .', 404);
            }

            return Helper::jsonResponse(true, 'Voice Details Retrieved Successfully !', 200, $voice);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed:', 500, [$e->getMessage()]);
        }
    }

    //user update functon
    public function voiceUpdate(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:600',
                'date' => 'nullable|date|after_or_equal:today',
                'voice_file' => 'nullable|file|mimes:mp3,wav,aac|max:40000',
                'duration' => 'nullable|integer|min:1|max:600',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $userId = Auth::id();
            $voice = Voice::where('id', $id)->where('user_id', $userId)->first();
            if (!$voice) {
                return Helper::jsonResponse(false, 'voice not found .', 404);
            }

            $voice->update($data);

            return Helper::jsonResponse(true, 'Voice updated successfully.', 200, $voice);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error :', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }


    //-- user voice delete
    public function voiceDestroy($id)
    {
        try {
            $userId = Auth::user()->id;
            $voice = Voice::where('id', $id)->where('user_id', $userId)->first();
            if (!$voice) {
                return Helper::jsonResponse(false, 'Voice Not Found ?', 404);
            }

            $voice->delete();
            return Helper::jsonResponse(true, 'Voice deleted successfully.', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
