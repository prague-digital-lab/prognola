<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Workspace model
 */
class Workspace extends Model
{
    use HasFactory;
    use Notifiable;

    protected $hidden = [
        'id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'is_active']);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function organisations(): HasMany
    {
        return $this->hasMany(Organisation::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    public function bank_accounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function bank_payments(): HasMany
    {
        return $this->hasMany(BankPayment::class);
    }

    public function balance_prognosis_records(): HasMany
    {
        return $this->hasMany(BalancePrognosisRecord::class);
    }

    public function expense_categories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function income_categories(): HasMany
    {
        return $this->hasMany(IncomeCategory::class);
    }

    public function counter_bank_accounts(): HasMany
    {
        return $this->hasMany(CounterBankAccount::class);
    }

    public function getInboxEmailAttribute()
    {
        return $this->url_slug.'-'.$this->inbox_email_hash.'@inbox.prognola.com';
    }

    public function regenerateInboxEmail()
    {
        $this->inbox_email_hash = Str::random(15);
    }

    public function routeNotificationForDiscord()
    {
        return $this->discord_channel_id;
    }
}
