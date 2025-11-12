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
        Schema::table('monthly_permits', function (Blueprint $table) {
            $table->renameColumn('police_report_issue_date', 'police_issue_date');
            $table->renameColumn('police_report_expire_date', 'police_expire_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_permits', function (Blueprint $table) {
            $table->renameColumn('police_issue_date', 'police_report_issue_date');
            $table->renameColumn('police_expire_date', 'police_report_expire_date');
        });
    }
};
