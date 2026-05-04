<?php

namespace App\Services;

use App\Models\TranslationKey;
use Illuminate\Support\Collection;

class TranslationSyncDiffService
{
    public function __construct(
        private readonly TranslationPublishService $publishService,
        private readonly TranslationSyncService $syncService,
    ) {}

    /**
     * Compare published DB export shape vs on-disk JSON, and JSON vs DB published values (import direction).
     *
     * @return array{
     *     publish: array{count: int, samples: list<array{locale: string, key: string, database: string, json: string}>},
     *     import: array{count: int, samples: list<array{locale: string, key: string, database: string, json: string}>},
     *     meta_checksum_match: bool,
     *     has_meta_checksum: bool
     * }
     */
    public function summarize(string $domain, int $sampleLimit = 40): array
    {
        $languages = $this->publishService->readLanguages($domain, true);
        $locales = array_keys($languages['supported'] ?? []);

        $dbFlatByLocale = $this->publishedDbFlatByLocale($domain, $locales);

        $publishRows = [];
        foreach ($locales as $locale) {
            $jsonFlat = $this->syncService->flattenDotKeys(
                $this->publishService->readBundle($domain, $locale)
            );
            $dbFlat = $dbFlatByLocale[$locale] ?? [];
            $allKeys = array_unique(array_merge(array_keys($dbFlat), array_keys($jsonFlat)));
            sort($allKeys);
            foreach ($allKeys as $key) {
                $dbNorm = $this->normalizeScalar($dbFlat[$key] ?? null);
                $jsonNorm = $this->normalizeScalar($jsonFlat[$key] ?? null);
                if ($dbNorm !== $jsonNorm) {
                    $publishRows[] = [
                        'locale' => $locale,
                        'key' => $key,
                        'database' => $this->labelForDiff($dbNorm),
                        'json' => $this->labelForDiff($jsonNorm),
                    ];
                }
            }
        }

        /** @var Collection<string, TranslationKey> $keysByNatural */
        $keysByNatural = TranslationKey::query()
            ->domain($domain)
            ->with(['values' => function ($query) {
                $query->where('status', 'published');
            }])
            ->get()
            ->keyBy('key');

        $importRows = [];
        foreach ($locales as $locale) {
            $jsonFlat = $this->syncService->flattenDotKeys(
                $this->publishService->readBundle($domain, $locale)
            );
            foreach ($jsonFlat as $key => $raw) {
                $jsonNorm = $this->normalizeScalar($raw);
                $dbNorm = $this->publishedDbValueForKey($keysByNatural, $key, $locale);
                if ($dbNorm !== $jsonNorm) {
                    $importRows[] = [
                        'locale' => $locale,
                        'key' => $key,
                        'database' => $this->labelForDiff($dbNorm),
                        'json' => $this->labelForDiff($jsonNorm),
                    ];
                }
            }
        }

        $syncMeta = $this->publishService->readMeta($domain);
        $diskChecksum = $syncMeta['checksum'] ?? null;
        $hasMetaChecksum = is_string($diskChecksum) && $diskChecksum !== '';
        $computed = $this->publishService->computePublishedBundleChecksum($domain);
        $metaChecksumMatch = $hasMetaChecksum && hash_equals($diskChecksum, $computed);

        return [
            'publish' => [
                'count' => count($publishRows),
                'samples' => array_slice($publishRows, 0, $sampleLimit),
            ],
            'import' => [
                'count' => count($importRows),
                'samples' => array_slice($importRows, 0, $sampleLimit),
            ],
            'meta_checksum_match' => $metaChecksumMatch,
            'has_meta_checksum' => $hasMetaChecksum,
        ];
    }

    /**
     * @param  list<string>  $locales
     * @return array<string, array<string, string>>
     */
    private function publishedDbFlatByLocale(string $domain, array $locales): array
    {
        $keys = TranslationKey::query()
            ->domain($domain)
            ->where('status', 'published')
            ->with(['values' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('key')
            ->get();

        $flatByLocale = [];
        foreach ($locales as $locale) {
            $flatByLocale[$locale] = [];
        }

        foreach ($keys as $key) {
            foreach ($locales as $locale) {
                $value = optional(
                    $key->values->firstWhere('locale', $locale)
                )->value;

                if ($value !== null && $value !== '') {
                    $flatByLocale[$locale][$key->key] = (string) $value;
                }
            }
        }

        return $flatByLocale;
    }

    /**
     * @param  Collection<string, TranslationKey>  $keysByNatural
     */
    private function publishedDbValueForKey(Collection $keysByNatural, string $naturalKey, string $locale): string
    {
        $tk = $keysByNatural->get($naturalKey);
        if ($tk === null) {
            return '';
        }

        $value = optional($tk->values->firstWhere('locale', $locale))->value;

        return $this->normalizeScalar($value);
    }

    private function normalizeScalar(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function labelForDiff(string $normalized): string
    {
        if ($normalized === '') {
            return '(empty)';
        }

        if (strlen($normalized) > 160) {
            return substr($normalized, 0, 157).'…';
        }

        return $normalized;
    }
}
