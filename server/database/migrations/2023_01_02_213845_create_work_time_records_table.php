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
        Schema::create('work_time_records', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_from');
            $table->dateTime('date_to')->nullable();
            $table->unsignedInteger('user_id');
            $table->decimal('hour_rate');
            $table->decimal('price')->default(0);
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
        Schema::dropIfExists('work_time_records');
    }
};
