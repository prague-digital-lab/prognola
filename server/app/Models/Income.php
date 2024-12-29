<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Income extends Model
{
    use HasFactory;

    const INCOME_TYPE_GENERIC = 'generic';
    const INCOME_TYPE_PLAN = 'plan';
    const INCOME_TYPE_INVOICE = 'invoice';


    const PAYMENT_STATUS_PLAN = 'plan';
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    protected $hidden = [
        'id',
    ];
    protected $casts = [
        'paid_at' => 'datetime',
        'paired_at' => 'datetime',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function income_category()
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function bank_payments(): BelongsToMany
    {
        return $this->belongsToMany(BankPayment::class)
            ->withPivot(['amount']);
    }
}
