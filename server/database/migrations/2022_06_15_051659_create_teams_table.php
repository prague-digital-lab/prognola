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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('game_internal_id')->nullable();
            $table->unsignedInteger('reservation_id')->nullable();
            $table->integer('player_count')->nullable();
            $table->string('name')->nullable();
            $table->string('photo')->nullable();
            $table->integer('game_time')->nullable();
            $table->integer('game_time_rough')->nullable();
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
        Schema::dropIfExists('teams');
    }
};
