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
        Schema::table('cancelled_permits', function (Blueprint $table) {
            $table->softDeletes(); // adds deleted_at column (nullable timestamp)
           
        });
    }

    public function down(): void
    {
        Schema::table('cancelled_permits', function (Blueprint $table) {
            $table->dropSoftDeletes(); // removes deleted_at column
        });
    }
};
