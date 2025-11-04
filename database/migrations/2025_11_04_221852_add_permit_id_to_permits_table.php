<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            if (!Schema::hasColumn('permits', 'permit_id')) {
                $table->string('permit_id')->nullable()->after('id');
            }
        });
        
        // Populate permit_id for existing records (use id as permit_id)
        DB::statement("UPDATE permits SET permit_id = CONCAT('P-', LPAD(id, 6, '0')) WHERE permit_id IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            if (Schema::hasColumn('permits', 'permit_id')) {
                $table->dropColumn('permit_id');
            }
        });
    }
};
