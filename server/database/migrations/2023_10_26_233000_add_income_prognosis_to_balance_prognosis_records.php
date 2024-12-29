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
            $table->decimal('income_prognosis', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_prognosis_records', function (Blueprint $table) {
            $table->dropColumn('income_prognosis');
        });
    }
};
