<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingEntity extends Model
{
    protected $fillable = ['landing_section_id', 'type', 'sort_order', 'slug', 'image_path', 'href', 'user_id', 'extra'];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(LandingSection::class, 'landing_section_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(LandingEntityTranslation::class);
    }
}
