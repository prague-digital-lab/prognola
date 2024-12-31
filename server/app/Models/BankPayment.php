<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * App\Models\BankPayment
 *
 * @mixin Eloquent
 */
class BankPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts
        = [
            'issued_at' => 'datetime',
            'paired_at' => 'datetime',
        ];

    protected $hidden = [
        'id',
    ];

    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function bank_account(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function counter_bank_account(): BelongsTo
    {
        return $this->belongsTo(CounterBankAccount::class);
    }

    public function transfer_bank_account()
    {
        return $this->belongsTo(BankAccount::class, 'transfer_bank_account_id');
    }

    public function expenses(): BelongsToMany
    {
        return $this->belongsToMany(Expense::class)
            ->withPivot(['amount']);
    }

    public function incomes(): BelongsToMany
    {
        return $this->belongsToMany(Income::class)
            ->withPivot(['amount']);
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class);
    }

    public function getTypeString()
    {
        if ($this->type === self::TYPE_INCOME) {
            return 'příjem';
        } elseif ($this->type === self::TYPE_EXPENSE) {
            return 'výdaj';
        } else {
            return 'převod';
        }
    }

    public function getTitle()
    {
        if ($this->description === $this->sender_comment) {
            return $this->description;
        } elseif (empty($this->description) && empty($this->sender_comment)) {
            return $this->counter_account_number;
        } else {
            return $this->description.' '.$this->sender_comment;
        }
    }

    public function syncOrCreateCounterBankAccount(): void
    {
        if ($this->counter_account_number === null || $this->counter_bank_number === null) {
            return;
        }

        $counter_bank_account = $this->workspace->counter_bank_accounts()
            ->where('account_number', $this->counter_account_number)
            ->where('bank_number', $this->counter_bank_number)
            ->first();

        if ($counter_bank_account !== null) {
            $this->counter_bank_account_id = $counter_bank_account->id;
            $this->save();

            return;
        }

        $counter_bank_account = new CounterBankAccount;
        $counter_bank_account->uuid = Str::uuid();
        $counter_bank_account->is_default = false;
        $counter_bank_account->workspace_id = $this->workspace->id;
        $counter_bank_account->account_number = $this->counter_account_number;
        $counter_bank_account->bank_number = $this->counter_bank_number;
        $counter_bank_account->save();

        $this->counter_bank_account_id = $counter_bank_account->id;
        $this->save();
    }
}
