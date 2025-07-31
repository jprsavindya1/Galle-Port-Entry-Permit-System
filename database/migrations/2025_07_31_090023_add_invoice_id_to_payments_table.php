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
    // Step 1: Add column without unique constraint
    Schema::table('payments', function (Blueprint $table) {
       $table->string('invoice_id', 25)->unique()->after('id');

    });

    // Step 2: Assign temporary unique values to existing rows
    DB::statement("
        UPDATE payments
        SET invoice_id = CONCAT('TEMP-', id)
        WHERE invoice_id IS NULL OR invoice_id = ''
    ");

    // Step 3: Add unique index
    Schema::table('payments', function (Blueprint $table) {
        $table->unique('invoice_id');
    });
}


    
};
