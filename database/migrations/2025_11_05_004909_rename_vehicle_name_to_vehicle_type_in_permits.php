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
        // Rename vehicle_name to vehicle_type if vehicle_name exists
        if (Schema::hasColumn('permits', 'vehicle_name') && !Schema::hasColumn('permits', 'vehicle_type')) {
            Schema::table('permits', function (Blueprint $table) {
                $table->renameColumn('vehicle_name', 'vehicle_type');
            });
        }
        // If neither exists, create vehicle_type
        elseif (!Schema::hasColumn('permits', 'vehicle_type')) {
            Schema::table('permits', function (Blueprint $table) {
                $table->string('vehicle_type')->nullable()->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('permits', 'vehicle_type')) {
            Schema::table('permits', function (Blueprint $table) {
                $table->renameColumn('vehicle_type', 'vehicle_name');
            });
        }
    }
};
