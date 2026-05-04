<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationDomain extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug',
        'name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function translationKeys(): HasMany
    {
        return $this->hasMany(TranslationKey::class);
    }
}
