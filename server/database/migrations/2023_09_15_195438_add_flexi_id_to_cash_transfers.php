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
        Schema::table('cash_transfers', function (Blueprint $table) {
            $table->unsignedInteger('flexi_id')->nullable();
            $table->string('flexi_number')->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_transfers', function (Blueprint $table) {
            $table->dropColumn('flexi_id');
            $table->dropColumn('flexi_number');
            $table->dropColumn('description');
        });
    }
};
