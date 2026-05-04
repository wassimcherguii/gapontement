<?php

namespace App\Services;

use App\Models\TranslationDomain;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TranslationDomainProvisioner
{
    public function __construct(
        private readonly TranslationPublishService $publishService,
        private readonly TranslationDomainRegistry $registry
    ) {}

    /**
     * Create DB row, default public_languages JSON, and i18n/{slug} directory.
     *
     * @throws \Throwable
     */
    public function provision(string $slug, string $name): TranslationDomain
    {
        $domain = DB::transaction(function () use ($slug, $name) {
            $maxOrder = (int) TranslationDomain::query()->max('sort_order');

            $domain = TranslationDomain::query()->create([
                'slug' => $slug,
                'name' => $name,
                'sort_order' => $maxOrder + 1,
            ]);

            $defaultCatalog = [
                'default' => 'en',
                'supported' => [
                    'en' => [
                        'code' => 'en',
                        'name' => 'English',
                        'native' => 'English',
                        'direction' => 'ltr',
                        'flag' => '🇺🇸',
                    ],
                ],
            ];

            $this->publishService->writePublicLanguages($slug, $defaultCatalog);

            $i18nDir = base_path('jsonassets/i18n/'.$slug);
            File::ensureDirectoryExists($i18nDir);

            return $domain;
        });

        $this->registry->invalidate();

        return $domain;
    }
}
