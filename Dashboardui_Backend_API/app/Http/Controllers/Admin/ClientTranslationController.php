<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TranslationDomain;
use App\Models\TranslationKey;
use App\Services\TranslationCoverageService;
use App\Services\TranslationDomainRegistry;
use App\Services\TranslationPublishService;
use App\Services\TranslationSyncDiffService;
use App\Services\TranslationSyncService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientTranslationController extends Controller
{
    public function __construct(
        private readonly TranslationPublishService $publishService,
        private readonly TranslationSyncService $syncService,
        private readonly TranslationSyncDiffService $syncDiffService,
        private readonly TranslationCoverageService $coverageService,
        private readonly TranslationDomainRegistry $domainRegistry
    ) {}

    public function index(Request $request)
    {
        $domain = $this->resolveDomain($request->query('domain'));
        $languages = $this->publishService->readLanguages($domain, false);
        $locales = array_keys($languages['supported'] ?? []);
        $localeCoverage = $this->coverageService->localeCoverage($domain, $languages['supported'] ?? []);

        $translationKeys = TranslationKey::query()
            ->domain($domain)
            ->with('values')
            ->orderBy('key')
            ->paginate(25)
            ->withQueryString();

        $flatJsonByLocale = [];
        foreach ($locales as $loc) {
            $flatJsonByLocale[$loc] = $this->syncService->flattenDotKeys(
                $this->publishService->readBundle($domain, $loc)
            );
        }

        $jsonMatrix = [];
        foreach ($translationKeys as $tk) {
            $entry = ['key' => $tk->key, 'locales' => []];
            foreach ($locales as $loc) {
                $entry['locales'][$loc] = $flatJsonByLocale[$loc][$tk->key] ?? '';
            }
            $jsonMatrix[] = $entry;
        }

        $syncMeta = $this->publishService->readMeta($domain);
        $diskChecksum = $syncMeta['checksum'] ?? null;
        $computedChecksum = $this->publishService->computePublishedBundleChecksum($domain);
        $publishedInSync = is_string($diskChecksum) && $diskChecksum !== ''
            && hash_equals($diskChecksum, $computedChecksum);
        $hasDraftKeys = TranslationKey::query()->domain($domain)->where('status', 'draft')->exists();

        return view('admin.languages.client-translations', [
            'domain' => $domain,
            'domains' => $this->domainRegistry->orderedDomains(),
            'locales' => $locales,
            'localeCoverage' => $localeCoverage,
            'supportedLanguageCount' => count($locales),
            'translationKeys' => $translationKeys,
            'jsonMatrix' => $jsonMatrix,
            'syncMeta' => $syncMeta,
            'syncState' => [
                'published_in_sync' => $publishedInSync,
                'has_meta_checksum' => is_string($diskChecksum) && $diskChecksum !== '',
                'has_draft_keys' => $hasDraftKeys,
                'computed_checksum' => $computedChecksum,
            ],
        ]);
    }

    public function languagesCatalog(Request $request)
    {
        $domain = $this->resolveDomain($request->query('domain'));
        $languages = $this->publishService->readLanguages($domain, false);

        return view('admin.languages.translation-domain-catalog', [
            'domain' => $domain,
            'domains' => $this->domainRegistry->orderedDomains(),
            'languages' => $languages,
        ]);
    }

    public function languagesCatalogUpdate(Request $request)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:64', Rule::exists('translation_domains', 'slug')],
            'default' => 'required|string|max:20|regex:/^[a-z0-9_-]+$/',
            'locale_codes' => 'required|array|min:1',
            'locale_codes.*' => 'required|string|max:20|regex:/^[a-z0-9_-]+$/',
            'locale_name' => 'required|array',
            'locale_native' => 'required|array',
            'locale_direction' => 'required|array',
            'locale_flag' => 'nullable|array',
            'locale_active' => 'nullable|array',
            'new_locale_code' => 'nullable|string|max:20|regex:/^[a-z0-9_-]+$/',
            'new_locale_name' => 'nullable|string|max:120',
            'new_locale_native' => 'nullable|string|max:120',
            'new_locale_direction' => 'nullable|in:ltr,rtl',
            'new_locale_flag' => 'nullable|string|max:10',
        ]);

        $domain = $validated['domain'];
        $codes = array_values(array_unique($validated['locale_codes']));

        $newCode = isset($validated['new_locale_code']) ? trim((string) $validated['new_locale_code']) : '';
        if ($newCode !== '') {
            if (in_array($newCode, $codes, true)) {
                return back()->withErrors(['new_locale_code' => __('messages.client_translations_catalog_duplicate')])->withInput();
            }
            $newName = trim((string) ($validated['new_locale_name'] ?? ''));
            $newNative = trim((string) ($validated['new_locale_native'] ?? ''));
            if ($newName === '' || $newNative === '') {
                return back()->withErrors(['new_locale_name' => __('messages.client_translations_catalog_new_required')])->withInput();
            }
            $codes[] = $newCode;
            $validated['locale_name'][$newCode] = $newName;
            $validated['locale_native'][$newCode] = $newNative;
            $validated['locale_direction'][$newCode] = $validated['new_locale_direction'] ?? 'ltr';
            if (! empty($validated['new_locale_flag'])) {
                $validated['locale_flag'][$newCode] = $validated['new_locale_flag'];
            }
        }

        if (! in_array($validated['default'], $codes, true)) {
            return back()->withErrors(['default' => __('messages.client_translations_catalog_default_invalid')])->withInput();
        }

        foreach ($codes as $code) {
            $nm = trim((string) ($request->input("locale_name.{$code}", '')));
            $nt = trim((string) ($request->input("locale_native.{$code}", '')));
            if ($nm === '' || $nt === '') {
                return back()->withErrors(["locale_name.{$code}" => __('messages.client_translations_catalog_name_required')])->withInput();
            }
        }

        $supported = [];
        foreach ($codes as $code) {
            $name = $validated['locale_name'][$code] ?? '';
            $native = $validated['locale_native'][$code] ?? '';
            $direction = $validated['locale_direction'][$code] ?? 'ltr';
            $flag = $validated['locale_flag'][$code] ?? null;
            $active = $request->boolean("locale_active.{$code}");

            if (! in_array($direction, ['ltr', 'rtl'], true)) {
                return back()->withErrors(["locale_direction.{$code}" => __('messages.client_translations_catalog_direction_invalid')])->withInput();
            }

            $entry = [
                'code' => $code,
                'name' => (string) $name,
                'native' => (string) $native,
                'direction' => $direction,
            ];
            if ($flag !== null && $flag !== '') {
                $entry['flag'] = (string) $flag;
            }
            if (! $active) {
                $entry['active'] = false;
            }
            $supported[$code] = $entry;
        }

        $payload = [
            'default' => $validated['default'],
            'supported' => $supported,
        ];

        $this->publishService->writePublicLanguages($domain, $payload);

        return redirect()
            ->route('admin.assets.client-translations.languages-catalog', ['lang' => app()->getLocale(), 'domain' => $domain])
            ->with('success', __('messages.client_translations_catalog_saved'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:64', Rule::exists('translation_domains', 'slug')],
            'key' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'values' => 'nullable|array',
        ]);

        $domainRow = TranslationDomain::query()->where('slug', $validated['domain'])->firstOrFail();

        $translationKey = TranslationKey::query()->updateOrCreate(
            [
                'translation_domain_id' => $domainRow->id,
                'key' => trim($validated['key']),
            ],
            [
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]
        );

        foreach (($validated['values'] ?? []) as $locale => $value) {
            $translationKey->values()->updateOrCreate(
                ['locale' => $locale],
                [
                    'value' => $value,
                    'status' => $validated['status'],
                ]
            );
        }

        $this->publishService->exportDomain($validated['domain']);

        return redirect()
            ->route('admin.assets.client-translations.index', ['lang' => app()->getLocale(), 'domain' => $validated['domain']])
            ->with('success', __('messages.client_translations_saved'));
    }

    public function syncToJson(Request $request)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:64', Rule::exists('translation_domains', 'slug')],
        ]);

        $result = $this->publishService->exportDomain($validated['domain']);

        return redirect()
            ->route('admin.assets.client-translations.index', ['lang' => app()->getLocale(), 'domain' => $validated['domain']])
            ->with('success', __('messages.client_translations_published', ['count' => $result['count']]));
    }

    public function syncFromJson(Request $request)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:64', Rule::exists('translation_domains', 'slug')],
        ]);

        $languages = $this->publishService->readLanguages($validated['domain'], false);
        $locales = array_keys($languages['supported'] ?? []);
        $result = $this->syncService->importDomainFromJson($validated['domain'], $locales, $this->publishService);

        return redirect()
            ->route('admin.assets.client-translations.index', ['lang' => app()->getLocale(), 'domain' => $validated['domain']])
            ->with('success', __('messages.client_translations_imported', ['count' => $result['count']]));
    }

    public function syncDiff(Request $request)
    {
        $domain = $this->resolveDomain($request->query('domain'));

        return response()->json($this->syncDiffService->summarize($domain));
    }

    private function resolveDomain(?string $domain): string
    {
        $allowed = $this->domainRegistry->allowedSlugs();

        if ($domain !== null && $domain !== '' && in_array($domain, $allowed, true)) {
            return $domain;
        }

        return $allowed[0] ?? 'web';
    }

    public function updateKey(Request $request, string $lang, TranslationKey $translation_key)
    {
        $translation_key->load('translationDomain');
        $slug = $translation_key->translationDomain->slug;

        $catalogLocales = array_keys($this->publishService->readLanguages($slug, false)['supported'] ?? []);

        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:64', Rule::in([$slug])],
            'description' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'values' => 'nullable|array',
            'values.*' => 'nullable|string',
        ]);

        $translation_key->update([
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        foreach (($validated['values'] ?? []) as $locale => $value) {
            if (! in_array($locale, $catalogLocales, true)) {
                continue;
            }
            $translation_key->values()->updateOrCreate(
                ['locale' => $locale],
                [
                    'value' => $value,
                    'status' => $validated['status'],
                ]
            );
        }

        $this->publishService->exportDomain($slug);

        $params = ['lang' => app()->getLocale(), 'domain' => $slug];
        if ($request->filled('page')) {
            $params['page'] = (int) $request->input('page');
        }

        return redirect()
            ->route('admin.assets.client-translations.index', $params)
            ->with('success', __('messages.client_translations_key_saved_and_published'));
    }

    public function updateJsonKey(Request $request, string $lang)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:64', Rule::exists('translation_domains', 'slug')],
            'key' => 'required|string|max:255',
            'values' => 'required|array',
            'values.*' => 'nullable|string',
        ]);

        $domain = $validated['domain'];
        $dotKey = trim($validated['key']);
        $catalogLocales = array_keys($this->publishService->readLanguages($domain, false)['supported'] ?? []);

        $localePayload = [];
        foreach ($validated['values'] as $locale => $raw) {
            if (! is_string($locale) || ! in_array($locale, $catalogLocales, true)) {
                continue;
            }
            $localePayload[$locale] = $raw === '' || $raw === null ? null : (string) $raw;
        }

        if ($localePayload === []) {
            return back()->withErrors(['values' => __('messages.client_translations_json_values_required')])->withInput();
        }

        $this->publishService->mergeDotKeyIntoLocaleBundles($domain, $dotKey, $localePayload, $this->syncService);

        $languages = $this->publishService->readLanguages($domain, false);
        $locales = array_keys($languages['supported'] ?? []);
        $this->syncService->importDomainFromJson($domain, $locales, $this->publishService);

        $params = ['lang' => app()->getLocale(), 'domain' => $domain];
        if ($request->filled('page')) {
            $params['page'] = (int) $request->input('page');
        }

        return redirect()
            ->route('admin.assets.client-translations.index', $params)
            ->with('success', __('messages.client_translations_json_key_saved_and_imported'));
    }
}
