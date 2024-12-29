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
            $table->integer('number_serie_number')->after('variable_symbol')->nullable();
            $table->unsignedInteger('number_serie_id')->after('variable_symbol')->nullable();
            $table->string('code')->after('id')->nullable();
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
            $table->dropColumn('number_serie_number');
            $table->dropColumn('number_serie_id');
            $table->dropColumn('code');
        });
    }
};
