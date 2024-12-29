<?php

namespace App\Jobs;

use App\Models\Customer;
use Ecomail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCustomerToEcomailContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Customer $customer;

    /**
     * Create a new job instance.
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tags = [];

        foreach ($this->customer->reservations as $reservation) {
            $tags[] = $reservation->customer_type;
        }

        if ($this->customer->vouchers()->count() > 0) {
            $tags[] = 'voucher';
        }

        if ($this->customer->tickets()->count() > 0) {
            $tags[] = 'crm_ticket';
        }

        $ecomail = new Ecomail('102788391d1715436035e11a821454414d0ea12876d033b96a6a247f77cd29c0');
        $res = $ecomail->addSubscriber(1, [
            'subscriber_data' => [
                'email' => $this->customer->email,
                'phone' => $this->customer->phone,
                'tags' => $tags,
                'custom_fields' => [
                    'IS_CUSTOMER_ID' => [
                        'value' => $this->customer->id,
                        'type' => 'int',
                    ],
                ],
            ],
            'update_existing' => true,
        ]);
    }
}
