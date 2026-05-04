<?php

namespace App\Services;

use App\Models\TranslationDomain;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use Illuminate\Support\Facades\DB;

class TranslationSyncService
{
    public function importDomainFromJson(string $domain, array $locales, TranslationPublishService $publishService): array
    {
        $importedCount = 0;

        $translationDomain = TranslationDomain::query()->where('slug', $domain)->firstOrFail();

        DB::transaction(function () use ($domain, $locales, $publishService, &$importedCount, $translationDomain) {
            foreach ($locales as $locale) {
                $bundle = $publishService->readBundle($domain, $locale);
                $flat = $this->flattenDotKeys($bundle);

                foreach ($flat as $key => $value) {
                    $translationKey = TranslationKey::query()->firstOrCreate(
                        ['translation_domain_id' => $translationDomain->id, 'key' => $key],
                        ['status' => 'published', 'version' => 1]
                    );

                    TranslationValue::query()->updateOrCreate(
                        ['translation_key_id' => $translationKey->id, 'locale' => $locale],
                        ['value' => (string) $value, 'status' => 'published']
                    );

                    $importedCount++;
                }
            }
        });

        return ['count' => $importedCount];
    }

    /**
     * @return array<string, string>
     */
    public function flattenDotKeys(array $nested, string $prefix = ''): array
    {
        $flat = [];
        foreach ($nested as $key => $value) {
            $fullKey = $prefix === '' ? $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $flat = array_merge($flat, $this->flattenDotKeys($value, $fullKey));

                continue;
            }

            $flat[$fullKey] = $value;
        }

        return $flat;
    }
}
