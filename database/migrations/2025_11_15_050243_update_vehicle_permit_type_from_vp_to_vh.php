<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration updates all existing vehicle permit type codes from 'VP' to 'VH'
     * in tables that have a 'type' column (old permits table, cancelled_permits, payments).
     * 
     * Note: temporary_permits, monthly_permits, and vehicle_permits tables don't have
     * a 'type' column since they're already separated by table structure.
     */
    public function up(): void
    {
        // Update old permits table (if it exists and has data)
        if (Schema::hasTable('permits') && Schema::hasColumn('permits', 'type')) {
            $count = DB::table('permits')
                ->where('type', 'VP')
                ->update(['type' => 'VH']);
            if ($count > 0) {
                echo "Updated {$count} records in permits table from VP to VH\n";
            }
        }

        // Update cancelled_permits table
        if (Schema::hasTable('cancelled_permits') && Schema::hasColumn('cancelled_permits', 'type')) {
            $count = DB::table('cancelled_permits')
                ->where('type', 'VP')
                ->update(['type' => 'VH']);
            if ($count > 0) {
                echo "Updated {$count} records in cancelled_permits table from VP to VH\n";
            }
        }

        // Update payments table (permit_type column)
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'permit_type')) {
            $count = DB::table('payments')
                ->where('permit_type', 'VP')
                ->update(['permit_type' => 'VH']);
            if ($count > 0) {
                echo "Updated {$count} records in payments table from VP to VH\n";
            }
        }

        echo "Migration completed successfully!\n";
    }

    /**
     * Reverse the migrations.
     * 
     * Rollback: Change all VH back to VP
     */
    public function down(): void
    {
        // Rollback old permits table
        if (Schema::hasTable('permits') && Schema::hasColumn('permits', 'type')) {
            DB::table('permits')
                ->where('type', 'VH')
                ->update(['type' => 'VP']);
        }

        // Rollback cancelled_permits table
        if (Schema::hasTable('cancelled_permits') && Schema::hasColumn('cancelled_permits', 'type')) {
            DB::table('cancelled_permits')
                ->where('type', 'VH')
                ->update(['type' => 'VP']);
        }

        // Rollback payments table
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'permit_type')) {
            DB::table('payments')
                ->where('permit_type', 'VH')
                ->update(['permit_type' => 'VP']);
        }

        echo "Rollback completed successfully!\n";
    }
};
