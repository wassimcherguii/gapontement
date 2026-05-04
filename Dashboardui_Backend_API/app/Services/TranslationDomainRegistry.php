<?php

namespace App\Services;

use App\Models\TranslationDomain;
use Illuminate\Support\Facades\Cache;

class TranslationDomainRegistry
{
    public const CACHE_KEY_SLUGS = 'translation_domains.slugs.v1';

    public const CACHE_KEY_MODELS = 'translation_domains.models.v1';

    /**
     * @return list<string>
     */
    public function allowedSlugs(): array
    {
        return Cache::rememberForever(self::CACHE_KEY_SLUGS, function () {
            return TranslationDomain::query()
                ->orderBy('sort_order')
                ->orderBy('slug')
                ->pluck('slug')
                ->all();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, TranslationDomain>
     */
    public function orderedDomains()
    {
        return Cache::rememberForever(self::CACHE_KEY_MODELS, function () {
            return TranslationDomain::query()
                ->orderBy('sort_order')
                ->orderBy('slug')
                ->get();
        });
    }

    public function findBySlug(string $slug): ?TranslationDomain
    {
        return TranslationDomain::query()->where('slug', $slug)->first();
    }

    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY_SLUGS);
        Cache::forget(self::CACHE_KEY_MODELS);
    }
}
