<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_permits', function (Blueprint $table) {
            $table->id();
            $table->string('id_type')->default('NIC'); // Only NIC
            $table->string('id_number');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('full_name');
            $table->string('initials');
            $table->string('designation')->nullable();
            $table->string('company_name');
            $table->text('company_address')->nullable();
            $table->text('residence_address')->nullable();
            $table->string('pass_type');
            $table->string('issue_type'); // free or payment
            $table->string('reason');
            $table->date('police_report_issue_date');
            $table->date('police_report_expire_date');
            $table->string('submission_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_permits');
    }
};

