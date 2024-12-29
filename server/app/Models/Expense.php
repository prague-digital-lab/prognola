<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Expense extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
    ];

    const PAYMENT_STATUS_PLAN = 'plan';

    const PAYMENT_STATUS_DRAFT = 'draft';

    const PAYMENT_STATUS_PENDING = 'pending';

    const PAYMENT_STATUS_PAID = 'paid';

    protected $casts = [
        'received_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'paired_at' => 'datetime',
        'received_by_accountant_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processed_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function received_by_accountant(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank_payments(): BelongsToMany
    {
        return $this->belongsToMany(BankPayment::class)
            ->orderBy('issued_at')
            ->withPivot('amount');
    }

    public function related_user()
    {
        return $this->belongsTo(User::class);
    }

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function getTitleString()
    {
        if ($this->description) {
            return $this->description;
        } else {
            return 'výdaj '.$this->getInternalCode();
        }
    }

    public function getPaymentStatusString(): string
    {
        if ($this->payment_status == self::PAYMENT_STATUS_DRAFT) {
            return ':white_square_button: ke zpracování';
        } elseif ($this->payment_status == self::PAYMENT_STATUS_PAID) {
            return ':purple_circle: uhrazeno';
        } elseif ($this->payment_status == self::PAYMENT_STATUS_PENDING) {
            return ':orange_circle: k úhradě';
        } elseif ($this->payment_status == self::PAYMENT_STATUS_PLAN) {
            return ' :yellow_circle: odhad';
        } else {
            throw new Exception("Unknown payment_status $this->payment_status.");
        }
    }

    public function getDueAtString()
    {
        return $this->due_at->diffForHumans(Carbon::now());
    }

    public function getInternalCode()
    {
        return 'V-'.$this->id;
    }

    public function getAmountToPay()
    {
        return $this->price - $this->bank_payments()->sum('bank_payment_received_invoice.amount');
    }

    public function getPaidAmount()
    {
        return $this->bank_payments()->sum('bank_payment_received_invoice.amount');
    }

    /**
     * @throws Exception
     */
    public function getDiscordMessageEmbed()
    {
        if ($this->organisation !== null) {
            $organisation_route = route('admin.organisations.show', $this->organisation);
        } else {
            $organisation_route = null;
        }

        return [
            'title' => $this->getTitleString(),
            'url' => route('admin.received_invoices.show', $this),
            'color' => 5921370,
            'fields' => [
                [
                    'name' => 'Dodavatel',
                    'value' => $this->organisation !== null ? "[{$this->organisation->name}]({$organisation_route})" : '-',
                    'inline' => true,
                ],

                [
                    'name' => 'Částka',
                    'value' => number_format($this->price, 2, ',', ' '),
                    'inline' => true,
                ],

                // New line in inline embeds
                [
                    'name' => '',
                    'value' => '',
                ],

                [
                    'name' => 'Interní ID',
                    'value' => $this->getInternalCode(),
                    'inline' => true,

                ],

                [
                    'name' => 'Stav',
                    'value' => $this->getPaymentStatusString(),
                    'inline' => true,
                ],

            ],
        ];
    }
}
