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
        Schema::table('balance_prognosis_records', function (Blueprint $table) {
            $table->uuid()->after('id');
            $table->unsignedInteger('workspace_id')->after('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_prognosis_records', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropColumn('workspace_id');
        });
    }
};
