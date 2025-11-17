<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    /**
     * Handle image upload from CKEditor
     */
    public function upload(Request $request)
    {
        try {
            // Support both 'upload' (CKEditor) and 'file' (TinyMCE) field names
            $file = null;
            $fieldName = null;
            
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fieldName = 'file';
            } elseif ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $fieldName = 'upload';
            }
            
            if (!$file) {
                return response()->json([
                    'error' => [
                        'message' => 'Không có file được tải lên.'
                    ]
                ], 400);
            }

            // Validate file
            $request->validate([
                $fieldName => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            ]);

            // Generate unique filename
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $imagePath = $file->storeAs('predictions/images', $imageName, 'public');
            
            // Get URL - use Storage::url() which returns /storage/path
            // Then use asset() to get full URL
            $imageUrl = Storage::url($imagePath);
            
            // Ensure full URL (asset() will prepend APP_URL)
            $fullUrl = asset($imageUrl);

            // TinyMCE expects this format
            return response()->json([
                'location' => $fullUrl,
                'url' => $fullUrl,
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage(),
                    'errors' => $e->errors()
                ]
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return response()->json([
                'error' => [
                    'message' => 'Không thể tải ảnh lên. Vui lòng thử lại. ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}
