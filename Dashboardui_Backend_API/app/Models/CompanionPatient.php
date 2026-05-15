<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanionPatient extends Model
{
    protected $fillable = [
        'companion_user_id',
        'patient_user_id',
        'can_book',
    ];

    protected function casts(): array
    {
        return [
            'can_book' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function companion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'companion_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_user_id');
    }
}
