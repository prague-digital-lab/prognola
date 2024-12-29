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
        Schema::create('number_series', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('prefix')->nullable();
            $table->integer('digits_count');
            $table->string('suffix')->nullable();
            $table->integer('current_count')->default(0);
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
        Schema::dropIfExists('number_series');
    }
};
