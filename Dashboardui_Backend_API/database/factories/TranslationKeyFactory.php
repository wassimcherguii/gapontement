<?php

namespace Database\Factories;

use App\Models\TranslationDomain;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TranslationKey>
 */
class TranslationKeyFactory extends Factory
{
    protected $model = TranslationKey::class;

    public function definition(): array
    {
        return [
            'translation_domain_id' => TranslationDomain::factory(),
            'key' => fake()->unique()->lexify('key.????'),
            'description' => null,
            'status' => 'draft',
            'version' => 1,
        ];
    }
}
