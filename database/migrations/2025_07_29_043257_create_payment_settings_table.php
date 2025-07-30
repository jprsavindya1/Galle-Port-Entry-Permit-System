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
        $table->decimal('nbt', 5, 2)->default(2);      // percent
        $table->decimal('vat', 5, 2)->default(15);     // percent
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
