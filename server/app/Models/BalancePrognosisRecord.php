<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\BalancePrognosisRecord
 */
class BalancePrognosisRecord extends Model
{
    use HasFactory;

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $hidden = [
        'id',
    ];
}
