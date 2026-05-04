<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingEntity;
use App\Models\LandingNavItem;
use App\Models\LandingPage;
use App\Services\LandingPagePublishService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LandingHomeController extends Controller
{
    private const SLUG = 'home';

    public function edit(): View
    {
        $page = LandingPage::query()
            ->where('slug', self::SLUG)
            ->with([
                'locales',
                'navItems.translations',
                'sections.translations',
                'sections.entities.translations',
            ])
            ->firstOrFail();

        $locales = array_keys(get_supported_languages());

        return view('admin.website.home', [
            'page' => $page,
            'locales' => $locales,
            'titleKey' => 'website_home_page',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $locales = array_keys(get_supported_languages());
        $page = LandingPage::query()->where('slug', self::SLUG)->firstOrFail();

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
            ->to(route_with_lang('admin.website.page', ['page' => self::SLUG]))
            ->with('status', get_translation('saved') ?? 'Saved.');
    }

    public function publish(LandingPagePublishService $publisher): RedirectResponse
    {
        $result = $publisher->publish(self::SLUG);

        return redirect()
            ->to(route_with_lang('admin.website.page', ['page' => self::SLUG]))
            ->with('landing_publish_checksum', $result['meta']['checksum'] ?? null);
    }
}
