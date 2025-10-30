<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('payment_settings', function (Blueprint $table) {
        $table->id();
        $table->decimal('rate', 10, 2)->default(100);
        $table->decimal('ssl', 5, 2)->default(2.5);    // SSL percent (e.g., 2.5%)
        $table->decimal('vat', 5, 2)->default(18);     // VAT percent (e.g., 18%)
        $table->decimal('price_onboard', 10, 2)->default(100);
        $table->decimal('price_afloat', 10, 2)->default(80);
        $table->decimal('price_ashore', 10, 2)->default(50);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
