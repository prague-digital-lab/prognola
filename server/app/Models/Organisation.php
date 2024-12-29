<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Organisation
 */
class Organisation extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];

    const ORGANISATION_TYPE_FREELANCER = 'freelancer';

    const ORGANISATION_TYPE_SCHOOL = 'school';

    const ORGANISATION_TYPE_COMPANY = 'company';

    const ORGANISATION_TYPE_NONPROFIT = 'nonprofit_organisation';

    const ORGANISATION_TYPE_FREE_TIME_ORGANISATION = 'free_time_organisation';

    const ORGANISATION_TYPE_SPORT_ORGANISATION = 'sport_organisation';

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class)
            ->orderByDesc('paid_at');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function counter_bank_accounts(): HasMany
    {
        return $this->hasMany(CounterBankAccount::class);
    }

    public function getTypeString()
    {
        if ($this->type == self::ORGANISATION_TYPE_FREELANCER) {
            return 'živnostník';
        } elseif ($this->type == self::ORGANISATION_TYPE_COMPANY) {
            return 'firma';
        } elseif ($this->type == self::ORGANISATION_TYPE_SCHOOL) {
            return 'škola';
        } elseif ($this->type == self::ORGANISATION_TYPE_NONPROFIT) {
            return 'nezisková organizace';
        } elseif ($this->type == self::ORGANISATION_TYPE_FREE_TIME_ORGANISATION) {
            return 'volnočasová organizace';
        } elseif ($this->type == self::ORGANISATION_TYPE_SPORT_ORGANISATION) {
            return 'sportovní organizace';
        } else {
            return 'neznámý';
        }
    }
}
