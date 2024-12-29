<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CashTransfer
 */
class CashTransfer extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];

    const TYPE_TO_BANK = 'to_bank';

    public function performer_user()
    {
        return $this->belongsTo(User::class, 'performer_user_id');
    }

    public function cash_register()
    {
        return $this->belongsTo(CashRegister::class);
    }
}
