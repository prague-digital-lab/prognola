<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InvoiceItem
 */
class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'id',
    ];

    const VAT_RATE_21 = 21;

    const VAT_RATE_15 = 15;

    const VAT_RATE_10 = 10;

    const VAT_RATE_0 = 0;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getPriceWithoutVat()
    {
        if ($this->vat_rate === InvoiceItem::VAT_RATE_21) {
            return round($this->price / 1.21, 2);
        } elseif ($this->vat_rate === InvoiceItem::VAT_RATE_15) {
            return round($this->price / 1.15, 2);
        } elseif ($this->vat_rate === InvoiceItem::VAT_RATE_10) {
            return round($this->price / 1.10, 2);
        } else {
            return $this->price;
        }
    }
}
