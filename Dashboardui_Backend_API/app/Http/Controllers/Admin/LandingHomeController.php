<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingEntity;
use App\Models\LandingNavItem;
use App\Models\LandingPage;
use App\Services\LandingPageJsonImportService;
use App\Services\LandingPagePublishService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use JsonException;

class LandingHomeController extends Controller
{
    private const SLUG = 'home';

    public function edit(): View
    {
        $page = LandingPage::query()->firstOrCreate(
            ['slug' => self::SLUG],
            []
        );
        $page->load([
            'locales',
            'navItems.translations',
            'sections.translations',
            'sections.entities.translations',
        ]);

        $locales = array_keys(get_supported_languages());
        $publisher = app(LandingPagePublishService::class);
        $jsonByLocale = [];
        foreach ($locales as $loc) {
            $path = $publisher->pathForLocale(self::SLUG, $loc);
            $jsonByLocale[$loc] = $this->landingJsonEditorSource($publisher, $page, $loc, $path);
        }

        if (old('locale') && old('json_payload')) {
            $ol = (string) old('locale');
            if (in_array($ol, $locales, true)) {
                try {
                    $parsed = json_decode((string) old('json_payload'), true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($parsed)) {
                        $jsonByLocale[$ol] = (string) old('json_payload');
                    }
                } catch (JsonException) {
                    // keep file/compose source
                }
            }
        }

        $jsonBundlesByLocale = [];
        foreach ($locales as $loc) {
            try {
                $decoded = json_decode($jsonByLocale[$loc], true, 512, JSON_THROW_ON_ERROR);
                $jsonBundlesByLocale[$loc] = is_array($decoded) ? $decoded : [];
            } catch (JsonException) {
                $jsonBundlesByLocale[$loc] = [];
            }
        }

        return view('admin.website.home', [
            'page' => $page,
            'locales' => $locales,
            'titleKey' => 'website_home_page',
            'jsonByLocale' => $jsonByLocale,
            'jsonBundlesByLocale' => $jsonBundlesByLocale,
        ]);
    }

