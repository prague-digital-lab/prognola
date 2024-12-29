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
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cash_register_id');
            $table->string('type')->default('to_bank');
            $table->decimal('amount');
            $table->unsignedInteger('performer_user_id');
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
        Schema::dropIfExists('cash_transfers');
    }
};
