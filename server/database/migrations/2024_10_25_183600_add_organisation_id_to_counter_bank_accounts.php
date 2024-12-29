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
        Schema::table('counter_bank_accounts', function (Blueprint $table) {
            $table->unsignedInteger('organisation_id')
                ->nullable()
                ->after('workspace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('counter_bank_accounts', function (Blueprint $table) {
            $table->dropColumn('organisation_id');
        });
    }
};
