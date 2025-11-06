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
            // Temporary permit documents: NIC, Passport, Driving Licence
            if (!Schema::hasColumn('permits', 'doc_nic')) {
                $table->boolean('doc_nic')->default(false)->after('reason');
            }
            if (!Schema::hasColumn('permits', 'doc_passport')) {
                $table->boolean('doc_passport')->default(false)->after('doc_nic');
            }
            if (!Schema::hasColumn('permits', 'doc_driving_licence')) {
                $table->boolean('doc_driving_licence')->default(false)->after('doc_passport');
            }
            
            // Monthly permit documents: Police Report (NIC already added above)
            if (!Schema::hasColumn('permits', 'doc_police_report')) {
                $table->boolean('doc_police_report')->default(false)->after('doc_driving_licence');
            }
            
            // Vehicle permit documents: Revenue Licence, Insurance
            if (!Schema::hasColumn('permits', 'doc_revenue_licence')) {
                $table->boolean('doc_revenue_licence')->default(false)->after('doc_police_report');
            }
            if (!Schema::hasColumn('permits', 'doc_insurance')) {
                $table->boolean('doc_insurance')->default(false)->after('doc_revenue_licence');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permits', function (Blueprint $table) {
            $columns = ['doc_nic', 'doc_passport', 'doc_driving_licence', 'doc_police_report', 'doc_revenue_licence', 'doc_insurance'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('permits', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
