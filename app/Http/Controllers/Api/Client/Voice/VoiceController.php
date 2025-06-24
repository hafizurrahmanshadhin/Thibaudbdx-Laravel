<?php

namespace App\Http\Controllers\Api\Client\Voice;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Voice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VoiceController extends Controller
{
    // -- voice list api
    public function index(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $per_page = $request->query('per_page', 50);
            $customerId = $request->query('customer_id');

            $voice = Voice::with('customer:id,contact_type,user_id')
                ->whereHas('customer', function ($q) {
                    $q->where('contact_type', 'customer')->where('user_id', Auth::user()->id);
                })->where('customer_id', $customerId)

                ->when(!empty(trim($search)), function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })
                ->select('id', 'title', 'voice_file', 'description', 'created_at')
                ->paginate($per_page);

            if (!empty($search) && $voice->isEmpty()) {
                return Helper::jsonResponse(false, 'No Voice found for the given search.', 404);
            }

            if ($voice->isEmpty()) {
                return Helper::jsonResponse(true, 'Voices Data Empty ', 200, [
                    'voices' => [],
                ]);
            }

            return Helper::jsonResponse(true, 'Voice list retrieved successfully.', 200, [
                'voices' => $voice->items(),
                'pagination' => [
                    'current_page' => $voice->currentPage(),
                    'last_page' => $voice->lastPage(),
                    'total' => $voice->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }



    //create-voice api
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'customer_id' => 'required|exists:customers,id',
                'description' => 'required|string|max:600',
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
            $customer = Customer::where('id', $request->input('customer_id'))->where('contact_type', 'customer')->first();

            if (!$customer) {
                return Helper::jsonResponse(false, 'Invalid Customer', 403);
            }

            // Save to database
            $voice = Voice::create([
                'title'        => $request->input('title'),
                'customer_id'  => $request->input('customer_id'),
                'description'  => $request->input('description'),
                'voice_file'   => $filePath,
                'duration'     => $request->input('duration'),
            ]);

            return Helper::jsonResponse(true, 'Voice Created Successfully!', 201, $voice);
        } catch (Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }


    // details voice api
    public function details(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');

            $voice = Voice::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($query) {
                    $query->where('contact_type', 'customer')->where('user_id', Auth::user()->id);
                })->first();

            if (!$voice) {
                return Helper::jsonResponse(false, 'voice Not Found .', 404);
            }

            return Helper::jsonResponse(true, 'Voice Details Retrieved Successfully !', 200, $voice);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed:', 500, [$e->getMessage()]);
        }
    }


    //update functon
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'customer_id' => 'nullable|exists:customers,id',
                'description' => 'nullable|string|max:600',
                'voice_file' => 'nullable|file|mimes:mp3,wav,aac|max:40000',
                'duration' => 'nullable|integer|min:1|max:600',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $validator->validated();
            $userId = Auth::id();

            //validation
            $voice = Voice::where('id', $id)->where('customer_id', $data['customer_id'])
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

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



    //-- voice delete
    public function destroy(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');
            $userId = Auth::id();

            $voice = Voice::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'customer');
                })->first();

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
