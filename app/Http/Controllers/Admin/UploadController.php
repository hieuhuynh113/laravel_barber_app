<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload image from CKEditor
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            
            // Validate file
            $validator = Validator::make(['upload' => $file], [
                'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'uploaded' => 0,
                    'error' => [
                        'message' => 'Tệp không hợp lệ. Chỉ cho phép hình ảnh (jpeg, png, jpg, gif) với kích thước tối đa 2MB.'
                    ]
                ]);
            }
            
            // Create safe filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
            
            // Store in the "uploads" folder
            $file->storeAs('uploads', $fileName, 'public');
            
            $url = asset('storage/uploads/' . $fileName);
            
            // Response for CKEditor 4
            return response()->json([
                'uploaded' => 1,
                'fileName' => $fileName,
                'url' => $url,
            ]);
        }
        
        return response()->json([
            'uploaded' => 0,
            'error' => [
                'message' => 'Không thể tải lên tệp hình ảnh'
            ]
        ]);
    }
} 