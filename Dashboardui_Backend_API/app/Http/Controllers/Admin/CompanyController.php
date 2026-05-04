<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CompanyController extends Controller
{
    public function index()
    {
        return view('admin.assets.company');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $path = base_path('jsonassets/company.json');
            
            if (!file_exists($path)) {
                return response()->json([
                    'success' => false, 
                    'message' => __('messages.company_file_not_found')
                ], 404);
            }

            $company = json_decode(file_get_contents($path), true);

            if (!$company) {
                return response()->json([
                    'success' => false, 
                    'message' => __('messages.company_file_parse_error')
                ], 500);
            }

            $oldName = $company['name'] ?? 'Technodec';
            $company['name'] = $validated['name'];
            $company['tagline'] = $validated['tagline'] ?? $company['tagline'] ?? 'Admin Panel';
            $company['description'] = $validated['description'] ?? $company['description'] ?? '';

            $json = json_encode($company, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if (file_put_contents($path, $json) === false) {
                return response()->json([
                    'success' => false, 
                    'message' => __('messages.company_file_write_error')
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.company_updated_success'),
                'company' => $company
            ]);

        } catch (\Exception $e) {
            \Log::error('Company update failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false, 
                'message' => __('messages.company_update_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

