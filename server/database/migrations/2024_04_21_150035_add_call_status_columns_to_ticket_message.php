<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->string('twilio_call_sid')->nullable();
            $table->string('twilio_call_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropColumn('twilio_call_sid');
            $table->dropColumn('twilio_call_status');
        });
    }
};
