<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('bank_payment_received_invoice', 'bank_payment_expense');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('bank_payment_expense', 'bank_payment_received_invoice');
    }
};
