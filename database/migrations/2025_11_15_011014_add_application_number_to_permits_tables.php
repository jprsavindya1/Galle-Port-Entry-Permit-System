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
        // Add application_number to temporary_permits
        Schema::table('temporary_permits', function (Blueprint $table) {
            $table->string('application_number')->unique()->nullable()->after('permit_id');
            $table->index('application_number');
        });

        // Add application_number to monthly_permits
        Schema::table('monthly_permits', function (Blueprint $table) {
            $table->string('application_number')->unique()->nullable()->after('permit_id');
            $table->index('application_number');
        });

        // Add application_number to vehicle_permits
        Schema::table('vehicle_permits', function (Blueprint $table) {
            $table->string('application_number')->unique()->nullable()->after('permit_id');
            $table->index('application_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_permits', function (Blueprint $table) {
            $table->dropIndex(['application_number']);
            $table->dropColumn('application_number');
        });

        Schema::table('monthly_permits', function (Blueprint $table) {
            $table->dropIndex(['application_number']);
            $table->dropColumn('application_number');
        });

        Schema::table('vehicle_permits', function (Blueprint $table) {
            $table->dropIndex(['application_number']);
            $table->dropColumn('application_number');
        });
    }
};
