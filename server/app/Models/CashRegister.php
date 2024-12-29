<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\CashRegister
 */
class CashRegister extends Model
{
    protected $casts = [
        'started_registering_at' => 'datetime:Y-m-d',
    ];

    protected $hidden = [
        'id',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function cashTransfers(): HasMany
    {
        return $this->hasMany(CashTransfer::class);
    }

    public function countCurrentAmount()
    {
        $invoices = $this->invoices()
            ->where('paid_at', '>=', $this->started_registering_at)
            ->sum('price');

        $transfers = $this->cashTransfers()
            ->where('created_at', '>=', $this->started_registering_at)
            ->sum('amount');

        $this->amount = $this->initial_amount + $invoices - $transfers;
    }
}
