<?php

namespace App\Models;

use App\Jobs\SyncCustomerToEcomailContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\Customer
 */
class Customer extends Model
{
    use HasFactory;
    use Notifiable;

    protected $hidden = [
        'id',
    ];

    public function countSalesAmount()
    {
        $reservations_sales = $this->reservations()->sum('price');
        $voucher_sales = $this->vouchers()
            ->where('order_status', Voucher::ORDER_STATUS_PAID)
            ->sum('price');

        $this->sales_amount = $reservations_sales + $voucher_sales;
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'requester_customer_id');
    }

    public function ticket_messages()
    {
        return $this->hasMany(TicketMessage::class, 'sender_customer_id');
    }

    public function getName()
    {
        return $this->name ?: $this->email ?: $this->phone;
    }

    public function syncToEcomail(): void
    {
        SyncCustomerToEcomailContact::dispatch($this);
    }
}
