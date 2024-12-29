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
        Schema::table('bank_payments', function (Blueprint $table) {
            $table->unsignedInteger('transfer_bank_account_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_payments', function (Blueprint $table) {
            $table->dropColumn('transfer_bank_account_id');
        });
    }
};
