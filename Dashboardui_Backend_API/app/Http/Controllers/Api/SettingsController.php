<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    use ApiResponse;

    /**
     * Get mobile app top-bar metadata (public)
     */
    public function appInfo()
    {
        try {
            $logo = Logo::where('name', 'Main Logo')->first();
            $logoUrl = null;

            if ($logo && !empty($logo->path)) {
                $logoUrl = url(Storage::url($logo->path));
            }

            return $this->success([
                'app_name' => config('app.name', 'Dashboard UI'),
                'logo_url' => $logoUrl,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch app info: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get company info from company.json (public)
     */
    public function company()
    {
        try {
            $company = $this->getCompany();
            return $this->success($company);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch company info: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get brand assets from brand-assets.json (public)
     */
    public function brandAssets()
    {
        try {
            $brand = $this->getBrandAssets();

            $logoUrl = null;
            if (!empty($brand['logo']['path'])) {
                $logoUrl = url('/' . ltrim($brand['logo']['path'], '/'));
            }

            return $this->success([
                'logo' => $brand['logo'] ?? null,
                'favicon' => $brand['favicon'] ?? null,
                'logo_url' => $logoUrl,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch brand assets: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get all settings
     */
    public function index()
    {
        try {
            $languages = $this->getLanguages();
            $colors = $this->getColors();

            return $this->success([
                'languages' => $languages,
                'colors' => $colors,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch settings: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get languages
     */
    public function languages()
    {
        try {
            $languages = $this->getLanguages();
            return $this->success($languages);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch languages: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get colors
     */
    public function colors()
    {
        try {
            $colors = $this->getColors();
            return $this->success($colors);
        } catch (\Exception $e) {
            return $this->error('Failed to fetch colors: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get languages from JSON file
     */
    private function getLanguages()
    {
        $path = base_path('jsonassets/languages.json');
        if (File::exists($path)) {
            return json_decode(File::get($path), true);
        }
        return ['default' => 'en', 'supported' => []];
    }

    /**
     * Get colors from JSON file
     */
    private function getColors()
    {
        $path = base_path('jsonassets/colors.json');
        if (File::exists($path)) {
            return json_decode(File::get($path), true);
        }
        return ['light' => [], 'dark' => []];
    }

    /**
     * Get company info from JSON file
     */
    private function getCompany()
    {
        $path = base_path('jsonassets/company.json');
        if (File::exists($path)) {
            return json_decode(File::get($path), true);
        }

        return [
            'name' => config('app.name', 'Dashboard UI'),
            'tagline' => '',
            'description' => '',
        ];
    }

    /**
     * Get brand assets from JSON file
     */
    private function getBrandAssets()
    {
        $path = base_path('jsonassets/brand-assets.json');
        if (File::exists($path)) {
            return json_decode(File::get($path), true);
        }

        return [
            'logo' => null,
            'favicon' => null,
        ];
    }
}
