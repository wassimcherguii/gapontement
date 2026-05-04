<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ColorPalette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class ColorController extends Controller
{
    use ApiResponse;

    /**
     * Get all colors from JSON file (public route, no auth required)
     */
    public function index(Request $request)
    {
        try {
            $jsonPath = base_path('jsonassets/colors.json');
            
            if (!File::exists($jsonPath)) {
                return $this->error('Colors file not found', 404);
            }

            $colors = json_decode(File::get($jsonPath), true);
            
            if (!$colors) {
                return $this->error('Failed to parse colors file', 500);
            }

            // Optional: Filter by theme if requested
            $theme = $request->get('theme');
            if ($theme && isset($colors[$theme])) {
                $colors = [$theme => $colors[$theme]];
            }

            return $this->success($colors);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch colors: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a specific color
     */
    public function show($id)
    {
        try {
            $color = ColorPalette::findOrFail($id);

            return $this->success([
                'id' => $color->id,
                'name' => $color->name,
                'category' => $color->category,
                'theme' => $color->theme,
                'hex_value' => $color->hex_value,
                'rgb_value' => $color->rgb_value,
            ]);
        } catch (\Exception $e) {
            return $this->notFound('Color not found');
        }
    }

    /**
     * Update a color
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'hex_value' => 'required|regex:/^#[A-Fa-f0-9]{6,8}$/',
            ]);

            $color = ColorPalette::findOrFail($id);
            $color->hex_value = $request->hex_value;
            $color->rgb_value = $this->hexToRgb($request->hex_value);
            $color->save();

            return $this->success([
                'id' => $color->id,
                'name' => $color->name,
                'category' => $color->category,
                'theme' => $color->theme,
                'hex_value' => $color->hex_value,
                'rgb_value' => $color->rgb_value,
            ], 'Color updated successfully');
        } catch (ValidationException $e) {
            return $this->validationError($e->validator);
        } catch (\Exception $e) {
            return $this->error('Failed to update color: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Sync colors - Get full color structure from JSON file
     * This endpoint returns the complete color structure for mobile app sync
     */
    public function sync(Request $request)
    {
        try {
            $jsonPath = base_path('jsonassets/colors.json');
            
            if (!File::exists($jsonPath)) {
                return $this->error('Colors file not found', 404);
            }

            $colors = json_decode(File::get($jsonPath), true);
            
            // Get file modification time as version/timestamp
            $lastModified = File::lastModified($jsonPath);
            
            // Get local version from request (if app sends it)
            $localVersion = $request->get('version');
            
            return $this->success([
                'colors' => $colors,
                'version' => $lastModified,
                'needs_sync' => $localVersion != $lastModified,
                'timestamp' => date('Y-m-d H:i:s', $lastModified),
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to sync colors: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Convert hex color to RGB
     */
    private function hexToRgb($hex): string
    {
        // Handle hex with alpha
        if (strlen($hex) > 7) {
            $hex = substr($hex, 0, 7);
        }

        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgb($r, $g, $b)";
    }
}
