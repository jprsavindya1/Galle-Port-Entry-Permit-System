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
        // Migrate temporary permits
        DB::table('permits')
            ->where('type', 'temporary')
            ->orderBy('id')
            ->chunk(100, function ($permits) {
                foreach ($permits as $permit) {
                    DB::table('temporary_permits')->insert([
                        'permit_id' => $permit->permit_id,
                        'id_type' => $permit->id_type,
                        'id_number' => $permit->id_number,
                        'from_date' => $permit->from_date,
                        'to_date' => $permit->to_date,
                        'full_name' => $permit->full_name,
                        'initials' => $permit->initials,
                        'designation' => $permit->designation,
                        'company_name' => $permit->company_name,
                        'company_address' => $permit->company_address,
                        'residence_address' => $permit->residence_address,
                        'pass_type' => $permit->pass_type,
                        'issue_type' => $permit->issue_type,
                        'reason' => $permit->reason,
                        'doc_nic' => $permit->doc_nic ?? false,
                        'doc_passport' => $permit->doc_passport ?? false,
                        'doc_driving_licence' => $permit->doc_driving_licence ?? false,
                        'rate' => $permit->rate,
                        'ssl' => $permit->ssl,
                        'vat' => $permit->vat,
                        'total' => $permit->total,
                        'submission_id' => $permit->submission_id,
                        'status' => $permit->status ?? 'pending',
                        'cancel_reason' => $permit->cancel_reason,
                        'created_at' => $permit->created_at,
                        'updated_at' => $permit->updated_at,
                        'deleted_at' => $permit->deleted_at,
                    ]);
                }
            });

        // Migrate monthly permits
        DB::table('permits')
            ->where('type', 'monthly')
            ->orderBy('id')
            ->chunk(100, function ($permits) {
                foreach ($permits as $permit) {
                    DB::table('monthly_permits')->insert([
                        'permit_id' => $permit->permit_id,
                        'id_type' => $permit->id_type ?? 'NIC',
                        'id_number' => $permit->id_number,
                        'from_date' => $permit->from_date,
                        'to_date' => $permit->to_date,
                        'full_name' => $permit->full_name,
                        'initials' => $permit->initials,
                        'designation' => $permit->designation,
                        'company_name' => $permit->company_name,
                        'company_address' => $permit->company_address,
                        'residence_address' => $permit->residence_address,
                        'pass_type' => $permit->pass_type,
                        'issue_type' => $permit->issue_type,
                        'reason' => $permit->reason,
                        'police_report_issue_date' => $permit->police_issue_date,
                        'police_report_expire_date' => $permit->police_expire_date,
                        'doc_nic' => $permit->doc_nic ?? false,
                        'doc_police_report' => $permit->doc_police_report ?? false,
                        'rate' => $permit->rate,
                        'ssl' => $permit->ssl,
                        'vat' => $permit->vat,
                        'total' => $permit->total,
                        'submission_id' => $permit->submission_id,
                        'status' => $permit->status ?? 'pending',
                        'cancel_reason' => $permit->cancel_reason,
                        'created_at' => $permit->created_at,
                        'updated_at' => $permit->updated_at,
                        'deleted_at' => $permit->deleted_at,
                    ]);
                }
            });

        // Migrate vehicle permits
        DB::table('permits')
            ->where('type', 'vehicle')
            ->orderBy('id')
            ->chunk(100, function ($permits) {
                foreach ($permits as $permit) {
                    DB::table('vehicle_permits')->insert([
                        'permit_id' => $permit->permit_id,
                        'vehicle_number' => $permit->vehicle_number,
                        'vehicle_type' => $permit->vehicle_type,
                        'from_date' => $permit->from_date,
                        'to_date' => $permit->to_date,
                        'owner_name' => $permit->owner_name,
                        'owner_address' => $permit->owner_address,
                        'company_name' => $permit->company_name,
                        'issue_type' => $permit->issue_type,
                        'reason' => $permit->reason,
                        'revenue_license_number' => $permit->revenue_license_number,
                        'insurance_number' => $permit->insurance_number,
                        'remarks' => $permit->remarks,
                        'doc_revenue_licence' => $permit->doc_revenue_licence ?? false,
                        'doc_insurance' => $permit->doc_insurance ?? false,
                        'rate' => $permit->rate,
                        'ssl' => $permit->ssl,
                        'vat' => $permit->vat,
                        'total' => $permit->total,
                        'submission_id' => $permit->submission_id,
                        'status' => $permit->status ?? 'pending',
                        'cancel_reason' => $permit->cancel_reason,
                        'created_at' => $permit->created_at,
                        'updated_at' => $permit->updated_at,
                        'deleted_at' => $permit->deleted_at,
                    ]);
                }
            });
            
        echo "Data migration completed successfully!\n";
        echo "Temporary permits migrated: " . DB::table('temporary_permits')->count() . "\n";
        echo "Monthly permits migrated: " . DB::table('monthly_permits')->count() . "\n";
        echo "Vehicle permits migrated: " . DB::table('vehicle_permits')->count() . "\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the migrated data (optional - you may want to keep it)
        DB::table('temporary_permits')->truncate();
        DB::table('vehicle_permits')->truncate();
        // Monthly permits table existed before, so we only delete the migrated records
        DB::table('monthly_permits')->whereNotNull('permit_id')->delete();
    }
};
