<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ColorPalette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class ColorController extends Controller
{
    /**
     * Display the color management page
     */
    public function index()
    {
        // Get colors from JSON file
        $jsonColors = $this->getJsonColors();
        
        // Get colors from database
        $dbColors = ColorPalette::ordered()->get()->groupBy(['theme', 'category']);
        
        return view('admin.assets.colors', compact('jsonColors', 'dbColors'));
    }
    
    /**
     * Get colors from colors.json file
     */
    private function getJsonColors()
    {
        $jsonPath = base_path('jsonassets/colors.json');
        
        if (File::exists($jsonPath)) {
            $content = File::get($jsonPath);
            return json_decode($content, true);
        }
        
        return ['light' => [], 'dark' => []];
    }
    
    /**
     * Update a color in the database
     */
    public function update(Request $request, $id = null)
    {
        // Get ID from route parameters - Laravel might be matching incorrectly
        $colorId = $id ?? $request->route('id');
        
        // If still not found, try to extract from path
        if (!$colorId || $colorId === 'en' || $colorId === 'fr' || $colorId === 'ar') {
            $path = $request->path();
            $segments = explode('/', $path);
            // Path should be: {lang}/admin/assets/colors/update/{id}
            // So ID should be the last segment
            $colorId = end($segments);
        }
        
        // Debug: Log what we're receiving
        \Log::info('Color update request', [
            'method_id' => $id,
            'route_id' => $request->route('id'),
            'route_lang' => $request->route('lang'),
            'extracted_id' => $colorId,
            'path' => $request->path(),
            'url' => $request->url(),
            'all_params' => $request->route()->parameters()
        ]);
        
        // Throttle request to prevent multiple clicks
        $cacheKey = 'color_update_' . $colorId;
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.request_in_progress')
            ], 429);
        }
        
        Cache::put($cacheKey, true, 5); // Lock for 5 seconds
        
        try {
            $request->validate([
                'hex_value' => 'required|regex:/^#[A-Fa-f0-9]{6,8}$/',
            ]);
            
            $color = ColorPalette::findOrFail($colorId);
            $color->hex_value = $request->hex_value;
            $color->rgb_value = $this->hexToRgb($request->hex_value);
            $color->save();
            
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => __('messages.color_updated_success'),
                'color' => $color
            ]);
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            
            \Log::error('Color update failed', [
                'method_id' => $id,
                'color_id' => $colorId,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('messages.color_update_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync database colors to JSON file
     */
    public function syncToJson(Request $request)
    {
        // Throttle request
        $cacheKey = 'color_sync_json';
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.request_in_progress')
            ], 429);
        }
        
        Cache::put($cacheKey, true, 10);
        
        try {
            // Get all colors from database grouped by theme and category
            $dbColors = ColorPalette::ordered()->get();
            
            $jsonData = [
                'light' => [],
                'dark' => []
            ];
            
            foreach ($dbColors as $color) {
                $theme = $color->theme;
                $category = $color->category;
                $name = $color->name;
                
                // Handle shadow colors (nested structure)
                if ($category === 'shadows') {
                    if (!isset($jsonData[$theme][$category])) {
                        $jsonData[$theme][$category] = [];
                    }
                    if (!isset($jsonData[$theme][$category]['primary'])) {
                        $jsonData[$theme][$category]['primary'] = [];
                    }
                    $jsonData[$theme][$category]['primary'][$name] = $color->hex_value;
                } else {
                    if (!isset($jsonData[$theme][$category])) {
                        $jsonData[$theme][$category] = [];
                    }
                    $jsonData[$theme][$category][$name] = $color->hex_value;
                }
            }
            
            // Write to JSON file
            $jsonPath = base_path('jsonassets/colors.json');
            File::put($jsonPath, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => __('messages.color_sync_success')
            ]);
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => false,
                'message' => __('messages.color_sync_failed') . ': ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Revert database colors from JSON file (opposite of syncToJson)
     */
    public function revertFromJson(Request $request)
    {
        // Throttle request
        $cacheKey = 'color_revert_json';
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.request_in_progress')
            ], 429);
        }
        
        Cache::put($cacheKey, true, 10);
        
        try {
            // Get colors from JSON file
            $jsonColors = $this->getJsonColors();
            
            if (empty($jsonColors) || (!isset($jsonColors['light']) && !isset($jsonColors['dark']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'JSON colors file is empty or invalid'
                ], 400);
            }
            
            $updatedCount = 0;
            $notFoundCount = 0;
            
            // Iterate through JSON structure and update database
            foreach (['light', 'dark'] as $theme) {
                if (!isset($jsonColors[$theme]) || !is_array($jsonColors[$theme])) {
                    continue;
                }
                
                foreach ($jsonColors[$theme] as $category => $colors) {
                    // Handle shadow colors (nested structure)
                    if ($category === 'shadows' && isset($colors['primary']) && is_array($colors['primary'])) {
                        foreach ($colors['primary'] as $name => $hexValue) {
                            $color = ColorPalette::where('theme', $theme)
                                ->where('category', $category)
                                ->where('name', $name)
                                ->first();
                            
                            if ($color) {
                                $color->hex_value = $hexValue;
                                $color->rgb_value = $this->hexToRgb($hexValue);
                                $color->save();
                                $updatedCount++;
                            } else {
                                $notFoundCount++;
                            }
                        }
                    } else {
                        // Regular categories
                        if (is_array($colors)) {
                            foreach ($colors as $name => $hexValue) {
                                $color = ColorPalette::where('theme', $theme)
                                    ->where('category', $category)
                                    ->where('name', $name)
                                    ->first();
                                
                                if ($color) {
                                    $color->hex_value = $hexValue;
                                    $color->rgb_value = $this->hexToRgb($hexValue);
                                    $color->save();
                                    $updatedCount++;
                                } else {
                                    $notFoundCount++;
                                }
                            }
                        }
                    }
                }
            }
            
            Cache::forget($cacheKey);
            
            $message = "Successfully reverted {$updatedCount} color(s) from JSON file";
            if ($notFoundCount > 0) {
                $message .= ". {$notFoundCount} color(s) not found in database";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'updated' => $updatedCount,
                'not_found' => $notFoundCount
            ]);
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            
            \Log::error('Color revert failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to revert colors: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get comparison between JSON and DB colors
     */
    public function getComparison()
    {
        $jsonColors = $this->getJsonColors();
        $dbColors = ColorPalette::ordered()->get();
        
        $comparison = [
            'different' => [],
            'same' => []
        ];
        
        foreach ($dbColors as $dbColor) {
            $theme = $dbColor->theme;
            $category = $dbColor->category;
            $name = $dbColor->name;
            $dbHex = $dbColor->hex_value;
            
            // Get JSON value
            $jsonHex = null;
            if ($category === 'shadows' && isset($jsonColors[$theme][$category]['primary'][$name])) {
                $jsonHex = $jsonColors[$theme][$category]['primary'][$name];
            } elseif (isset($jsonColors[$theme][$category][$name])) {
                $jsonHex = $jsonColors[$theme][$category][$name];
            }
            
            if ($jsonHex !== null) {
                if (strtolower($jsonHex) !== strtolower($dbHex)) {
                    $comparison['different'][] = [
                        'id' => $dbColor->id,
                        'theme' => $theme,
                        'category' => $category,
                        'name' => $name,
                        'db_value' => $dbHex,
                        'json_value' => $jsonHex
                    ];
                } else {
                    $comparison['same'][] = [
                        'id' => $dbColor->id,
                        'theme' => $theme,
                        'category' => $category,
                        'name' => $name,
                        'value' => $dbHex
                    ];
                }
            }
        }
        
        return response()->json($comparison);
    }
    
    /**
     * Get comparison from JSON to DB (reverse direction)
     * Shows what's in JSON that's different or missing in DB
     */
    public function getJsonComparison()
    {
        $jsonColors = $this->getJsonColors();
        $dbColors = ColorPalette::ordered()->get()->keyBy(function ($color) {
            return $color->theme . '|' . $color->category . '|' . $color->name;
        });
        
        $comparison = [
            'different' => [],
            'missing' => [],
            'same' => []
        ];
        
        // Iterate through JSON structure
        foreach (['light', 'dark'] as $theme) {
            if (!isset($jsonColors[$theme]) || !is_array($jsonColors[$theme])) {
                continue;
            }
            
            foreach ($jsonColors[$theme] as $category => $colors) {
                // Handle shadow colors (nested structure)
                if ($category === 'shadows' && isset($colors['primary']) && is_array($colors['primary'])) {
                    foreach ($colors['primary'] as $name => $jsonHex) {
                        $key = $theme . '|' . $category . '|' . $name;
                        $dbColor = $dbColors->get($key);
                        
                        if ($dbColor) {
                            if (strtolower($jsonHex) !== strtolower($dbColor->hex_value)) {
                                $comparison['different'][] = [
                                    'id' => $dbColor->id,
                                    'theme' => $theme,
                                    'category' => $category,
                                    'name' => $name,
                                    'json_value' => $jsonHex,
                                    'db_value' => $dbColor->hex_value
                                ];
                            } else {
                                $comparison['same'][] = [
                                    'id' => $dbColor->id,
                                    'theme' => $theme,
                                    'category' => $category,
                                    'name' => $name,
                                    'value' => $jsonHex
                                ];
                            }
                        } else {
                            $comparison['missing'][] = [
                                'theme' => $theme,
                                'category' => $category,
                                'name' => $name,
                                'json_value' => $jsonHex
                            ];
                        }
                    }
                } else {
                    // Regular categories
                    if (is_array($colors)) {
                        foreach ($colors as $name => $jsonHex) {
                            $key = $theme . '|' . $category . '|' . $name;
                            $dbColor = $dbColors->get($key);
                            
                            if ($dbColor) {
                                if (strtolower($jsonHex) !== strtolower($dbColor->hex_value)) {
                                    $comparison['different'][] = [
                                        'id' => $dbColor->id,
                                        'theme' => $theme,
                                        'category' => $category,
                                        'name' => $name,
                                        'json_value' => $jsonHex,
                                        'db_value' => $dbColor->hex_value
                                    ];
                                } else {
                                    $comparison['same'][] = [
                                        'id' => $dbColor->id,
                                        'theme' => $theme,
                                        'category' => $category,
                                        'name' => $name,
                                        'value' => $jsonHex
                                    ];
                                }
                            } else {
                                $comparison['missing'][] = [
                                    'theme' => $theme,
                                    'category' => $category,
                                    'name' => $name,
                                    'json_value' => $jsonHex
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        return response()->json($comparison);
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
        $g = hexdec(substr($hex, 4, 2));
        $b = hexdec(substr($hex, 6, 2));
        
        return "rgb($r, $g, $b)";
    }
}