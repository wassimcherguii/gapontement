<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationKey extends Model
{
    use HasFactory;

    /** @deprecated Use TranslationDomain::SLUG_WEB or resolve from DB */
    public const DOMAIN_WEB = 'web';

    /** @deprecated Use TranslationDomain slug from DB */
    public const DOMAIN_MOBILE = 'mobile';

    protected $fillable = [
        'translation_domain_id',
        'key',
        'description',
        'status',
        'version',
    ];

    protected $casts = [
        'version' => 'integer',
    ];

    public function translationDomain(): BelongsTo
    {
        return $this->belongsTo(TranslationDomain::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(TranslationValue::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<TranslationKey>  $query
     * @return \Illuminate\Database\Eloquent\Builder<TranslationKey>
     */
    public function scopeDomain($query, string $slug)
    {
        return $query->whereHas('translationDomain', function ($q) use ($slug) {
            $q->where('slug', $slug);
        });
    }
}
