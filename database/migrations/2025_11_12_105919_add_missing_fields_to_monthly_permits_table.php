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
        Schema::table('monthly_permits', function (Blueprint $table) {
            $table->string('permit_id')->unique()->after('id');
            
            // Document checkboxes
            $table->boolean('doc_nic')->default(false)->after('reason');
            $table->boolean('doc_police_report')->default(false)->after('doc_nic');
            
            // Payment fields
            $table->decimal('rate', 10, 2)->nullable()->after('doc_police_report');
            $table->decimal('ssl', 10, 2)->nullable()->after('rate');
            $table->decimal('vat', 10, 2)->nullable()->after('ssl');
            $table->decimal('total', 10, 2)->nullable()->after('vat');
            
            $table->string('status')->default('pending')->after('submission_id'); // pending/active/expired/cancelled
            $table->text('cancel_reason')->nullable()->after('status');
            
            $table->softDeletes()->after('updated_at');
            
            // Indexes
            $table->index('id_number');
            $table->index('company_name');
            $table->index('status');
            $table->index(['from_date', 'to_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_permits', function (Blueprint $table) {
            $table->dropColumn([
                'permit_id',
                'doc_nic',
                'doc_police_report',
                'rate',
                'ssl',
                'vat',
                'total',
                'status',
                'cancel_reason',
                'deleted_at'
            ]);
            
            $table->dropIndex(['id_number']);
            $table->dropIndex(['company_name']);
            $table->dropIndex(['status']);
            $table->dropIndex(['from_date', 'to_date']);
        });
    }
};
