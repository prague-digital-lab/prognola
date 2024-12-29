<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\BankAccount
 */
class BankAccount extends Model
{
    use HasFactory;

    const BANK_FIO_API = 'fio';

    const BANK_KOMERCNI_BANKA_CSV = 'komercni_banka_csv';
    const BANK_MONETA_API = 'moneta';

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    protected $hidden = [
        'id',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function bank_payments(): HasMany
    {
        return $this->hasMany(BankPayment::class);
    }
}
