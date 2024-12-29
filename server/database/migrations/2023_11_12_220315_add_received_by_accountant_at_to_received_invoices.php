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
        Schema::table('received_invoices', function (Blueprint $table) {
            $table->dateTime('received_by_accountant_at')->nullable()->after('paid_at');
            $table->unsignedInteger('received_by_accountant_id')->nullable()->after('received_by_accountant_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('received_invoices', function (Blueprint $table) {
            $table->dropColumn('received_by_accountant_at');
            $table->dropColumn('received_by_accountant_id');
        });
    }
};
