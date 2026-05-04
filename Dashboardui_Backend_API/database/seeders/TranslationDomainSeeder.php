<?php

namespace Database\Seeders;

use App\Models\TranslationDomain;
use Illuminate\Database\Seeder;

class TranslationDomainSeeder extends Seeder
{
    /**
     * Idempotent: ensures rows exist for each slug in config (bootstrap / legacy list).
     */
    public function run(): void
    {
        $slugs = config('translation_domains.domains', ['web', 'mobile', 'student', 'teacher']);
        foreach ($slugs as $i => $slug) {
            TranslationDomain::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst((string) $slug), 'sort_order' => $i]
            );
        }
    }
}
