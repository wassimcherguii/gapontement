<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorPalette extends Model
{
    protected $fillable = [
        'name',
        'category',
        'theme',
        'hex_value',
        'rgb_value',
        'usage',
        'description',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Scope for active colors
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific category
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
    
    // Scope for specific theme
    public function scopeTheme($query, $theme)
    {
        return $query->where('theme', $theme);
    }

    // Get colors by usage
    public function scopeUsage($query, $usage)
    {
        return $query->where('usage', 'like', "%{$usage}%");
    }

    // Get colors ordered by sort_order
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
