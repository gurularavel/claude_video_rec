<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_sessions', function (Blueprint $table) {
            $table->string('customer_phone')->nullable()->after('customer_email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_superadmin')->default(false)->after('is_operator');
        });
    }

    public function down(): void
    {
        Schema::table('support_sessions', function (Blueprint $table) {
            $table->dropColumn('customer_phone');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_superadmin');
        });
    }
};