    /**
     * Raw JSON for the admin JSON tab: on-disk cache if valid, otherwise a fresh bundle from the DB.
     */
    private function landingJsonEditorSource(
        LandingPagePublishService $publisher,
        LandingPage $page,
        string $locale,
        string $path
    ): string {
        if (File::exists($path)) {
            $raw = File::get($path);
            try {
                json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

                return $raw;
            } catch (JsonException) {
                // fall through
            }
        }

        return json_encode(
            $publisher->composeLocaleBundle($page, $locale),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    public function update(Request $request): RedirectResponse
    {
        $locales = array_keys(get_supported_languages());
        $page = LandingPage::query()->firstOrCreate(
            ['slug' => self::SLUG],
            []
        );

        $validated = $request->validate([
            'page_meta' => 'nullable:array',
            'page_meta.*.meta_title' => 'nullable|string|max:255',
            'page_meta.*.meta_description' => 'nullable|string|max:2000',
            'content' => 'nullable:array',
            'nav' => 'nullable:array',
            'entities' => 'nullable:array',
        ]);

        DB::transaction(function () use ($validated, $page, $locales, $request): void {
            foreach ($validated['page_meta'] ?? [] as $locale => $meta) {
                if (! in_array($locale, $locales, true) || ! is_array($meta)) {
                    continue;
                }
                $page->locales()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'meta_title' => $meta['meta_title'] ?? null,
                        'meta_description' => $meta['meta_description'] ?? null,
                    ]
                );
            }

            foreach ($validated['content'] ?? [] as $sectionKey => $byLocale) {
                $section = $page->sections()->where('section_key', $sectionKey)->first();
                if (! $section || ! is_array($byLocale)) {
                    continue;
                }
                foreach ($byLocale as $locale => $fields) {
                    if (! in_array($locale, $locales, true) || ! is_array($fields)) {
                        continue;
                    }
                    $clean = [];
                    foreach ($fields as $k => $v) {
                        if (is_string($k) && is_scalar($v)) {
                            $clean[$k] = $v;
                        }
                    }
                    if ($clean === []) {
                        continue;
                    }
                    $tr = $section->translations()->firstOrNew(['locale' => $locale]);
                    $tr->content = array_merge($tr->content ?? [], $clean);
                    $tr->save();
                }
            }

            $qb = $page->sections()->where('section_key', 'quick_booking')->first();
            if ($qb && $request->has('quick_booking_enabled')) {
                $settings = $qb->settings ?? [];
                $settings['enabled'] = $request->boolean('quick_booking_enabled');
                $qb->update(['settings' => $settings]);
            }

            foreach ($validated['nav'] ?? [] as $id => $payload) {
                if (! is_array($payload)) {
                    continue;
                }
                /** @var LandingNavItem|null $nav */
                $nav = $page->navItems()->where('id', (int) $id)->first();
                if (! $nav) {
                    continue;
                }
                $nav->fill([
                    'href' => isset($payload['href']) ? (string) $payload['href'] : $nav->href,
                    'is_visible' => array_key_exists('is_visible', $payload)
                        ? filter_var($payload['is_visible'], FILTER_VALIDATE_BOOLEAN)
                        : $nav->is_visible,
                    'is_cta' => array_key_exists('is_cta', $payload)
                        ? filter_var($payload['is_cta'], FILTER_VALIDATE_BOOLEAN)
                        : $nav->is_cta,
                    'route_key' => array_key_exists('route_key', $payload) ? ($payload['route_key'] !== '' ? (string) $payload['route_key'] : null) : $nav->route_key,
                    'icon' => array_key_exists('icon', $payload) ? ($payload['icon'] !== '' ? (string) $payload['icon'] : null) : $nav->icon,
                ]);
                $nav->save();

                foreach ($payload['labels'] ?? [] as $locale => $label) {
                    if (! in_array($locale, $locales, true)) {
                        continue;
                    }
                    $nav->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['label' => (string) $label]
                    );
                }
            }

            foreach ($validated['entities'] ?? [] as $id => $payload) {
                if (! is_array($payload)) {
                    continue;
                }
                /** @var LandingEntity|null $entity */
                $entity = LandingEntity::query()
                    ->where('id', (int) $id)
                    ->whereHas('section', fn ($q) => $q->where('landing_page_id', $page->id))
                    ->first();
                if (! $entity) {
                    continue;
                }
                $entity->fill([
                    'sort_order' => isset($payload['sort_order']) ? (int) $payload['sort_order'] : $entity->sort_order,
                    'slug' => array_key_exists('slug', $payload) ? ($payload['slug'] !== '' ? (string) $payload['slug'] : null) : $entity->slug,
                    'image_path' => array_key_exists('image_path', $payload) ? ($payload['image_path'] !== '' ? (string) $payload['image_path'] : null) : $entity->image_path,
                    'href' => array_key_exists('href', $payload) ? ($payload['href'] !== '' ? (string) $payload['href'] : null) : $entity->href,
                ]);
                $entity->save();

                foreach ($payload['t'] ?? [] as $locale => $t) {
                    if (! in_array($locale, $locales, true) || ! is_array($t)) {
                        continue;
                    }
                    $entity->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'title' => $t['title'] ?? null,
                            'subtitle' => $t['subtitle'] ?? null,
                            'body' => $t['body'] ?? null,
                            'cta_label' => $t['cta_label'] ?? null,
                        ]
                    );
                }
            }
        });

        return redirect()
            ->to(route_with_lang('admin.website.landing'))
            ->with('status', get_translation('saved') ?? 'Saved.');
    }

    public function publish(LandingPagePublishService $publisher): RedirectResponse
    {
        $result = $publisher->publish(self::SLUG);

        return redirect()
            ->to(route_with_lang('admin.website.landing'))
            ->with('status', get_translation('landing_cms_publish_done'))
            ->with('landing_publish_checksum', $result['meta']['checksum'] ?? null);
    }

    /**
     * Writes the current database landing content to page-cache JSON (same as Publish).
     */
    public function syncDbToJson(Request $request, LandingPagePublishService $publisher): RedirectResponse
    {
        $result = $publisher->publish(self::SLUG);
        $tab = $this->landingAdminReturnTab($request);

        return redirect()
            ->to(route_with_lang('admin.website.landing').'?tab='.$tab)
            ->with('status', get_translation('landing_cms_publish_done'))
            ->with('landing_publish_checksum', $result['meta']['checksum'] ?? null);
    }

    /**
     * Write one locale bundle from the JSON tab editor to jsonassets/page-cache (no DB write).
     */
    public function saveJsonToPageCache(Request $request, LandingPagePublishService $publisher): RedirectResponse
    {
        $locales = array_keys(get_supported_languages());
        $validator = Validator::make($request->all(), [
            'locale' => 'required|string|max:10',
            'json_payload' => 'required|string|max:512000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        if (! in_array($validated['locale'], $locales, true)) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->with('status', get_translation('landing_cms_json_invalid'))
                ->withInput();
        }

        try {
            $data = json_decode($validated['json_payload'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->with('status', get_translation('landing_cms_json_invalid'))
                ->withInput();
        }

        if (! is_array($data)) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->with('status', get_translation('landing_cms_json_invalid'))
                ->withInput();
        }

        File::ensureDirectoryExists(base_path('jsonassets/page-cache'));
        File::put(
            $publisher->pathForLocale(self::SLUG, $validated['locale']),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
        $publisher->refreshMetaFromDisk(self::SLUG, $locales);

        return redirect()
            ->to(route_with_lang('admin.website.landing').'?tab=json')
            ->with('status', get_translation('landing_cms_json_saved_to_disk'));
    }

    /**
     * Import every existing home.{locale}.json from page-cache into the database.
     */
    public function importJsonFromFiles(LandingPageJsonImportService $importer, LandingPagePublishService $publisher): RedirectResponse
    {
        $locales = array_keys(get_supported_languages());
        $imported = 0;

        foreach ($locales as $locale) {
            $path = $publisher->pathForLocale(self::SLUG, $locale);
            if (! File::exists($path)) {
                continue;
            }
            try {
                $data = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }
            if (! is_array($data)) {
                continue;
            }
            $page = LandingPage::query()->firstOrCreate(
                ['slug' => self::SLUG],
                []
            );
            $page->load([
                'locales',
                'navItems.translations',
                'sections.translations',
                'sections.entities.translations',
            ]);
            $importer->importLocaleBundle($page, $locale, $data);
            $imported++;
        }

        $message = $imported > 0
            ? get_translation('landing_cms_json_imported_files', ['count' => $imported])
            : get_translation('landing_cms_json_imported_files_none');

        return redirect()
            ->to(route_with_lang('admin.website.landing').'?tab=db')
            ->with('status', $message);
    }

    public function importJson(Request $request, LandingPageJsonImportService $importer): RedirectResponse
    {
        $locales = array_keys(get_supported_languages());
        $validator = Validator::make($request->all(), [
            'locale' => 'required|string|max:10',
            'json_payload' => 'required|string|max:512000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        if (! in_array($validated['locale'], $locales, true)) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->with('status', get_translation('landing_cms_json_invalid'))
                ->withInput();
        }

        try {
            $data = json_decode($validated['json_payload'], true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->with('status', get_translation('landing_cms_json_invalid'))
                ->withInput();
        }

        if (! is_array($data)) {
            return redirect()
                ->to(route_with_lang('admin.website.landing').'?tab=json')
                ->with('status', get_translation('landing_cms_json_invalid'))
                ->withInput();
        }

        $page = LandingPage::query()->firstOrCreate(
            ['slug' => self::SLUG],
            []
        );

        $importer->importLocaleBundle($page, $validated['locale'], $data);

        $tab = $this->landingAdminReturnTab($request);

        return redirect()
            ->to(route_with_lang('admin.website.landing').'?tab='.$tab)
            ->with('status', get_translation('landing_cms_json_imported'));
    }

    private function landingAdminReturnTab(Request $request): string
    {
        $tab = $request->query('return_tab') ?? $request->input('return_tab') ?? 'json';

        return in_array($tab, ['db', 'json'], true) ? $tab : 'json';
    }
}
