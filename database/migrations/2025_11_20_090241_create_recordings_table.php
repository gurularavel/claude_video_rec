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
        Schema::create('recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_session_id')->constrained()->cascadeOnDelete();
            $table->string('twilio_recording_sid')->unique();
            $table->string('recording_url')->nullable();
            $table->string('recording_status')->nullable();
            $table->integer('duration')->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('format')->default('mp4');
            $table->timestamp('recording_started_at')->nullable();
            $table->timestamp('recording_completed_at')->nullable();
            $table->timestamps();

            $table->index('support_session_id');
            $table->index('twilio_recording_sid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recordings');
    }
};
