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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('payment_status');
            $table->decimal('amount', 10, 2)->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('paired_at')->nullable();

            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('organisation_id')->nullable();
            $table->unsignedInteger('income_category_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
