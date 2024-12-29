<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;

class CashBalanceCheck extends Model
{
    use HasFactory, Notifiable;

    protected $hidden = [
        'id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function cash_register(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function routeNotificationForDiscord()
    {
        if (App::environment() == 'production') {
            return '1044920409142861876';

        } else {
            return '1111963282018947092';
        }
    }
}
