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
        Schema::create('cash_balance_checks', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount');
            $table->unsignedInteger('created_by_user_id');
            $table->unsignedInteger('cash_register_id');
            $table->dateTime('checked_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_balance_checks');
    }
};
