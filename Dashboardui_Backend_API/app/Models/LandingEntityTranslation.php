<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingEntityTranslation extends Model
{
    protected $fillable = ['landing_entity_id', 'locale', 'title', 'subtitle', 'body', 'cta_label'];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(LandingEntity::class, 'landing_entity_id');
    }
}
