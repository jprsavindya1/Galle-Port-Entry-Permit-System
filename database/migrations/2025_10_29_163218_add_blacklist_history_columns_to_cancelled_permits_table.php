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
        Schema::table('cancelled_permits', function (Blueprint $table) {
            $table->timestamp('blacklisted_at')->nullable()->after('blacklist_reason');
            $table->string('blacklisted_by')->nullable()->after('blacklisted_at');
            $table->timestamp('blacklist_removed_at')->nullable()->after('blacklisted_by');
            $table->string('blacklist_removed_by')->nullable()->after('blacklist_removed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cancelled_permits', function (Blueprint $table) {
            $table->dropColumn(['blacklisted_at', 'blacklisted_by', 'blacklist_removed_at', 'blacklist_removed_by']);
        });
    }
};
