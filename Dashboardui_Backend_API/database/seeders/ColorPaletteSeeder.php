<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorPaletteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read colors from colors.json file
        $colorsJsonPath = base_path('jsonassets/colors.json');
        
        if (!file_exists($colorsJsonPath)) {
            $this->command->error('colors.json file not found at: ' . $colorsJsonPath);
            return;
        }
        
        $colorsData = json_decode(file_get_contents($colorsJsonPath), true);
        
        if (!$colorsData) {
            $this->command->error('Failed to parse colors.json file');
            return;
        }
        
        $sortOrder = 1;
        
        // Process light theme
        if (isset($colorsData['light'])) {
            foreach ($colorsData['light'] as $category => $colors) {
                foreach ($colors as $name => $hexValue) {
                    // Handle shadow colors (nested structure)
                    if (is_array($hexValue)) {
                        foreach ($hexValue as $shadowName => $shadowValue) {
                            $color = [
                                'name' => $shadowName,
                                'category' => $category,
                                'theme' => 'light',
                                'hex_value' => $shadowValue,
                                'rgb_value' => $this->hexToRgb($shadowValue),
                                'usage' => $this->getUsageForColor($category, $shadowName),
                                'description' => $this->getDescriptionForColor($category, $shadowName),
                                'sort_order' => $sortOrder++,
                            ];
                            
                            $this->createOrUpdateColor($color);
                        }
                    } else {
                        $color = [
                            'name' => $name,
                            'category' => $category,
                            'theme' => 'light',
                            'hex_value' => $hexValue,
                            'rgb_value' => $this->hexToRgb($hexValue),
                            'usage' => $this->getUsageForColor($category, $name),
                            'description' => $this->getDescriptionForColor($category, $name),
                            'sort_order' => $sortOrder++,
                        ];
                        
                        $this->createOrUpdateColor($color);
                    }
                }
            }
        }
        
        // Process dark theme
        if (isset($colorsData['dark'])) {
            foreach ($colorsData['dark'] as $category => $colors) {
                foreach ($colors as $name => $hexValue) {
                    // Handle shadow colors (nested structure)
                    if (is_array($hexValue)) {
                        foreach ($hexValue as $shadowName => $shadowValue) {
                            $color = [
                                'name' => $shadowName,
                                'category' => $category,
                                'theme' => 'dark',
                                'hex_value' => $shadowValue,
                                'rgb_value' => $this->hexToRgb($shadowValue),
                                'usage' => $this->getUsageForColor($category, $shadowName),
                                'description' => $this->getDescriptionForColor($category, $shadowName),
                                'sort_order' => $sortOrder++,
                            ];
                            
                            $this->createOrUpdateColor($color);
                        }
                    } else {
                        $color = [
                            'name' => $name,
                            'category' => $category,
                            'theme' => 'dark',
                            'hex_value' => $hexValue,
                            'rgb_value' => $this->hexToRgb($hexValue),
                            'usage' => $this->getUsageForColor($category, $name),
                            'description' => $this->getDescriptionForColor($category, $name),
                            'sort_order' => $sortOrder++,
                        ];
                        
                        $this->createOrUpdateColor($color);
                    }
                }
            }
        }
        
        $this->command->info('Color palette seeded successfully from colors.json');
    }
    
    /**
     * Convert hex color to RGB
     */
    private function hexToRgb($hex): string
    {
        // Handle hex with alpha (e.g., #94131D20)
        if (strlen($hex) > 7) {
            $hex = substr($hex, 0, 7);
        }
        
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 4, 2));
        $b = hexdec(substr($hex, 6, 2));
        
        return "rgb($r, $g, $b)";
    }
    
    /**
     * Get usage description for color
     */
    private function getUsageForColor($category, $name): string
    {
        $usageMap = [
            'brand' => 'buttons,logos,highlights',
            'complementary' => 'accent_elements',
            'neutral' => 'text,backgrounds',
            'shadows' => 'shadow_effects',
            'semantic' => 'ui_states',
            'usage' => 'general_usage',
        ];
        
        return $usageMap[$category] ?? 'general';
    }
    
    /**
     * Get description for color
     */
    private function getDescriptionForColor($category, $name): string
    {
        return ucfirst(str_replace('-', ' ', $name)) . ' color';
    }
    
    /**
     * Create or update color in database
     */
    private function createOrUpdateColor(array $color): void
    {
        \App\Models\ColorPalette::updateOrCreate(
            [
                'name' => $color['name'],
                'category' => $color['category'],
                'theme' => $color['theme']
            ],
            $color
        );
    }
}
