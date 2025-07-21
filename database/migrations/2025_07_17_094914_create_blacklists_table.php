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
         Schema::create('blacklists', function (Blueprint $table) {
        $table->id();
        $table->string('nic')->nullable();
        $table->string('name')->nullable();
        $table->string('company_name')->nullable();
        $table->enum('type', ['state', 'full']);
        $table->string('reason')->nullable(); // e.g., "expired police report"
        $table->date('expires_at')->nullable(); // optional for state-based
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklists');
    }
};
