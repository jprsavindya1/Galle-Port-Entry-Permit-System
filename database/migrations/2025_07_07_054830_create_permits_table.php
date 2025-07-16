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
        Schema::create('permits', function (Blueprint $table) {
        $table->id();
        $table->string('type')->nullable(); // temporary/monthly/vehicle
        $table->string('id_type')->nullable(); // NIC/Passport/License
        $table->string('id_number')->nullable();
        $table->date('from_date')->nullable();
        $table->date('to_date')->nullable();
        $table->string('full_name')->nullable();
        $table->string('initials')->nullable();
        $table->string('designation')->nullable();
        $table->string('company_name')->nullable();
        $table->text('company_address')->nullable();
        $table->text('residence_address')->nullable();
        $table->string('pass_type')->nullable(); // onboard/afloat/ashore
        $table->string('issue_type')->nullable(); // free/payment
        $table->string('reason')->nullable();
        $table->string('id_document')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permits');
    }
};
