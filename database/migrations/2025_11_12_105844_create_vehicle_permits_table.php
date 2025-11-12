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
        Schema::create('vehicle_permits', function (Blueprint $table) {
            $table->id();
            $table->string('permit_id')->unique();
            $table->string('vehicle_number');
            $table->string('vehicle_type');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('owner_name');
            $table->text('owner_address');
            $table->string('company_name');
            $table->string('issue_type'); // free/payment
            $table->string('reason');
            $table->string('revenue_license_number')->nullable();
            $table->string('insurance_number')->nullable();
            $table->text('remarks')->nullable();
            
            // Document checkboxes
            $table->boolean('doc_revenue_licence')->default(false);
            $table->boolean('doc_insurance')->default(false);
            
            // Payment fields
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('ssl', 10, 2)->nullable();
            $table->decimal('vat', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            
            $table->string('submission_id')->nullable();
            $table->string('status')->default('pending'); // pending/active/expired/cancelled
            $table->text('cancel_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('vehicle_number');
            $table->index('company_name');
            $table->index('submission_id');
            $table->index('status');
            $table->index(['from_date', 'to_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_permits');
    }
};
