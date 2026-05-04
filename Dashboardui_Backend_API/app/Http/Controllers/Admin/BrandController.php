<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    public function index()
    {
        return view('admin.assets.brand');
    }

    public function uploadLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:png,jpg,jpeg|max:10240', // 10 MB (kilobytes per Laravel file rule)
                'alt_text' => 'required|string|max:255',
                'description' => 'nullable|string|max:500'
            ]);

            // Handle file upload
            if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/logos', $filename, 'public');
            
            // Update or create logo in database
            $logo = Logo::where('name', 'Main Logo')->first();
            if ($logo) {
                // Update existing record
                $logo->update([
                    'filename' => $filename,
                    'path' => 'assets/logos/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Main company logo used in headers, sidebars, and branding'
                ]);
            } else {
                // Create new record
                $logo = Logo::create([
                    'name' => 'Main Logo',
                    'filename' => $filename,
                    'path' => 'assets/logos/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Main company logo used in headers, sidebars, and branding'
                ]);
            }
            
            // Force refresh the model
            $logo->refresh();
            
            // Log the update for debugging
            \Log::info('Logo updated in database', [
                'name' => $logo->name,
                'filename' => $logo->filename,
                'path' => $logo->path,
                'new_filename' => $filename,
                'timestamp' => time()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.logo_upload_success'),
                'logo' => [
                    'filename' => $filename,
                    'path' => $path,
                    'alt' => $request->alt_text,
                    'url' => asset('storage/' . $path)
                ]
            ]);
            }

            return response()->json(['success' => false, 'message' => __('messages.no_logo_file')], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => __('messages.logo_upload_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadFavicon(Request $request)
    {
        try {
            $request->validate([
                'favicon' => 'required|image|mimes:png,ico|max:1024',
                'alt_text' => 'required|string|max:255',
                'description' => 'nullable|string|max:500'
            ]);

            // Handle file upload
            if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/favicons', $filename, 'public');
            
            // Update or create favicon in database
            $favicon = Logo::where('name', 'Favicon')->first();
            if ($favicon) {
                // Update existing record
                $favicon->update([
                    'filename' => $filename,
                    'path' => 'assets/favicons/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Browser tab icon and favicon'
                ]);
            } else {
                // Create new record
                $favicon = Logo::create([
                    'name' => 'Favicon',
                    'filename' => $filename,
                    'path' => 'assets/favicons/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Browser tab icon and favicon'
                ]);
            }
            
            // Force refresh the model
            $favicon->refresh();
            
            // Log the update for debugging
            \Log::info('Favicon updated in database', [
                'name' => $favicon->name,
                'filename' => $favicon->filename,
                'path' => $favicon->path,
                'new_filename' => $filename,
                'timestamp' => time()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.favicon_upload_success'),
                'favicon' => [
                    'filename' => $filename,
                    'path' => $path,
                    'alt' => $request->alt_text,
                    'url' => asset('storage/' . $path)
                ]
            ]);
            }

            return response()->json(['success' => false, 'message' => __('messages.no_favicon_file')], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => __('messages.logo_upload_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncToJson()
    {
        try {
            // Get current database data
            $mainLogo = Logo::where('name', 'Main Logo')->first();
            $favicon = Logo::where('name', 'Favicon')->first();

            if (!$mainLogo || !$favicon) {
                return response()->json([
                    'success' => false, 
                    'message' => __('messages.no_brand_assets_found')
                ], 400);
            }

            // Read current JSON file
            $jsonPath = base_path('jsonassets/brand-assets.json');
            $jsonData = json_decode(File::get($jsonPath), true);

            // Copy files from storage to public directory
            $logoSourcePath = storage_path('app/public/' . $mainLogo->path);
            $logoDestPath = public_path($mainLogo->path);
            $faviconSourcePath = storage_path('app/public/' . $favicon->path);
            $faviconDestPath = public_path($favicon->path);
            
            // Ensure destination directories exist
            if (!file_exists(dirname($logoDestPath))) {
                mkdir(dirname($logoDestPath), 0755, true);
            }
            if (!file_exists(dirname($faviconDestPath))) {
                mkdir(dirname($faviconDestPath), 0755, true);
            }

            // Resolve missing or legacy source paths
            if (!file_exists($logoSourcePath)) {
                $legacyLogo = public_path($mainLogo->path ?: 'assets/logos/ClientLogo.png');
                if (file_exists($legacyLogo)) {
                    $logoSourcePath = $legacyLogo;
                }
            }
            if (!file_exists($faviconSourcePath)) {
                $legacyFavicon = public_path($favicon->path ?: 'favicon.png');
                if (file_exists($legacyFavicon)) {
                    $faviconSourcePath = $legacyFavicon;
                }
            }

            // Validate sources exist before copying
            if (!file_exists($logoSourcePath)) {
                throw new \RuntimeException('Logo source not found at ' . $logoSourcePath);
            }
            if (!file_exists($faviconSourcePath)) {
                throw new \RuntimeException('Favicon source not found at ' . $faviconSourcePath);
            }

            // Copy files
            copy($logoSourcePath, $logoDestPath);
            copy($faviconSourcePath, $faviconDestPath);

            // Update JSON data with public paths
            $jsonData['logo'] = [
                'filename' => $mainLogo->filename,
                'path' => $mainLogo->path, // This will be the public path
                'alt' => $mainLogo->alt,
                'description' => $mainLogo->description
            ];

            $jsonData['favicon'] = [
                'filename' => $favicon->filename,
                'path' => $favicon->path, // This will be the public path
                'alt' => $favicon->alt,
                'description' => $favicon->description
            ];

            // Write updated JSON
            File::put($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => __('messages.brand_sync_success')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.brand_sync_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function getComparison()
    {
        // Get JSON data
        $jsonAssets = get_brand_assets();
        
        // Get database data
        $dbLogos = get_all_logos_from_db();
        
        return response()->json([
            'json' => $jsonAssets,
            'database' => $dbLogos->toArray(),
            'has_changes' => $this->hasChanges($jsonAssets, $dbLogos)
        ]);
    }

    private function hasChanges($jsonAssets, $dbLogos)
    {
        $mainLogo = $dbLogos->where('name', 'Main Logo')->first();
        $favicon = $dbLogos->where('name', 'Favicon')->first();

        if (!$mainLogo || !$favicon) {
            return false;
        }

        // Compare logo
        $logoChanged = (
            $jsonAssets['logo']['filename'] !== $mainLogo->filename ||
            $jsonAssets['logo']['path'] !== $mainLogo->path ||
            $jsonAssets['logo']['alt'] !== $mainLogo->alt
        );

        // Compare favicon
        $faviconChanged = (
            $jsonAssets['favicon']['filename'] !== $favicon->filename ||
            $jsonAssets['favicon']['path'] !== $favicon->path ||
            $jsonAssets['favicon']['alt'] !== $favicon->alt
        );

        return $logoChanged || $faviconChanged;
    }
}
