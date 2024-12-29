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
        Schema::create('received_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('external_code')->nullable();
            $table->unsignedInteger('organisation_id')->nullable();
            $table->decimal('price')->default(0);
            $table->dateTime('received_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('payment_status');
            $table->text('internal_note')->nullable();
            $table->unsignedInteger('expense_category_id')->nullable();
            $table->unsignedInteger('created_by_user_id')->nullable();
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
        Schema::dropIfExists('received_invoices');
    }
};
