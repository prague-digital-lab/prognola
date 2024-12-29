<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    public function findOrCreate(string $email, ?string $phone): Customer
    {
        $customer = Customer::where('email', $email)->first();

        // Existing customer
        if ($customer) {
            // Refresh phone
            if ($customer->phone != $phone) {
                $customer->phone = $phone;
                $customer->save();
            }

            return $customer;
        }

        // Create new customer
        $customer = new Customer;
        $customer->email = $email;

        $customer->phone = $phone;

        $customer->save();

        return $customer;
    }

    public function findOrCreateByEmail(string $email): Customer
    {
        $customer = Customer::where('email', $email)->first();

        // Existing customer
        if ($customer) {
            return $customer;
        }

        // Create new customer
        $customer = new Customer;
        $customer->email = $email;
        $customer->save();

        return $customer;
    }

    public function findOrCreateByPhone($phone)
    {
        $customer = Customer::where('phone', $phone)->first();

        // Existing customer
        if ($customer) {
            return $customer;
        }

        // Create new customer
        $customer = new Customer;
        $customer->phone = $phone;
        $customer->save();

        return $customer;
    }

    public function mergeCustomers(Customer $merged_customer, Customer $target_customer): void
    {
        Log::info("Merging customer $merged_customer->id to customer $target_customer->id.");

        foreach ($merged_customer->reservations as $reservation) {
            $reservation->customer_id = $target_customer->id;
            $reservation->save();
        }

        foreach ($merged_customer->vouchers as $voucher) {
            $voucher->customer_id = $target_customer->id;
            $voucher->save();
        }

        foreach ($merged_customer->invoices as $invoice) {
            $invoice->customer_id = $target_customer->id;
            $invoice->save();
        }

        foreach ($merged_customer->tickets as $ticket) {
            $ticket->requester_customer_id = $target_customer->id;
            $ticket->save();
        }

        foreach ($merged_customer->ticket_messages as $ticket_message) {
            $ticket_message->sender_customer_id = $target_customer->id;
            $ticket_message->save();
        }

        $merged_customer->delete();

        Log::info("Merging customer $merged_customer->id to customer $target_customer->id.");
    }
}
