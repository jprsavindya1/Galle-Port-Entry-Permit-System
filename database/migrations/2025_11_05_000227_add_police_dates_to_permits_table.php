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
            if (!Schema::hasColumn('permits', 'police_issue_date')) {
                $table->date('police_issue_date')->nullable()->after('cancel_reason');
            }
            if (!Schema::hasColumn('permits', 'police_expire_date')) {
                $table->date('police_expire_date')->nullable()->after('police_issue_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            if (Schema::hasColumn('permits', 'police_issue_date')) {
                $table->dropColumn('police_issue_date');
            }
            if (Schema::hasColumn('permits', 'police_expire_date')) {
                $table->dropColumn('police_expire_date');
            }
        });
    }
};
