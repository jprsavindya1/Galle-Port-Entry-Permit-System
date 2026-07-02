# Pre-Migration Checklist for Separate Permit Tables

## ✅ Before Running Migrations

### 1. **BACKUP YOUR DATABASE** ⚠️ CRITICAL
```powershell
# Option 1: Using mysqldump (recommended)
mysqldump -u your_username -p your_database_name > backup_before_migration_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql

# Option 2: Export via phpMyAdmin
# Go to http://localhost/phpmyadmin
# Select your database → Export → Go
```

### 2. **Verify Current Data**
Check how many permits you currently have:
```sql
SELECT type, COUNT(*) as count FROM permits GROUP BY type;
```

Expected types:
- `TP` - Temporary Permits
- `MP` - Monthly Permits  
- `VP` - Vehicle Permits

### 3. **Check Dependencies**
Make sure all controllers have been updated:
- [x] PermitController
- [x] TemporaryPermitController
- [x] MonthlyPermitController
- [x] VehiclePermitController
- [x] DashboardController
- [x] ReportController
- [x] PrintController

### 4. **Routes Updated**
- [x] `/permits/{permitType}/{id}/edit`
- [x] `/permits/{permitType}/{id}` (update/delete)
- [x] `/permits/{permitType}/{id}/cancel`
- [x] `/permit/print/single/{permitType}/{id}`

### 5. **Models Created**
- [x] `App\Models\TemporaryPermit`
- [x] `App\Models\MonthlyPermit` (updated)
- [x] `App\Models\VehiclePermit`

---

## 🚀 Running the Migration

### Step 1: Run Migrations
```powershell
cd C:\xampp\htdocs\port-entry-permit
php artisan migrate
```

The migrations will run in this order:
1. Create `temporary_permits` table
2. Create `vehicle_permits` table
3. Update `monthly_permits` table (add new fields)
4. Migrate data from `permits` table to the three new tables

### Step 2: Verify Data Migration
```sql
-- Check temporary permits
SELECT COUNT(*) FROM temporary_permits;

-- Check monthly permits
SELECT COUNT(*) FROM monthly_permits;

-- Check vehicle permits
SELECT COUNT(*) FROM vehicle_permits;

-- Compare with original
SELECT COUNT(*) FROM permits;
```

All counts should match the original permit counts by type.

### Step 3: Test Basic Functionality
- [ ] Dashboard loads correctly
- [ ] Create a temporary permit
- [ ] Create a monthly permit
- [ ] Create a vehicle permit
- [ ] View submitted permits list
- [ ] Edit a permit
- [ ] Cancel a permit
- [ ] Print a permit
- [ ] Generate reports

---

## 🔄 Rollback Plan (If Something Goes Wrong)

### Option 1: Rollback Last Migration Batch
```powershell
php artisan migrate:rollback
```

### Option 2: Restore from Backup
```powershell
# Stop your application first
mysql -u your_username -p your_database_name < backup_before_migration_YYYYMMDD_HHMMSS.sql
```

---

## 📋 Post-Migration Tasks

### 1. **Clear Application Cache**
```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. **Update Views (if needed)**
Views that display permit data may need to be updated to:
- Pass `permit_type` parameter to routes
- Handle the new model structure
- Display permit type correctly

Common views to check:
- `resources/views/permit/submitted.blade.php`
- `resources/views/permit/edit.blade.php`
- `resources/views/permit/print_single.blade.php`
- `resources/views/admin/reports/user_report.blade.php`

### 3. **Test All Permit Operations**
- [ ] Create permits (all 3 types)
- [ ] Edit permits (all 3 types)
- [ ] Delete permits (all 3 types)
- [ ] Cancel permits (all 3 types)
- [ ] Print permits (all 3 types)
- [ ] Search permits
- [ ] Generate reports
- [ ] Check availability
- [ ] View dashboard statistics
- [ ] Export CSV/PDF reports

### 4. **Monitor for Errors**
Check logs after migration:
```powershell
# View Laravel logs
Get-Content storage/logs/laravel.log -Tail 50
```

---

## 🎯 Benefits After Migration

✅ **Better Data Integrity** - Each permit type has only relevant fields
✅ **Improved Performance** - Smaller tables, better indexes
✅ **Clearer Code** - Type-specific models and logic
✅ **Easier Maintenance** - Changes to one type don't affect others
✅ **Better Scalability** - Each table can be optimized independently

---

## ⚠️ Important Notes

1. **DO NOT** delete the old `permits` table immediately after migration
2. Keep it for at least 1-2 weeks as a backup
3. Monitor application behavior for any issues
4. If everything works correctly, you can optionally drop the old `permits` table later

### To Drop Old Table (ONLY AFTER THOROUGH TESTING):
```sql
-- WAIT AT LEAST 2 WEEKS BEFORE DOING THIS!
DROP TABLE permits;
```

---

## 🆘 Support & Troubleshooting

### Common Issues:

**Issue**: Data not migrated correctly
**Solution**: Check migration logs, verify permit type field values

**Issue**: Routes not working
**Solution**: Clear route cache: `php artisan route:clear`

**Issue**: Views showing errors
**Solution**: Update view files to pass `permit_type` parameter

**Issue**: Foreign key errors
**Solution**: Check `payments` table and `cancelled_permits` table relationships

---

**Migration Date**: _____________
**Performed By**: _____________
**Backup Location**: _____________
**Status**: [ ] Success  [ ] Failed  [ ] Rolled Back
