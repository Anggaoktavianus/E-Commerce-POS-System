<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        try {
            $file = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('editor-uploads', $filename, 'public');
            
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'location' => Storage::url($path),
                'message' => 'Image uploaded successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function delete(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);
        
        try {
            $filename = $request->filename;
            
            // Check if file exists in editor-uploads directory
            $path = 'editor-uploads/' . $filename;
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Image not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }
}
