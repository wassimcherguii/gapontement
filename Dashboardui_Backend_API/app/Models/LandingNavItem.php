<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingNavItem extends Model
{
    protected $fillable = ['landing_page_id', 'sort_order', 'href', 'route_key', 'is_visible', 'is_cta', 'icon'];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_cta' => 'boolean',
        ];
    }

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(LandingNavItemTranslation::class);
    }
}
