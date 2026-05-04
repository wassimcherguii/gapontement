<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslationValue extends Model
{
    protected $fillable = [
        'translation_key_id',
        'locale',
        'value',
        'status',
    ];

    public function translationKey(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }
}
