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
        // Drop indexes first (SQLite requires this before dropping columns)
        Schema::table('recordings', function (Blueprint $table) {
            $table->dropIndex('recordings_twilio_recording_sid_index');
            $table->dropUnique('recordings_twilio_recording_sid_unique');
        });

        Schema::table('recordings', function (Blueprint $table) {
            $table->dropColumn(['twilio_recording_sid', 'recording_url']);
        });

        Schema::table('recordings', function (Blueprint $table) {
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('recordings', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'file_path']);
            $table->string('recording_url')->nullable();
            $table->string('twilio_recording_sid')->unique()->nullable();
        });
    }
};
