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
            $table->unsignedInteger('processed_by_user_id')->nullable()->after('created_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('received_invoices', function (Blueprint $table) {
            $table->dropColumn('processed_by_user_id');
        });
    }
};
