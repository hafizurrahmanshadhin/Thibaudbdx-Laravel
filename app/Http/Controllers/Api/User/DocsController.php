<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Docs;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Log;

class DocsController extends Controller
{
    /**
     * document list api
     */
    public function index(Request $request)
    {
        try {
            $per_page = $request->query('per_page', 50);
            $document = Docs::where('user_id', Auth::user()->id)->whereNull('customer_id')->paginate($per_page);

            if ($document->isEmpty()) {
                return Helper::jsonResponse(true, 'Documents data empty.', 200,);
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

    /**
     * Create a new document app owner
     */
    public function create(Request $request)
    {
        $request->validate([
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
                if ($path && $doc = $this->storePdf($path)) {
                    $uploadedDocs[] = $doc;
                }
            }
        }

        // Convert and store image(s) as PDF
        if (!empty($imageFiles)) {
            $pdfPath = $this->convertImagesToPdf($imageFiles);
            if ($pdfPath && $doc = $this->storePdf($pdfPath)) {
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
    protected function storePdf(string $filePath): ?Docs
    {
        try {
            return Docs::create([
                'file' => $filePath,
                'user_id' => Auth::user()->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store PDF in DB: ' . $e->getMessage());
            return null;
        }
    }



    //details api
    public function details($id)
    {
        try {
            $document = Docs::whereNull('customer_id',)->where('user_id', Auth::user()->id)->find($id);

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
            $document = Docs::where('id', $id)->whereNull('customer_id',)->where('user_id', $userId)->first();

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
