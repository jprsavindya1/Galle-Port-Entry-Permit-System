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
        Schema::create('cancelled_permits', function (Blueprint $table) {
            $table->id();

            // Links back to original permit & invoice
            $table->string('permit_id');
            $table->string('submission_id')->nullable();
            $table->string('invoice_id')->nullable();

            // Core permit info
            $table->string('type'); // TP, MP, VP
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('full_name')->nullable();
            $table->string('initials')->nullable();
            $table->string('designation')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('residence_address')->nullable();

            // Vehicle specific
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('revenue_license_number')->nullable();
            $table->string('insurance_number')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_address')->nullable();

            // Dates
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->date('police_issue_date')->nullable();
            $table->date('police_expire_date')->nullable();

            // Other details
            $table->string('pass_type')->nullable();
            $table->string('issue_type')->nullable();
            $table->string('reason')->nullable();
            $table->string('remarks')->nullable();
            $table->string('id_document')->nullable();

            // Cancellation info
            $table->string('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->useCurrent();
            $table->string('cancelled_by')->nullable(); // admin/user name/id

            // Blacklist info at cancellation time (snapshot)
            $table->string('blacklist_status')->nullable();
            $table->string('blacklist_reason')->nullable();

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_permits');
    }
};
