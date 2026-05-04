<?php

namespace Database\Factories;

use App\Models\TranslationDomain;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TranslationDomain>
 */
class TranslationDomainFactory extends Factory
{
    protected $model = TranslationDomain::class;

    public function definition(): array
    {
        $slug = 'dom_'.fake()->unique()->lexify('????');

        return [
            'slug' => $slug,
            'name' => fake()->words(2, true),
            'sort_order' => 0,
        ];
    }
}
