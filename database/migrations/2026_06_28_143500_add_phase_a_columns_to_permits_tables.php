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
        Schema::table('temporary_permits', function (Blueprint $table) {
            if (!Schema::hasColumn('temporary_permits', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('temporary_permits', 'doc_nic_path')) {
                $table->string('doc_nic_path')->nullable()->after('photo_path');
            }
            if (!Schema::hasColumn('temporary_permits', 'doc_passport_path')) {
                $table->string('doc_passport_path')->nullable()->after('doc_nic_path');
            }
            if (!Schema::hasColumn('temporary_permits', 'doc_driving_licence_path')) {
                $table->string('doc_driving_licence_path')->nullable()->after('doc_passport_path');
            }
            if (!Schema::hasColumn('temporary_permits', 'yacht_name')) {
                $table->string('yacht_name')->nullable()->after('doc_driving_licence_path');
            }
            if (!Schema::hasColumn('temporary_permits', 'yacht_agent')) {
                $table->string('yacht_agent')->nullable()->after('yacht_name');
            }
            if (!Schema::hasColumn('temporary_permits', 'passport_country')) {
                $table->string('passport_country')->nullable()->after('yacht_agent');
            }
            if (!Schema::hasColumn('temporary_permits', 'visa_expiry')) {
                $table->date('visa_expiry')->nullable()->after('passport_country');
            }
            if (!Schema::hasColumn('temporary_permits', 'customs_clearance')) {
                $table->boolean('customs_clearance')->default(false)->after('visa_expiry');
            }
        });

        Schema::table('monthly_permits', function (Blueprint $table) {
            if (!Schema::hasColumn('monthly_permits', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('monthly_permits', 'doc_nic_path')) {
                $table->string('doc_nic_path')->nullable()->after('photo_path');
            }
            if (!Schema::hasColumn('monthly_permits', 'doc_police_report_path')) {
                $table->string('doc_police_report_path')->nullable()->after('doc_nic_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_permits', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path',
                'doc_nic_path',
                'doc_passport_path',
                'doc_driving_licence_path',
                'yacht_name',
                'yacht_agent',
                'passport_country',
                'visa_expiry',
                'customs_clearance',
            ]);
        });

        Schema::table('monthly_permits', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path',
                'doc_nic_path',
                'doc_police_report_path',
            ]);
        });
    }
};
