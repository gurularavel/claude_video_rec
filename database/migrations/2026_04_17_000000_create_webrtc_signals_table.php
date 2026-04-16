<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webrtc_signals', function (Blueprint $table) {
            $table->id();
            $table->string('session_uuid', 36)->index();
            $table->enum('direction', ['to_operator', 'to_customer']);
            $table->string('type', 50);
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['session_uuid', 'direction', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webrtc_signals');
    }
};
