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
        Schema::table('permits', function (Blueprint $table) {
    $table->decimal('rate', 10, 2)->default(0);
    $table->decimal('ssl', 10, 2)->default(0);
    $table->decimal('vat', 10, 2)->default(0);
    $table->decimal('total', 10, 2)->default(0);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            //
        });
    }
};
