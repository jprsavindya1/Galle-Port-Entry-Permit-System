<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('permits', function (Blueprint $table) {
        if (!Schema::hasColumn('permits', 'vehicle_type')) {
            $table->string('vehicle_type')->nullable();
        }
        if (!Schema::hasColumn('permits', 'vehicle_number')) {
            $table->string('vehicle_number')->nullable();
        }
        if (!Schema::hasColumn('permits', 'revenue_license_number')) {
            $table->string('revenue_license_number')->nullable();
        }
        if (!Schema::hasColumn('permits', 'owner_name')) {
            $table->string('owner_name')->nullable();
        }
        if (!Schema::hasColumn('permits', 'owner_address')) {
            $table->string('owner_address')->nullable();
        }
        if (!Schema::hasColumn('permits', 'remarks')) {
            $table->text('remarks')->nullable();
        }
    });
    
    // Drop old vehicle_name column if it exists
    Schema::table('permits', function (Blueprint $table) {
        if (Schema::hasColumn('permits', 'vehicle_name')) {
            $table->dropColumn('vehicle_name');
        }
    });
}

public function down()
{
    Schema::table('permits', function (Blueprint $table) {
        $table->dropColumn([
            'vehicle_type',
            'vehicle_number',
            'revenue_license_number',
            'owner_name',
            'owner_address',
            'remarks',
        ]);
    });
}

};
