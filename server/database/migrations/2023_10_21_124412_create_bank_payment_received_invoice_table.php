<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bank_payment_received_invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('bank_payment_id');
            $table->unsignedInteger('received_invoice_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_payment_received_invoice');
    }
};
