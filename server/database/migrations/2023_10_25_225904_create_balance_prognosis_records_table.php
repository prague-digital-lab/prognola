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
        Schema::create('balance_prognosis_records', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->decimal('amount', 12);
            $table->decimal('amount_diff', 12);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_prognosis_records');
    }
};
