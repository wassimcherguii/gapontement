<?php

namespace App\Services;

use App\Models\LandingEntity;
use App\Models\LandingNavItem;
use App\Models\LandingPage;
use App\Models\LandingSection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LandingPageJsonImportService
{
    /** @var array<string, int> */
    private const SECTION_SORT = [
        'top_bar' => 0,
        'hero' => 1,
        'about' => 2,
        'departments' => 3,
        'featured_doctors' => 4,
        'quick_booking' => 5,
        'why_us' => 6,
        'testimonials' => 7,
        'blog' => 8,
        'contact' => 9,
        'cta' => 10,
        'footer' => 11,
    ];

    /**
     * Apply one locale bundle (same shape as page-cache JSON) into the database.
     *
     * @param  array<string, mixed>  $bundle
     */
    public function importLocaleBundle(LandingPage $page, string $locale, array $bundle): void
    {
        DB::transaction(function () use ($page, $locale, $bundle): void {
            $this->importMeta($page, $locale, $bundle);
            $this->importSectionContent($page, $locale, 'top_bar', Arr::get($bundle, 'topBar'));
            $this->importSectionContent($page, $locale, 'hero', Arr::get($bundle, 'hero'));
            $this->importSectionContent($page, $locale, 'about', Arr::get($bundle, 'about'));
            $this->importSectionContent($page, $locale, 'cta', Arr::get($bundle, 'cta'));
            $this->importSectionContent($page, $locale, 'contact', Arr::get($bundle, 'contact'));
            $this->importSectionContent($page, $locale, 'footer', Arr::get($bundle, 'footer'));
            $this->importQuickBooking($page, $locale, Arr::get($bundle, 'quickBooking', []));
            $this->importWhyUs($page, $locale, Arr::get($bundle, 'whyUs', []));
            $this->importEntities($page, $locale, 'departments', Arr::get($bundle, 'departments', null));
            $this->importEntities($page, $locale, 'featured_doctors', Arr::get($bundle, 'featuredDoctors', null));
            $this->importEntities($page, $locale, 'testimonials', Arr::get($bundle, 'testimonials', null));
            $this->importNav($page, $locale, Arr::get($bundle, 'nav', null));
        });
    }

    /**
     * @param  array<string, mixed>  $bundle
     */
    private function importMeta(LandingPage $page, string $locale, array $bundle): void
    {
        $meta = Arr::get($bundle, 'meta');
        if (! is_array($meta)) {
            return;
        }
        $page->locales()->updateOrCreate(
            ['locale' => $locale],
            [
                'meta_title' => Arr::get($meta, 'title'),
                'meta_description' => Arr::get($meta, 'description'),
            ]
        );
    }

    /**
     * @param  array<string, mixed>|null  $content
     */
    private function importSectionContent(LandingPage $page, string $locale, string $sectionKey, ?array $content): void
    {
        if ($content === null || $content === []) {
            return;
        }
        $section = $this->sectionOrCreate($page, $sectionKey);
        $tr = $section->translations()->firstOrNew(['locale' => $locale]);
        $tr->content = array_merge(is_array($tr->content) ? $tr->content : [], $content);
        $tr->save();
    }

    /**
     * @param  array<string, mixed>  $quickBooking
     */
    private function importQuickBooking(LandingPage $page, string $locale, array $quickBooking): void
    {
        if ($quickBooking === []) {
            return;
        }
        $section = $this->sectionOrCreate($page, 'quick_booking');
        $copy = Arr::get($quickBooking, 'copy', []);
        $settings = Arr::except($quickBooking, ['copy']);
        if ($settings !== []) {
            $section->update([
                'settings' => array_merge($section->settings ?? [], $settings),
            ]);
        }
        if (is_array($copy) && $copy !== []) {
            $tr = $section->translations()->firstOrNew(['locale' => $locale]);
            $tr->content = array_merge(is_array($tr->content) ? $tr->content : [], $copy);
            $tr->save();
        }
    }

    /**
     * @param  array<string, mixed>  $whyUs
     */
    private function importWhyUs(LandingPage $page, string $locale, array $whyUs): void
    {
        $section = $this->sectionOrCreate($page, 'why_us');
        $title = Arr::get($whyUs, 'title');
        $subtitle = Arr::get($whyUs, 'subtitle');
        if ($title !== null || $subtitle !== null) {
            $tr = $section->translations()->firstOrNew(['locale' => $locale]);
            $base = is_array($tr->content) ? $tr->content : [];
            if ($title !== null) {
                $base['title'] = $title;
            }
            if ($subtitle !== null) {
                $base['subtitle'] = $subtitle;
            }
            $tr->content = $base;
            $tr->save();
        }
        if (array_key_exists('items', $whyUs) && is_array($whyUs['items'])) {
            $this->syncEntityRows($page, $locale, 'why_us', $whyUs['items']);
        }
    }

    /**
     * @param  list<array<string, mixed>>|null  $rows
     */
    private function importEntities(LandingPage $page, string $locale, string $sectionKey, ?array $rows): void
    {
        if ($rows === null) {
            return;
        }
        $this->syncEntityRows($page, $locale, $sectionKey, $rows);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function syncEntityRows(LandingPage $page, string $locale, string $sectionKey, array $rows): void
    {
        $section = $this->sectionOrCreate($page, $sectionKey);

        foreach ($rows as $i => $row) {
            if (! is_array($row)) {
                continue;
            }
            $entity = LandingEntity::query()
                ->where('landing_section_id', $section->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->skip($i)
                ->first();
            if (! $entity) {
                $entity = LandingEntity::query()->create([
                    'landing_section_id' => $section->id,
                    'sort_order' => $i,
                    'type' => (string) ($row['type'] ?? 'feature'),
                    'slug' => $row['slug'] ?? null,
                    'image_path' => $row['image'] ?? $row['image_path'] ?? null,
                    'href' => $row['href'] ?? null,
                    'user_id' => isset($row['user_id']) ? (int) $row['user_id'] : null,
                    'extra' => is_array($row['extra'] ?? null) ? $row['extra'] : null,
                ]);
            } else {
                $entity->fill([
                    'sort_order' => $i,
                    'type' => (string) ($row['type'] ?? $entity->type),
                    'slug' => array_key_exists('slug', $row) ? $row['slug'] : $entity->slug,
                    'image_path' => array_key_exists('image', $row) ? $row['image'] : (array_key_exists('image_path', $row) ? $row['image_path'] : $entity->image_path),
                    'href' => array_key_exists('href', $row) ? $row['href'] : $entity->href,
                    'user_id' => array_key_exists('user_id', $row) ? ($row['user_id'] !== null ? (int) $row['user_id'] : null) : $entity->user_id,
                    'extra' => array_key_exists('extra', $row) ? (is_array($row['extra']) ? $row['extra'] : $entity->extra) : $entity->extra,
                ]);
                $entity->save();
            }

            $entity->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $row['title'] ?? null,
                    'subtitle' => $row['subtitle'] ?? null,
                    'body' => $row['body'] ?? null,
                    'cta_label' => $row['cta_label'] ?? null,
                ]
            );
        }

        $ids = $section->entities()->orderBy('sort_order')->orderBy('id')->pluck('id');
        $drop = $ids->slice(count($rows))->values()->all();
        if ($drop !== []) {
            LandingEntity::query()->whereIn('id', $drop)->delete();
        }
    }

    /**
     * @param  list<array<string, mixed>>|null  $items
     */
    private function importNav(LandingPage $page, string $locale, ?array $items): void
    {
        if ($items === null || $items === []) {
            return;
        }
        $allLocales = array_keys(get_supported_languages());

        foreach ($items as $i => $item) {
            if (! is_array($item)) {
                continue;
            }
            $nav = LandingNavItem::query()
                ->where('landing_page_id', $page->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->skip($i)
                ->first();
            if (! $nav) {
                $nav = LandingNavItem::query()->create([
                    'landing_page_id' => $page->id,
                    'sort_order' => $i,
                    'href' => (string) ($item['href'] ?? '#'),
                    'route_key' => $item['route_key'] ?? null,
                    'is_visible' => (bool) ($item['is_visible'] ?? true),
                    'is_cta' => (bool) ($item['is_cta'] ?? false),
                    'icon' => $item['icon'] ?? null,
                ]);
                $label = isset($item['label']) && is_string($item['label']) ? $item['label'] : '';
                foreach ($allLocales as $loc) {
                    $nav->translations()->create([
                        'locale' => $loc,
                        'label' => $loc === $locale ? $label : '',
                    ]);
                }

                continue;
            }

            $nav->update([
                'sort_order' => $i,
                'href' => (string) ($item['href'] ?? $nav->href),
                'route_key' => array_key_exists('route_key', $item) ? $item['route_key'] : $nav->route_key,
                'is_visible' => array_key_exists('is_visible', $item) ? (bool) $item['is_visible'] : $nav->is_visible,
                'is_cta' => array_key_exists('is_cta', $item) ? (bool) $item['is_cta'] : $nav->is_cta,
                'icon' => array_key_exists('icon', $item) ? $item['icon'] : $nav->icon,
            ]);

            if (isset($item['label']) && is_string($item['label'])) {
                $nav->translations()->updateOrCreate(
                    ['locale' => $locale],
                    ['label' => $item['label']]
                );
            }
        }

        $navIds = $page->navItems()->orderBy('sort_order')->orderBy('id')->pluck('id');
        $dropNav = $navIds->slice(count($items))->values()->all();
        if ($dropNav !== []) {
            LandingNavItem::query()->whereIn('id', $dropNav)->delete();
        }
    }

    private function sectionOrCreate(LandingPage $page, string $sectionKey): LandingSection
    {
        $sort = self::SECTION_SORT[$sectionKey] ?? 99;

        return LandingSection::query()->firstOrCreate(
            [
                'landing_page_id' => $page->id,
                'section_key' => $sectionKey,
            ],
            [
                'sort_order' => $sort,
                'settings' => null,
            ]
        );
    }
}
