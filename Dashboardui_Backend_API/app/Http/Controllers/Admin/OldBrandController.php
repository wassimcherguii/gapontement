<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class OldBrandController extends Controller
{
    public function index()
    {
        return view('admin.assets.old-brand');
    }

    public function getLogos()
    {
        // Get all logo files from storage
        $logoFiles = Storage::disk('public')->files('assets/logos');
        $logos = [];
        
        foreach ($logoFiles as $file) {
            $filename = basename($file);
            $logos[] = [
                'filename' => $filename,
                'path' => $file,
                'url' => asset('storage/' . $file),
                'size' => Storage::disk('public')->size($file),
                'modified' => Storage::disk('public')->lastModified($file),
                'is_current' => $this->isCurrentLogo($filename)
            ];
        }
        
        // Sort by modification date (newest first)
        usort($logos, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return response()->json(['logos' => $logos]);
    }

    public function getFavicons()
    {
        // Get all favicon files from storage
        $faviconFiles = Storage::disk('public')->files('assets/favicons');
        $favicons = [];
        
        foreach ($faviconFiles as $file) {
            $filename = basename($file);
            $favicons[] = [
                'filename' => $filename,
                'path' => $file,
                'url' => asset('storage/' . $file),
                'size' => Storage::disk('public')->size($file),
                'modified' => Storage::disk('public')->lastModified($file),
                'is_current' => $this->isCurrentFavicon($filename)
            ];
        }
        
        // Sort by modification date (newest first)
        usort($favicons, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return response()->json(['favicons' => $favicons]);
    }

    public function restore(Request $request)
    {
        // Prevent duplicate requests within 5 seconds
        $requestKey = 'restore_' . auth()->id() . '_' . $request->input('filename');
        if (cache()->has($requestKey)) {
            return response()->json([
                'success' => false, 
                'message' => __('messages.request_in_progress')
            ], 429);
        }
        
        // Set cache for 5 seconds
        cache()->put($requestKey, true, 5);
        
        $request->validate([
            'type' => 'required|in:logo,favicon',
            'filename' => 'required|string',
            'name' => 'required|string|max:255',
            'alt' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        $oldFilename = $request->filename;
        $type = $request->type;
        
        // Check if source file exists
        $sourcePath = 'assets/' . ($type === 'logo' ? 'logos' : 'favicons') . '/' . $oldFilename;
        if (!Storage::disk('public')->exists($sourcePath)) {
            return response()->json(['success' => false, 'message' => __('messages.file_not_found')], 404);
        }

        // Keep the original filename - no need to copy/rename the file
        $newFilename = $oldFilename;
        $newPath = $sourcePath;

        // Update or create record in database
        $recordName = $type === 'logo' ? 'Main Logo' : 'Favicon';
        $logo = Logo::where('name', $recordName)->first();
        
        if ($logo) {
            $logo->update([
                'filename' => $newFilename,
                'path' => $newPath,
                'alt' => $request->alt,
                'description' => $request->description ?? ''
            ]);
        } else {
            Logo::create([
                'name' => $recordName,
                'filename' => $newFilename,
                'path' => $newPath,
                'alt' => $request->alt,
                'description' => $request->description ?? ''
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.restore_success', ['type' => ucfirst($type)])
        ]);
    }

    public function delete(Request $request)
    {
        // Prevent duplicate requests within 5 seconds
        $requestKey = 'delete_' . auth()->id() . '_' . $request->input('filename');
        if (cache()->has($requestKey)) {
            return response()->json([
                'success' => false, 
                'message' => __('messages.request_in_progress')
            ], 429);
        }
        
        // Set cache for 5 seconds
        cache()->put($requestKey, true, 5);
        
        $request->validate([
            'type' => 'required|in:logo,favicon',
            'filename' => 'required|string'
        ]);

        $filename = $request->filename;
        $type = $request->type;
        
        // Check if this is the current logo/favicon
        if ($this->isCurrentLogo($filename) || $this->isCurrentFavicon($filename)) {
            return response()->json(['success' => false, 'message' => __('messages.cannot_delete_current', ['type' => $type])], 400);
        }
        
        // Delete file from storage
        $filePath = 'assets/' . ($type === 'logo' ? 'logos' : 'favicons') . '/' . $filename;
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.delete_success_with_type', ['type' => ucfirst($type)])
        ]);
    }

    private function isCurrentLogo($filename)
    {
        $currentLogo = Logo::where('name', 'Main Logo')->first();
        return $currentLogo && $currentLogo->filename === $filename;
    }

    private function isCurrentFavicon($filename)
    {
        $currentFavicon = Logo::where('name', 'Favicon')->first();
        return $currentFavicon && $currentFavicon->filename === $filename;
    }
}
