<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPage extends Model
{
    protected $fillable = ['slug'];

    public function locales(): HasMany
    {
        return $this->hasMany(LandingPageLocale::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(LandingSection::class)->orderBy('sort_order');
    }

    public function navItems(): HasMany
    {
        return $this->hasMany(LandingNavItem::class)->orderBy('sort_order');
    }
}
