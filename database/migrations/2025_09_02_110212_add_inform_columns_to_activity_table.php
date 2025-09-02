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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add only the new columns
            $table->string('user_name')->nullable()->after('user_id');
            $table->string('role')->nullable()->after('user_name');
            $table->string('ip_address', 45)->nullable()->after('details');
            $table->string('user_agent', 255)->nullable()->after('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['user_name', 'role', 'ip_address', 'user_agent']);
        });
    }
};
