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
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn(['price_onboard', 'price_afloat', 'price_ashore']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->decimal('price_onboard', 10, 2)->default(100);
            $table->decimal('price_afloat', 10, 2)->default(80);
            $table->decimal('price_ashore', 10, 2)->default(50);
        });
    }
};
