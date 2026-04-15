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
        Schema::table('support_sessions', function (Blueprint $table) {
            $table->dropColumn(['twilio_room_sid', 'twilio_room_name']);
        });
    }

    public function down(): void
    {
        Schema::table('support_sessions', function (Blueprint $table) {
            $table->string('twilio_room_sid')->nullable();
            $table->string('twilio_room_name')->nullable();
        });
    }
};
