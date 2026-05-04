<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingSection extends Model
{
    protected $fillable = ['landing_page_id', 'section_key', 'sort_order', 'settings'];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(LandingSectionTranslation::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(LandingEntity::class)->orderBy('sort_order');
    }

    public function translationFor(string $locale): ?LandingSectionTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }
}
