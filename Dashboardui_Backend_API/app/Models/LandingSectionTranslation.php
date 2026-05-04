<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingSectionTranslation extends Model
{
    protected $fillable = ['landing_section_id', 'locale', 'content'];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(LandingSection::class, 'landing_section_id');
    }
}
