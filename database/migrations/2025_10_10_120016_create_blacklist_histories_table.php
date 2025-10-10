<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blacklist_histories', function (Blueprint $table) {
            $table->id();
            
            // Original blacklist data
            $table->string('nic')->nullable();
            $table->string('full_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('reason')->nullable();

            // Action tracking
            $table->string('action'); // created, updated, deleted, reinstated
            $table->unsignedBigInteger('blacklist_id')->nullable(); // original blacklist id
            $table->unsignedBigInteger('admin_id')->nullable(); // who did the action
            $table->string('admin_name')->nullable();
            $table->string('admin_role')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklist_histories');
    }
};
