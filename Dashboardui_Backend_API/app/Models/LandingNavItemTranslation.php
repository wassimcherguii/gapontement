<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingNavItemTranslation extends Model
{
    protected $fillable = ['landing_nav_item_id', 'locale', 'label'];

    public function navItem(): BelongsTo
    {
        return $this->belongsTo(LandingNavItem::class, 'landing_nav_item_id');
    }
}
