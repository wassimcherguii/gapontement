<?php

namespace App\Services;

use App\Models\TranslationKey;
use Illuminate\Support\Facades\File;

class TranslationPublishService
{
    public function exportDomain(string $domain): array
    {
        $languages = $this->readLanguages($domain, true);
        $supportedLocales = array_keys($languages['supported'] ?? []);

        $keys = TranslationKey::query()
            ->domain($domain)
            ->where('status', 'published')
            ->with(['values' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('key')
            ->get();

        $flatByLocale = [];
        foreach ($supportedLocales as $locale) {
            $flatByLocale[$locale] = [];
        }

        foreach ($keys as $key) {
            foreach ($supportedLocales as $locale) {
                $value = optional(
                    $key->values->firstWhere('locale', $locale)
                )->value;

                if ($value !== null && $value !== '') {
                    $flatByLocale[$locale][$key->key] = $value;
                }
            }
        }

        $bundles = [];
        foreach ($flatByLocale as $locale => $flat) {
            $bundles[$locale] = $this->nestDotKeys($flat);
            $this->persistLocaleBundle($domain, $locale, $bundles[$locale]);
        }

        $checksumSource = [];
        foreach ($flatByLocale as $locale => $flat) {
            ksort($flat);
            $checksumSource[$locale] = $flat;
        }
        ksort($checksumSource);

        $meta = [
            'domain' => $domain,
            'generated_at' => now()->toIso8601String(),
            'checksum' => hash('sha256', json_encode($checksumSource, JSON_UNESCAPED_UNICODE)),
            'version' => 1,
        ];

        $this->writeMeta($domain, $meta);

        return [
            'domain' => $domain,
            'locales' => $supportedLocales,
            'count' => $keys->count(),
            'meta' => $meta,
        ];
    }

    /**
     * SHA-256 checksum of published DB content as it would be exported (active locales only).
     * Compare to `_sync_meta.json` checksum to detect drift from last publish.
     */
    public function computePublishedBundleChecksum(string $domain): string
    {
        $languages = $this->readLanguages($domain, true);
        $supportedLocales = array_keys($languages['supported'] ?? []);

        $keys = TranslationKey::query()
            ->domain($domain)
            ->where('status', 'published')
            ->with(['values' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('key')
            ->get();

        $flatByLocale = [];
        foreach ($supportedLocales as $locale) {
            $flatByLocale[$locale] = [];
        }

        foreach ($keys as $key) {
            foreach ($supportedLocales as $locale) {
                $value = optional(
                    $key->values->firstWhere('locale', $locale)
                )->value;

                if ($value !== null && $value !== '') {
                    $flatByLocale[$locale][$key->key] = $value;
                }
            }
        }

        $checksumSource = [];
        foreach ($flatByLocale as $locale => $flat) {
            ksort($flat);
            $checksumSource[$locale] = $flat;
        }
        ksort($checksumSource);

        return hash('sha256', json_encode($checksumSource, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Merge dot-key string values into on-disk locale bundles (nested JSON) and persist.
     *
     * @param  array<string, string|null>  $localeToValue  locale => value or null to remove key from flat map
     */
    public function mergeDotKeyIntoLocaleBundles(string $domain, string $dotKey, array $localeToValue, TranslationSyncService $syncService): void
    {
        foreach ($localeToValue as $locale => $raw) {
            if (! is_string($locale) || $locale === '') {
                continue;
            }
            $bundle = $this->readBundle($domain, $locale);
            $flat = $syncService->flattenDotKeys($bundle);
            if ($raw === null || $raw === '') {
                unset($flat[$dotKey]);
            } else {
                $flat[$dotKey] = $raw;
            }
            $nested = $this->nestDotKeys($flat);
            $this->persistLocaleBundle($domain, $locale, $nested);
        }
    }

    public function readBundle(string $domain, string $locale): array
    {
        $path = $this->domainBundlePath($domain, $locale);
        if (! File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?: [];
    }

    public function readLanguages(string $domain, bool $onlyActiveLocales = false): array
    {
        $data = $this->getDomainLanguages($domain);
        if (! $onlyActiveLocales || empty($data['supported']) || ! is_array($data['supported'])) {
            return $data;
        }

        $filtered = [];
        foreach ($data['supported'] as $code => $meta) {
            if (! is_array($meta)) {
                continue;
            }
            if (array_key_exists('active', $meta) && $meta['active'] === false) {
                continue;
            }
            $filtered[$code] = $meta;
        }

        $default = $data['default'] ?? 'en';
        if (! array_key_exists($default, $filtered) && count($filtered) > 0) {
            $default = array_key_first($filtered);
        }

        return [
            'default' => $default,
            'supported' => $filtered,
        ];
    }

    /**
     * Persist public_languages_{domain}.json (admin catalog editor).
     */
    public function writePublicLanguages(string $domain, array $payload): void
    {
        $path = $this->publicLanguagesPath($domain);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function publicLanguagesPath(string $domain): string
    {
        return base_path("jsonassets/public_languages_{$domain}.json");
    }

    public function readMeta(string $domain): array
    {
        $path = $this->domainMetaPath($domain);
        if (! File::exists($path)) {
            return [
                'domain' => $domain,
                'generated_at' => null,
                'checksum' => null,
                'version' => 1,
            ];
        }

        return json_decode(File::get($path), true) ?: [];
    }

    private function getDomainLanguages(string $domain): array
    {
        $path = $this->publicLanguagesPath($domain);
        if (File::exists($path)) {
            $decoded = json_decode(File::get($path), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [
            'default' => 'en',
            'supported' => [
                'en' => ['code' => 'en', 'name' => 'English', 'native' => 'English', 'direction' => 'ltr'],
            ],
        ];
    }

    private function domainBundlePath(string $domain, string $locale): string
    {
        return base_path("jsonassets/i18n/{$domain}/{$locale}.json");
    }

    private function domainMetaPath(string $domain): string
    {
        return base_path("jsonassets/i18n/{$domain}/_sync_meta.json");
    }

    private function persistLocaleBundle(string $domain, string $locale, array $bundle): void
    {
        $path = $this->domainBundlePath($domain, $locale);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($bundle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function writeMeta(string $domain, array $meta): void
    {
        $path = $this->domainMetaPath($domain);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function nestDotKeys(array $flat): array
    {
        $nested = [];
        foreach ($flat as $dotKey => $value) {
            $parts = explode('.', $dotKey);
            $cursor = &$nested;
            foreach ($parts as $index => $part) {
                if ($index === count($parts) - 1) {
                    $cursor[$part] = $value;

                    continue;
                }

                if (! isset($cursor[$part]) || ! is_array($cursor[$part])) {
                    $cursor[$part] = [];
                }
                $cursor = &$cursor[$part];
            }
            unset($cursor);
        }

        return $nested;
    }
}
