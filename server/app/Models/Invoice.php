<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Invoice
 */
class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    const PAYMENT_STATUS_DRAFT = 'draft';

    const PAYMENT_STATUS_PENDING = 'pending';

    const PAYMENT_STATUS_PAID = 'paid';

    const PAYMENT_STATUS_STORNO = 'storno';

    protected $casts = [
        'paid_at' => 'datetime:Y-m-d',
        'due_at' => 'datetime:Y-m-d',
    ];

    protected $hidden = [
        'id',
    ];

    public const PAYMENT_TYPE_CASH = 'cash';

    public const PAYMENT_TYPE_CARD = 'card';

    public const PAYMENT_TYPE_BANK_TRANSFER = 'bank_transfer';

    public const PAYMENT_TYPE_STRIPE = 'card_stripe';

    public const PAYMENT_TYPE_SLEVOMAT = 'slevomat';

    public const PAYMENT_TYPE_SKVELE_CESKO = 'skvele_cesko';

    public function getTitleString()
    {
        if ($this->code) {
            return $this->code;
        } else {
            return "Doklad č. $this->id";
        }
    }

    public function getPaymentTypeString()
    {
        if ($this->payment_type == $this::PAYMENT_TYPE_CASH) {
            return 'hotově';
        } elseif ($this->payment_type == $this::PAYMENT_TYPE_CARD) {
            return 'kartou';
        } elseif ($this->payment_type == $this::PAYMENT_TYPE_SLEVOMAT) {
            return 'slevomat voucher';
        } elseif ($this->payment_type == $this::PAYMENT_TYPE_SKVELE_CESKO) {
            return 'Skvelecesko.cz voucher';
        } elseif ($this->payment_type == $this::PAYMENT_TYPE_BANK_TRANSFER) {
            return 'převodem';
        } elseif ($this->payment_type == $this::PAYMENT_TYPE_STRIPE) {
            return 'online platební bránou';
        } elseif (! $this->payment_type) {
            return '-';
        } else {
            throw new \Exception('Wrong payment type.');
        }
    }

    public function income()
    {
        return $this->hasOne(Income::class);
    }

    public function invoice_items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function bank_payments()
    {
        return $this->belongsToMany(BankPayment::class);
    }

    public function cash_register()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function number_serie()
    {
        return $this->belongsTo(NumberSerie::class);
    }

    /**
     * @throws \Exception
     */
    public function setNumberSerie(Invoice $invoice, NumberSerie $serie)
    {
        if ($invoice->number_serie_id !== null) {
            throw new \Exception('Invoice number serie id already set.');
        }

        $invoice->number_serie_id = $serie->id;
        $invoice->save();

        return $invoice;
    }

    public function getPriceWithoutVatForVatRate($vat_rate)
    {
        return $this->invoice_items
            ->where('vat_rate', $vat_rate)
            ->sum(function ($item) {
                return $item->getPriceWithoutVat();
            });
    }

    public function getPriceWithVatForVatRate($vat_rate)
    {
        return $this->invoice_items
            ->where('vat_rate', $vat_rate)
            ->sum('price');
    }

    public function getVatAmountForVatRate($vat_rate)
    {
        return $this->getPriceWithVatForVatRate($vat_rate) - $this->getPriceWithoutVatForVatRate($vat_rate);
    }

    public function getPaymentStatusString()
    {
        if ($this->payment_status == self::PAYMENT_STATUS_DRAFT) {
            return 'koncept';
        } elseif ($this->payment_status == self::PAYMENT_STATUS_PENDING) {
            return 'čeká na platbu';
        } elseif ($this->payment_status == self::PAYMENT_STATUS_PAID) {
            return 'zaplaceno';
        } else {
            throw new \Exception('Unknown payment status');
        }
    }

    /**
     * @throws \Exception
     */
    public function getDiscordMessageEmbed()
    {
        return [
            'title' => $this->getTitleString(),
            'url' => route('admin.invoices.edit', $this),
            'color' => 0x064BF9,
            'fields' => [
                [
                    'name' => 'Odběratel',
                    'value' => $this->recipient_name,
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
                    'name' => 'Typ platby',
                    'value' => $this->getPaymentTypeString(),
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

    public function syncToIncome(): ?Income
    {
        Log::info("Syncing invoice $this->id to income.");

        $this->refresh();

        // When invoice is draft or stornoed, there should be no related income.
        if ($this->payment_status == self::PAYMENT_STATUS_DRAFT || $this->payment_status == self::PAYMENT_STATUS_STORNO) {

            if ($this->income !== null) {
                Log::info("Deleting income {$this->income->id}. Related invoice is in state $this->payment_status.");
                $this->income()->delete();
            }

            return null;
        }

        // Create or update income
        if ($this->income === null) {
            Log::info("There is no related income for invoice $this->id. Creating new one.");
            $income = new Income;
        } else {
            Log::info("Existing income found: {$this->income->id} for invoice $this->id.");
            $income = $this->income;
        }

        $income->name = $this->getTitleString();

        if ($this->payment_status == self::PAYMENT_STATUS_PENDING) {
            $income->payment_status = Income::PAYMENT_STATUS_PLAN;
            $income->paid_at = $this->due_at ?: $this->created_at;
        } elseif ($this->payment_status == self::PAYMENT_STATUS_PAID) {
            $income->payment_status = Income::PAYMENT_STATUS_PAID;
            $income->paid_at = $this->paid_at;
        }

        $income->amount = $this->price;
        $income->invoice_id = $this->id;
        $income->save();

        return $income;
    }
}
