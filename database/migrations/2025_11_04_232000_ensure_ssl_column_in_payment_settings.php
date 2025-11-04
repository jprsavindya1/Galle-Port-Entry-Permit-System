<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            // Add SSL column if it doesn't exist (was removed by previous migration)
            if (!Schema::hasColumn('payment_settings', 'ssl')) {
                $table->decimal('ssl', 10, 2)->default(2.5)->after('rate');
            }
        });
        
        // Update existing records to have default SSL value if it's 0 or null
        try {
            DB::table('payment_settings')
                ->whereNull('ssl')
                ->orWhere('ssl', 0)
                ->update(['ssl' => 2.5]);
        } catch (\Exception $e) {
            // Silently fail if no records exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            if (Schema::hasColumn('payment_settings', 'ssl')) {
                $table->dropColumn('ssl');
            }
        });
    }
};
