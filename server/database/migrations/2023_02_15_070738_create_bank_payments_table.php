<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('bank_account_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();

            $table->decimal('amount');
            $table->string('type');
            $table->text('description')->nullable();

            $table->bigInteger('external_id');
            $table->dateTime('issued_at');
            $table->string('counter_account_number')->nullable();
            $table->unsignedInteger('counter_bank_account_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_transfers');
    }
};
