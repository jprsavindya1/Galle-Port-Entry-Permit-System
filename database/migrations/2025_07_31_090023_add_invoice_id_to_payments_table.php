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
        // Check if column already exists before adding
        if (!Schema::hasColumn('payments', 'invoice_id')) {
            // Step 1: Add column with unique constraint
            Schema::table('payments', function (Blueprint $table) {
               $table->string('invoice_id', 25)->after('id');
            });

            // Step 2: Assign temporary unique values to existing rows
            DB::statement("
                UPDATE payments
                SET invoice_id = CONCAT('TEMP-', id)
                WHERE invoice_id IS NULL OR invoice_id = ''
            ");

            // Step 3: Add unique index
            Schema::table('payments', function (Blueprint $table) {
                $table->unique('invoice_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
        });
    }
};
