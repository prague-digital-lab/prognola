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
            $table->dateTime('paired_at')->nullable();

            $table->dropColumn('is_paired');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('received_invoices', function (Blueprint $table) {
            $table->dropColumn('paired_at');

            $table->boolean('is_paired')->default(false);
        });
    }
};
