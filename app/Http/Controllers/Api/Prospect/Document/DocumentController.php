<?php

namespace App\Http\Controllers\Api\Prospect\Document;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Docs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    // -- document list api
    public function index(Request $request)
    {
        try {
            $per_page = $request->query('per_page', 50);
            $customerId = $request->query('customer_id');
            $userId = Auth::id();

            if (!$customerId) {
                return Helper::jsonResponse(false, 'customer_id is required', 400);
            }

            $document = Docs::where('user_id', $userId)->where('customer_id', $customerId)
                ->whereHas('customer', function ($q) {
                    $q->where('contact_type', 'prospect');
                })->paginate($per_page);

            if ($document->isEmpty()) {
                return Helper::jsonResponse(true, 'Documents data empty.', 200, [
                    'documents' => [],
                ]);
            }

            return Helper::jsonResponse(true, 'Document list retrieved successfully.', 200, [
                'documents' => $document->items(),
                'pagination' => [
                    'current_page' => $document->currentPage(),
                    'last_page' => $document->lastPage(),
                    'total' => $document->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Server Error', 500, [
                'error' => $e->getMessage()
            ]);
        }
    }


    //image convert pdf and pdf upload 
    // public function create(Request $request)
    // {
    //     $request->validate([
    //         'customer_id' => 'required|exists:customers,id',
    //         'file' => 'required|array|min:1',
    //         'file.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
    //     ]);

    //     $uploadedDocs = [];
    //     $imagePaths = [];

    //     foreach ($request->file('file') as $index => $file) {
    //         try {
    //             $ext = strtolower($file->getClientOriginalExtension());

    //             if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
    //                 $uploadedPath = Helper::fileUpload($file, 'docs', 'image_' . $index);

    //                 if (!$uploadedPath) {
    //                     Log::error("Image upload failed for file index $index");
    //                     continue;
    //                 }
    //                 $imagePaths[] = public_path($uploadedPath);
    //             } elseif ($ext === 'pdf') {
    //                 $uploadedPath = Helper::fileUpload($file, 'docs', 'pdf_' . $index);

    //                 if (!$uploadedPath) {
    //                     Log::error("PDF upload failed for file index $index");
    //                     continue;
    //                 }

    //                 try {
    //                     $doc = Docs::create([
    //                         'customer_id' => $request->customer_id,
    //                         'file' => $uploadedPath,
    //                     ]);
    //                     $uploadedDocs[] = $doc;
    //                 } catch (\Exception $e) {
    //                     Log::error("DB insert failed for PDF file index $index: " . $e->getMessage());
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Exception on file index $index: " . $e->getMessage());
    //         }
    //     }

    //     // image convert to pdf
    //     if (count($imagePaths) > 0) {
    //         try {
    //             $pdf = PDF::loadView('pdf.images_to_pdf', ['images' => $imagePaths]);

    //             $pdfName = 'images_to_pdf_' . time() . '.pdf';
    //             $pdfPath = public_path('uploads/docs/' . $pdfName);

    //             $pdf->save($pdfPath);

    //             try {
    //                 $doc = Docs::create([
    //                     'customer_id' => $request->customer_id,
    //                     'file' => 'uploads/docs/' . $pdfName,
    //                 ]);
    //                 $uploadedDocs[] = $doc;
    //             } catch (\Exception $e) {
    //                 Log::error("DB insert failed for generated PDF: " . $e->getMessage());
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("PDF generation failed: " . $e->getMessage());
    //         }
    //     }

    //     return response()->json([
    //         'message' => 'Files processed successfully',
    //         'data' => $uploadedDocs,
    //     ]);
    // }

    public function create(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'file' => 'required|array|min:1',
            'file.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $uploadedDocs = [];
        $imageFiles = [];

        foreach ($request->file('file') as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $imageFiles[] = $file;
            } elseif ($extension === 'pdf') {
                $path = Helper::PdfUpload($file, 'docs');
                if ($path && $doc = $this->storePdf($request->customer_id, $path)) {
                    $uploadedDocs[] = $doc;
                }
            }
        }

        // Convert and store image(s) as PDF
        if (!empty($imageFiles)) {
            $pdfPath = $this->convertImagesToPdf($imageFiles);
            if ($pdfPath && $doc = $this->storePdf($request->customer_id, $pdfPath)) {
                $uploadedDocs[] = $doc;
            }
        }

        return Helper::jsonResponse(true, 'Documents uploaded successfully.', 201, $uploadedDocs);
    }

    protected function convertImagesToPdf(array $imageFiles): ?string
    {
        try {
            $firstImage = $imageFiles[0];
            $baseName = preg_replace('/\s+/', '_', pathinfo($firstImage->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = $baseName . '_' . time() . '.pdf';

            $uploadDir = public_path('uploads/docs');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir);
            }

            $relativePath = 'uploads/docs/' . $filename;
            $fullPath = public_path($relativePath);

            $pdf = PDF::loadView('pdf.images_to_pdf', [
                'images' => array_map(function ($file) {
                    return 'data://application/octet-stream;base64,' . base64_encode(file_get_contents($file->path()));
                }, $imageFiles)
            ]);

            $pdf->save($fullPath);
            return $relativePath;
        } catch (\Exception $e) {
            Log::error('Image to PDF conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    //pdf store
    protected function storePdf(int $customerId, string $filePath): ?Docs
    {
        try {
            return Docs::create([
                'customer_id' => $customerId,
                'file' => $filePath,
                'user_id' => Auth::id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store PDF in DB: ' . $e->getMessage());
            return null;
        }
    }


    //details api
    public function details(Request $request, $id)
    {
        try {
            $customerId = $request->query('customer_id');

            $document = Docs::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($query) {
                    $query->whereIn('contact_type', ['prospect'])->where('user_id', Auth::user()->id);
                })->first();

            if (!$document) {
                return Helper::jsonResponse(false, 'document Not Found .', 404);
            }

            return Helper::jsonResponse(true, 'document Details Retrieved Successfully!', 200, $document);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed:', 500, [$e->getMessage()]);
        }
    }

    //delete api
    public function destroy(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            $customerId = $request->query('customer_id');

            $document = Docs::where('id', $id)->where('customer_id', $customerId)
                ->whereHas('customer', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('contact_type', 'prospect');
                })->first();

            if (!$document) {
                return Helper::jsonResponse(false, 'Document not found!', 404);
            }

            if ($document->file) {
                $parsedUrl = parse_url($document->file, PHP_URL_PATH);
                $oldFilePath = ltrim($parsedUrl, '/');
                Helper::fileDelete($oldFilePath);
            }

            $document->delete();

            return Helper::jsonResponse(true, 'Document deleted successfully!', 200);
        } catch (\Exception $e) {
            return Helper::jsonResponse(false, 'Failed:', 500, [$e->getMessage()]);
        }
    }
}
