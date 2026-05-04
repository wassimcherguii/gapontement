<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    use ApiResponse;

    /**
     * Get brand assets (logo and favicon)
     */
    public function index()
    {
        try {
            $logo = Logo::where('name', 'Main Logo')->first();
            $favicon = Logo::where('name', 'Favicon')->first();

            $data = [
                'logo' => null,
                'favicon' => null,
            ];

            if ($logo) {
                $data['logo'] = [
                    'id' => $logo->id,
                    'name' => $logo->name,
                    'filename' => $logo->filename,
                    'url' => Storage::url($logo->path),
                    'alt' => $logo->alt,
                    'description' => $logo->description,
                ];
            }

            if ($favicon) {
                $data['favicon'] = [
                    'id' => $favicon->id,
                    'name' => $favicon->name,
                    'filename' => $favicon->filename,
                    'url' => Storage::url($favicon->path),
                    'alt' => $favicon->alt,
                    'description' => $favicon->description,
                ];
            }

            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch brand assets: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload logo
     */
    public function uploadLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:png,jpg,jpeg|max:10240', // 10 MB
                'alt_text' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/logos', $filename, 'public');

            $logo = Logo::where('name', 'Main Logo')->first();
            if ($logo) {
                // Delete old logo
                if (Storage::disk('public')->exists($logo->path)) {
                    Storage::disk('public')->delete($logo->path);
                }
                $logo->update([
                    'filename' => $filename,
                    'path' => 'assets/logos/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Main company logo',
                ]);
            } else {
                $logo = Logo::create([
                    'name' => 'Main Logo',
                    'filename' => $filename,
                    'path' => 'assets/logos/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Main company logo',
                ]);
            }

            return $this->success([
                'id' => $logo->id,
                'name' => $logo->name,
                'filename' => $logo->filename,
                'url' => Storage::url($logo->path),
                'alt' => $logo->alt,
            ], 'Logo uploaded successfully');
        } catch (ValidationException $e) {
            return $this->validationError($e->validator);
        } catch (\Exception $e) {
            return $this->error('Failed to upload logo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload favicon
     */
    public function uploadFavicon(Request $request)
    {
        try {
            $request->validate([
                'favicon' => 'required|image|mimes:png,ico|max:512',
                'alt_text' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
            ]);

            $file = $request->file('favicon');
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('assets/favicons', $filename, 'public');

            $favicon = Logo::where('name', 'Favicon')->first();
            if ($favicon) {
                // Delete old favicon
                if (Storage::disk('public')->exists($favicon->path)) {
                    Storage::disk('public')->delete($favicon->path);
                }
                $favicon->update([
                    'filename' => $filename,
                    'path' => 'assets/favicons/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Website favicon',
                ]);
            } else {
                $favicon = Logo::create([
                    'name' => 'Favicon',
                    'filename' => $filename,
                    'path' => 'assets/favicons/' . $filename,
                    'alt' => $request->alt_text,
                    'description' => $request->description ?? 'Website favicon',
                ]);
            }

            return $this->success([
                'id' => $favicon->id,
                'name' => $favicon->name,
                'filename' => $favicon->filename,
                'url' => Storage::url($favicon->path),
                'alt' => $favicon->alt,
            ], 'Favicon uploaded successfully');
        } catch (ValidationException $e) {
            return $this->validationError($e->validator);
        } catch (\Exception $e) {
            return $this->error('Failed to upload favicon: ' . $e->getMessage(), 500);
        }
    }
}
