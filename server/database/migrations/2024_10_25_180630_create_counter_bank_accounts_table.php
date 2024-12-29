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
        Schema::create('counter_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->unsignedInteger('workspace_id');
            $table->string('account_number')->nullable();
            $table->string('bank_number')->nullable();
            $table->string('iban_number')->nullable();
            $table->string('swift')->nullable();
            $table->boolean('is_default');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counter_bank_accounts');
    }
};
