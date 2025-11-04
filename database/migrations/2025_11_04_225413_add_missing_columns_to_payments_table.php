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
        Schema::table('payments', function (Blueprint $table) {
            // Add permit_type column
            if (!Schema::hasColumn('payments', 'permit_type')) {
                $table->string('permit_type')->nullable()->after('invoice_id');
            }
            
            // Add entry_count column
            if (!Schema::hasColumn('payments', 'entry_count')) {
                $table->integer('entry_count')->default(1)->after('permit_type');
            }
            
            // Add rate_total column
            if (!Schema::hasColumn('payments', 'rate_total')) {
                $table->decimal('rate_total', 10, 2)->default(0)->after('entry_count');
            }
            
            // Ensure ssl_total exists (might already exist from another migration)
            if (!Schema::hasColumn('payments', 'ssl_total')) {
                $table->decimal('ssl_total', 10, 2)->default(0)->after('rate_total');
            }
            
            // Add vat_total column
            if (!Schema::hasColumn('payments', 'vat_total')) {
                $table->decimal('vat_total', 10, 2)->default(0)->after('ssl_total');
            }
            
            // Add amount_total column (rename from 'amount' if needed)
            if (!Schema::hasColumn('payments', 'amount_total')) {
                $table->decimal('amount_total', 10, 2)->default(0)->after('vat_total');
            }
            
            // Add paid_at column
            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_date');
            }
        });
        
        // Drop old 'amount' column if it exists and 'amount_total' was created
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'amount') && Schema::hasColumn('payments', 'amount_total')) {
                $table->dropColumn('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'permit_type',
                'entry_count',
                'rate_total',
                'vat_total',
                'amount_total',
                'paid_at'
            ]);
        });
    }
};
