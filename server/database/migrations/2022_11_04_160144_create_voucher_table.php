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
            $table->uuid();
            $table->string('code');
            $table->string('name');
            $table->string('email');
            $table->string('phone');

            $table->unsignedInteger('family_entry_count')->default(0);
            $table->unsignedInteger('child_entry_count')->default(0);
            $table->unsignedInteger('student_entry_count')->default(0);
            $table->unsignedInteger('adult_entry_count')->default(0);

            $table->decimal('price');

            $table->text('internal_note')->nullable();
            $table->text('description')->nullable();

            $table->string('order_status')->default('ordered');

            $table->string('shipping_type');
            $table->text('custom_text')->nullable();
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
            $table->dropColumn('uuid');
            $table->dropColumn('code');
            $table->dropColumn('name');
            $table->dropColumn('email');
            $table->dropColumn('phone');

            $table->dropColumn('family_entry_count');
            $table->dropColumn('child_entry_count');
            $table->dropColumn('student_entry_count');
            $table->dropColumn('adult_entry_count');

            $table->dropColumn('price');

            $table->dropColumn('internal_note');
            $table->dropColumn('description');

            $table->dropColumn('order_status');

            $table->dropColumn('shipping_type');
            $table->dropColumn('custom_text');
        });
    }
};
