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
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedInteger('customer_id');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_street')->nullable();
            $table->string('recipient_city')->nullable();
            $table->string('recipient_postal')->nullable();
            $table->string('recipient_country')->nullable();
            $table->string('recipient_ic')->nullable();
            $table->string('recipient_dic')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropColumn('recipient_name');
            $table->dropColumn('recipient_street');
            $table->dropColumn('recipient_city');
            $table->dropColumn('recipient_postal');
            $table->dropColumn('recipient_country');
            $table->dropColumn('recipient_ic');
            $table->dropColumn('recipient_dic');
        });
    }
};
