<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TranslationDomain;
use App\Services\TranslationDomainProvisioner;
use App\Services\TranslationDomainRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TranslationDomainController extends Controller
{
    public function __construct(
        private readonly TranslationDomainRegistry $registry,
        private readonly TranslationDomainProvisioner $provisioner
    ) {}

    public function index()
    {
        return view('admin.languages.translation-domains', [
            'domains' => TranslationDomain::query()->orderBy('sort_order')->orderBy('slug')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => [
                'required',
                'string',
                'max:64',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique('translation_domains', 'slug'),
            ],
            'name' => ['required', 'string', 'max:120'],
        ]);

        $this->provisioner->provision($validated['slug'], $validated['name']);

        return redirect()
            ->route('admin.assets.translation-domains.index', ['lang' => app()->getLocale()])
            ->with('success', get_translation('translation_domains_created'));
    }
}
