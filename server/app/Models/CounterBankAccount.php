<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CounterBankAccount extends Model
{
    use HasFactory;

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function bank_payments(): HasMany
    {
        return $this->hasMany(BankPayment::class);
    }
}
