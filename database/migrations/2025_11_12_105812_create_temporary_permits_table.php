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
        Schema::create('temporary_permits', function (Blueprint $table) {
            $table->id();
            $table->string('permit_id')->unique();
            $table->string('id_type'); // NIC/Passport/Driving License
            $table->string('id_number');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('full_name');
            $table->string('initials');
            $table->string('designation')->nullable();
            $table->string('company_name');
            $table->text('company_address')->nullable();
            $table->text('residence_address')->nullable();
            $table->string('pass_type'); // onboard/afloat/ashore
            $table->string('issue_type'); // free/payment
            $table->string('reason');
            
            // Document checkboxes
            $table->boolean('doc_nic')->default(false);
            $table->boolean('doc_passport')->default(false);
            $table->boolean('doc_driving_licence')->default(false);
            
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
            $table->index('id_number');
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
        Schema::dropIfExists('temporary_permits');
    }
};
