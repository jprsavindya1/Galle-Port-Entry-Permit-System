# Database Migration Summary

## Overview
This document outlines the database migrations created to split the single `permits` table into three separate tables: `temporary_permits`, `monthly_permits`, and `vehicle_permits`.

## Migration Files (In Order)

### 1. `2025_11_12_105812_create_temporary_permits_table.php`
**Purpose:** Create the `temporary_permits` table for temporary permit entries (TP type)

**Fields:**
- Core: permit_id (unique), id_type, id_number, from_date, to_date
- Person: full_name, initials, designation
- Company: company_name, company_address, residence_address
- Permit: pass_type, issue_type, reason
- Documents: doc_nic, doc_passport, doc_driving_licence
- Payment: rate, ssl, vat, total
- Status: submission_id, status (default: 'pending'), cancel_reason
- Timestamps: created_at, updated_at, deleted_at (soft deletes)

**Indexes:** permit_id, id_number, company_name, submission_id, status, from_date, to_date

---

### 2. `2025_11_12_105844_create_vehicle_permits_table.php`
**Purpose:** Create the `vehicle_permits` table for vehicle permit entries (VP type)

**Fields:**
- Core: permit_id (unique), vehicle_number, vehicle_type, from_date, to_date
- Owner: owner_name, owner_address
- Company: company_name
- Permit: issue_type, reason, remarks
- Documents: doc_revenue_licence, doc_insurance
- Vehicle: revenue_license_number, insurance_number
- Payment: rate, ssl, vat, total
- Status: submission_id, status (default: 'pending'), cancel_reason
- Timestamps: created_at, updated_at, deleted_at (soft deletes)

**Indexes:** permit_id, vehicle_number, company_name, submission_id, status, from_date, to_date

---

### 3. `2025_11_12_105919_add_missing_fields_to_monthly_permits_table.php`
**Purpose:** Add missing fields to existing `monthly_permits` table (MP type)

**Note:** The `monthly_permits` table already existed with basic fields. This migration adds:
- permit_id (unique)
- doc_nic, doc_police_report (document checkboxes)
- rate, ssl, vat, total (payment fields)
- status (default: 'pending'), cancel_reason
- deleted_at (soft deletes)
- Indexes on id_number, company_name, status, from_date, to_date

---

### 4. `2025_11_12_105951_migrate_data_from_permits_to_separate_tables.php`
**Purpose:** Migrate existing data from old `permits` table to the three new tables

**Process:**
- Migrates records WHERE type = 'temporary' → `temporary_permits`
- Migrates records WHERE type = 'monthly' → `monthly_permits`
- Migrates records WHERE type = 'vehicle' → `vehicle_permits`

**Important Notes:**
- Uses chunk(100) for memory efficiency
- Maps field names (e.g., `police_issue_date` from permits → `police_report_issue_date` in monthly_permits)
- Sets default values (status = 'pending' if null)
- Preserves timestamps and soft delete status
- Outputs migration counts for verification

**Down Method:** Truncates temporary_permits and vehicle_permits, deletes migrated monthly_permits records

---

### 5. `2025_11_12_125407_rename_police_report_dates_in_monthly_permits_table.php`
**Purpose:** Standardize police report date field names across all tables

**Changes:**
- Renames `police_report_issue_date` → `police_issue_date`
- Renames `police_report_expire_date` → `police_expire_date`

**Reason:** The old `permits` table uses `police_issue_date` and `police_expire_date`. To avoid field mapping confusion and maintain consistency, all tables now use the same field names.

---

## Deployment Instructions

### Fresh Installation (New Server)
1. Run all migrations in order:
   ```bash
   php artisan migrate
   ```

2. Verify migration:
   ```bash
   php artisan migrate:status
   ```

3. Check table structures:
   ```bash
   php artisan tinker
   >>> \DB::select('SHOW TABLES');
   >>> \DB::select('DESCRIBE temporary_permits');
   >>> \DB::select('DESCRIBE monthly_permits');
   >>> \DB::select('DESCRIBE vehicle_permits');
   ```

### Updating Existing Installation
1. **IMPORTANT:** Backup the database first:
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. Run new migrations:
   ```bash
   php artisan migrate
   ```

3. Verify data migration:
   ```bash
   php artisan tinker
   >>> "Old table count: " . \App\Models\Permit::count()
   >>> "Temporary permits: " . \App\Models\TemporaryPermit::count()
   >>> "Monthly permits: " . \App\Models\MonthlyPermit::count()
   >>> "Vehicle permits: " . \App\Models\VehiclePermit::count()
   ```

4. If counts don't match, check the migration logs and verify the type values in the old permits table

---

## Code Changes Summary

### Models Created/Updated
- **TemporaryPermit** (new): `app/Models/TemporaryPermit.php`
- **MonthlyPermit** (updated): `app/Models/MonthlyPermit.php`
- **VehiclePermit** (new): `app/Models/VehiclePermit.php`

### Controllers Updated
- **PermitController**: Updated `submittedList()`, `edit()`, `update()`, `destroy()`, `cancel()`, `activate()`
- **PaymentController**: Updated `submit()` to save to both old and new tables
- **MonthlyPermitController**: Updated validation rules for police date fields

### Routes Updated
All permit routes now use `{permitType}/{id}` pattern:
- `permits/{permitType}/{id}/edit`
- `permits/{permitType}/{id}` (update/delete)
- `permits/{permitType}/{id}/cancel`
- `permits/{permitType}/{id}/activate`

### Views Updated
- **submitted.blade.php**: Updated JavaScript for cancel/activate functionality
- **monthly.blade.php**: Added police report date input fields

---

## Important Notes for Hosting

1. **Dual-Table Saving**: The system currently saves to BOTH the new separate tables AND the old `permits` table for backward compatibility. After confidence period, you can:
   - Remove `Permit::create()` calls from PaymentController
   - Drop or archive the old `permits` table
   - Remove dual-update logic from cancel/activate methods

2. **Field Name Consistency**: All tables now use `police_issue_date` and `police_expire_date` for consistency

3. **Soft Deletes**: All permit tables use soft deletes - records are never permanently deleted

4. **Console Logging**: The submitted.blade.php currently has console.log statements for debugging. Consider removing these in production

5. **Migration Order**: The migrations MUST run in the order specified by their timestamps. Do not reorder or rename them.

---

## Rollback Plan

If issues occur, rollback steps:

1. Rollback migrations:
   ```bash
   php artisan migrate:rollback --step=5
   ```

2. Restore from backup:
   ```bash
   mysql -u username -p database_name < backup_YYYYMMDD_HHMMSS.sql
   ```

3. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

---

## Testing Checklist

- [ ] Create new temporary permit
- [ ] Create new monthly permit
- [ ] Create new vehicle permit
- [ ] View submitted permits list
- [ ] Cancel a permit (admin only)
- [ ] Activate a cancelled permit (admin only)
- [ ] Print single permit
- [ ] View group invoice
- [ ] Generate user report
- [ ] Generate revenue report
- [ ] Verify all 24 existing permits are visible
- [ ] Verify permit IDs don't duplicate

---

**Last Updated:** November 12, 2025
