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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->boolean('is_used')->default(false);
            $table->unsignedInteger('reservation_id')->nullable();
            $table->string('shipping_status')->after('shipping_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('is_used');
            $table->dropColumn('reservation_id');
            $table->dropColumn('shipping_type');
        });
    }
};
