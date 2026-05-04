<?php

namespace App\Services;

use App\Models\LandingEntity;
use App\Models\LandingPage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

class LandingPagePublishService
{
    public function pathForLocale(string $slug, string $locale): string
    {
        return base_path("jsonassets/page-cache/{$slug}.{$locale}.json");
    }

    public function metaPath(string $slug): string
    {
        return base_path("jsonassets/page-cache/{$slug}._meta.json");
    }

    /**
     * @return array{locales: list<string>, meta: array<string, mixed>}
     */
    public function publish(string $slug): array
    {
        $page = LandingPage::query()
            ->where('slug', $slug)
            ->with([
                'locales',
                'navItems.translations',
                'sections.translations',
                'sections.entities.translations',
                'sections.entities.user',
            ])
            ->firstOrFail();

        $locales = array_keys(get_supported_languages());
        $bundles = [];

        foreach ($locales as $locale) {
            $bundles[$locale] = $this->composeLocaleBundle($page, $locale);
            $this->writeBundle($slug, $locale, $bundles[$locale]);
        }

        ksort($bundles);
        $meta = [
            'page' => $slug,
            'generated_at' => now()->toIso8601String(),
            'checksum' => hash('sha256', json_encode($bundles, JSON_UNESCAPED_UNICODE)),
            'version' => 1,
            'locales' => $locales,
        ];

        File::ensureDirectoryExists(base_path('jsonassets/page-cache'));
        File::put($this->metaPath($slug), json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return ['locales' => $locales, 'meta' => $meta];
    }

    /**
     * @return array<string, mixed>
     */
    public function composeLocaleBundle(LandingPage $page, string $locale): array
    {
        $pageLocale = $page->locales->firstWhere('locale', $locale)
            ?? $page->locales->firstWhere('locale', 'en');

        $out = [
            'version' => 1,
            'page' => $page->slug,
            'locale' => $locale,
            'generated_at' => now()->toIso8601String(),
            'meta' => [
                'title' => $pageLocale?->meta_title,
                'description' => $pageLocale?->meta_description,
            ],
            'topBar' => $this->sectionContent($page, 'top_bar', $locale) ?? [],
            'nav' => $this->composeNav($page, $locale),
            'hero' => $this->sectionContent($page, 'hero', $locale) ?? [],
            'about' => $this->sectionContent($page, 'about', $locale) ?? [],
            'cta' => $this->sectionContent($page, 'cta', $locale) ?? [],
            'departments' => $this->composeEntities($page, 'departments', $locale),
            'featuredDoctors' => $this->composeEntities($page, 'featured_doctors', $locale),
            'quickBooking' => array_merge(
                $this->sectionSettings($page, 'quick_booking') ?? [],
                ['copy' => $this->sectionContent($page, 'quick_booking', $locale) ?? []]
            ),
            'whyUs' => $this->composeWhyUs($page, $locale),
            'testimonials' => $this->composeEntities($page, 'testimonials', $locale),
            'blog' => $this->composeEntities($page, 'blog', $locale),
            'contact' => $this->sectionContent($page, 'contact', $locale) ?? [],
            'footer' => $this->sectionContent($page, 'footer', $locale) ?? [],
        ];

        return $out;
    }

    private function sectionContent(LandingPage $page, string $sectionKey, string $locale): ?array
    {
        $section = $page->sections->firstWhere('section_key', $sectionKey);
        if (! $section) {
            return null;
        }

        $tr = $section->translations->firstWhere('locale', $locale)
            ?? $section->translations->firstWhere('locale', 'en');

        return $tr?->content;
    }

    /**
     * Section copy (title, subtitle, …) plus card rows for “why us” / features.
     *
     * @return array{title: ?string, subtitle: ?string, items: list<array<string, mixed>>}
     */
    private function composeWhyUs(LandingPage $page, string $locale): array
    {
        $content = $this->sectionContent($page, 'why_us', $locale) ?? [];

        return [
            'title' => Arr::get($content, 'title'),
            'subtitle' => Arr::get($content, 'subtitle'),
            'items' => $this->composeEntities($page, 'why_us', $locale),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function sectionSettings(LandingPage $page, string $sectionKey): ?array
    {
        $section = $page->sections->firstWhere('section_key', $sectionKey);

        return $section?->settings;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function composeNav(LandingPage $page, string $locale): array
    {
        $items = [];
        foreach ($page->navItems->where('is_visible', true)->sortBy('sort_order') as $nav) {
            $tr = $nav->translations->firstWhere('locale', $locale)
                ?? $nav->translations->firstWhere('locale', 'en');
            if (! $tr) {
                continue;
            }
            $items[] = [
                'href' => $nav->href,
                'label' => $tr->label,
                'is_cta' => (bool) $nav->is_cta,
                'route_key' => $nav->route_key,
                'icon' => $nav->icon,
            ];
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function composeEntities(LandingPage $page, string $sectionKey, string $locale): array
    {
        $section = $page->sections->firstWhere('section_key', $sectionKey);
        if (! $section) {
            return [];
        }

        $rows = [];
        /** @var LandingEntity $entity */
        foreach ($section->entities->sortBy('sort_order') as $entity) {
            $tr = $entity->translations->firstWhere('locale', $locale)
                ?? $entity->translations->firstWhere('locale', 'en');
            $row = [
                'type' => $entity->type,
                'slug' => $entity->slug,
                'image' => $entity->image_path,
                'href' => $entity->href,
                'user_id' => $entity->user_id,
                'extra' => $entity->extra,
            ];
            if ($tr) {
                $row['title'] = $tr->title;
                $row['subtitle'] = $tr->subtitle;
                $row['body'] = $tr->body;
                $row['cta_label'] = $tr->cta_label;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function writeBundle(string $slug, string $locale, array $data): void
    {
        File::ensureDirectoryExists(base_path('jsonassets/page-cache'));
        File::put(
            $this->pathForLocale($slug, $locale),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
    }
}
