# Quick Reference - Key Documentation Updates

**Last Updated:** December 10, 2025  
**Purpose:** Quick reference for the most important documentation changes

---

## 🔑 Critical Updates

### 1. Database Field Names ⚠️ IMPORTANT

#### MonthlyPermit Police Report Fields
```php
// ✅ CORRECT (Current Implementation)
'police_issue_date'
'police_expire_date'

// ❌ WRONG (Old/Incorrect)
'police_report_issue_date'
'police_report_expire_date'
```

#### All Permit Tables Now Include
```php
'application_number'  // For cart tracking
'permit_id'          // Unique permit identifier
'status'             // Default: 'pending'
'is_printed'         // Print tracking
'printed_at'         // Print timestamp
'printed_by'         // User who printed
```

#### TemporaryPermit & VehiclePermit Include
```php
'nic_number'         // For NIC storage
```

---

### 2. User Roles (Database Values)

```php
// ✅ CORRECT (Actual Database Values)
'super-admin'  // Not 'Super Admin'
'admin'        // Not 'Admin'
'clerk'        // Not 'Clerk'
'staff'        // Not 'Staff'
```

**Usage in Code:**
```php
if (auth()->user()->role === 'super-admin') {
    // Super admin logic
}
```

---

### 3. ID Generation - Advisory Locks

```php
// ✅ CORRECT (Current Implementation)
// Uses MySQL Advisory Locks
$lockName = 'app_number_generation';
$lockResult = DB::selectOne("SELECT GET_LOCK(?, 10) as locked", [$lockName]);

try {
    // Generate ID
} finally {
    DB::selectOne("SELECT RELEASE_LOCK(?) as released", [$lockName]);
}

// ❌ WRONG (Not Used)
// Table-level locks are NOT used
DB::statement('LOCK TABLES table_name WRITE');
```

**Key Points:**
- Uses named advisory locks
- 10-second timeout on all locks
- Automatic release in finally blocks
- Excludes soft-deleted records with `whereNull('deleted_at')`

---

### 4. Print Tracking Implementation

```php
// ✅ CORRECT (Current Implementation)
// PrintController uses update() method
foreach ($tempPermits as $permit) {
    $permit->update([
        'is_printed' => true,
        'printed_at' => now(),
        'printed_by' => auth()->id(),
    ]);
}

// For single permit
$permit->update([
    'is_printed' => true,
    'printed_at' => now(),
    'printed_by' => auth()->id(),
]);
```

---

### 5. Session Configuration

```php
// ✅ RECOMMENDED (Production)
SESSION_DRIVER=database     // or redis
SESSION_LIFETIME=120        // 2 hours in minutes
SESSION_ENCRYPT=false       // true if very sensitive data
```

**Exception Handling in bootstrap/app.php:**
```php
// Handles expired sessions
$exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Your session has expired...'], 401);
    }
    return redirect()->route('login')->with('error', 'Your session has expired...');
});

// Handles expired CSRF tokens
$exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Your session has expired...'], 419);
    }
    return redirect()->route('login')->with('error', 'Your session has expired...');
});
```

---

### 6. Seeded User Credentials

```php
// ✅ ALL SEEDED USERS (Default Password: 'password')
[
    ['email' => 'superadmin@slpa.lk', 'role' => 'super-admin'],
    ['email' => 'admin@slpa.lk',      'role' => 'admin'],
    ['email' => 'clerk1@slpa.lk',     'role' => 'clerk'],
    ['email' => 'clerk2@slpa.lk',     'role' => 'clerk'],
    ['email' => 'staff@slpa.lk',      'role' => 'staff'],
]
```

⚠️ **CRITICAL:** Change all passwords before production deployment!

---

### 7. Separate Permit Tables

```php
// ✅ CORRECT (Current Structure)
use App\Models\TemporaryPermit;  // For TP type
use App\Models\MonthlyPermit;    // For MP type
use App\Models\VehiclePermit;    // For VH type

// ❌ WRONG (Old Structure)
use App\Models\Permit;  // No longer exists
```

**Route Usage:**
```php
// ✅ CORRECT
route('permits.edit', ['temporary', $id])
route('permits.edit', ['monthly', $id])
route('permits.edit', ['vehicle', $id])

// ❌ WRONG
route('permits.edit', $id)  // Missing permit type
```

---

### 8. Invoice ID Format

```php
// ✅ CORRECT (Current Format)
IdGeneratorHelper::generateInvoiceId('TP')  // Returns: INV-T25120101
IdGeneratorHelper::generateInvoiceId('MP')  // Returns: INV-M25120101
IdGeneratorHelper::generateInvoiceId('VH')  // Returns: INV-V25120101

// Format: INV-[T|M|V] + YY + MM + ##
// T = Temporary, M = Monthly, V = Vehicle
```

---

### 9. Vehicle Types (All 20 Seeded)

```
MC   - Motorcycle         (200.00)
TW   - Three Wheeler      (300.00)
CAR  - Car                (500.00)
VAN  - Van                (750.00)
MB   - Mini Bus           (800.00)    ← NEW
BUS  - Bus                (1000.00)
LS   - Lorry (Small)      (1000.00)
LM   - Lorry (Medium)     (1500.00)
LL   - Lorry (Large)      (2000.00)
FL   - Forklift           (2000.00)
TRK  - Truck              (2500.00)
PM   - Prime Mover        (3000.00)
TRL  - Trailer            (3500.00)
TNK  - Tanker             (3500.00)
CT   - Container Trailer  (4000.00)
HE   - Heavy Equipment    (4000.00)
CRN  - Crane              (5000.00)
BWS  - Bowser             (2800.00)   ← NEW
LL   - Low Loader         (4500.00)   ← NEW
OTH  - Other Vehicle      (1000.00)   ← NEW
```

---

## 📋 Quick Checks Before Deployment

### Pre-Deployment Checklist
```bash
# 1. Verify environment
[ ] APP_ENV=production
[ ] APP_DEBUG=false
[ ] APP_KEY generated
[ ] Database credentials correct
[ ] Session driver set (database/redis)

# 2. Security
[ ] All default passwords changed
[ ] .env file not in Git
[ ] File permissions set (755/644)
[ ] HTTPS configured

# 3. Database
[ ] Migrations run: php artisan migrate
[ ] Seeders run (optional): php artisan db:seed
[ ] Backup system configured

# 4. Performance
[ ] Config cached: php artisan config:cache
[ ] Routes cached: php artisan route:cache
[ ] Views cached: php artisan view:cache

# 5. Testing
[ ] Login with all user roles
[ ] Create all permit types
[ ] Print permits (batch and single)
[ ] Test session expiration
[ ] Test blacklist validation
```

---

## 🔗 Documentation Files Reference

### Core System Documentation
- **README.md** - Main project documentation
- **QUICK_START.md** - 5-minute setup guide
- **INSTALLATION.md** - Complete installation instructions

### Technical Guides
- **ID_GENERATION_SYSTEM.md** - ID generation with advisory locks
- **PRINT_STATUS_TRACKING.md** - Print tracking implementation
- **SESSION_HANDLING.md** - Session and exception handling
- **SEPARATE_TABLES_GUIDE.md** - Separate permit tables usage

### Deployment Guides
- **DEPLOYMENT.md** - Production deployment steps
- **ENV_CONFIGURATION.md** - Environment configuration
- **HOSTING_CHECKLIST.md** - Hosting deployment checklist

### Database Guides
- **MIGRATION_SUMMARY.md** - Database migrations overview
- **SEEDING_SUMMARY.md** - Database seeders reference
- **PRE_MIGRATION_CHECKLIST.md** - Pre-migration checklist

### Maintenance Guides
- **MAINTENANCE.md** - System maintenance procedures
- **HANDOVER_CHECKLIST.md** - Project handover checklist
- **TEST_SESSION_EXPIRY.md** - Session testing guide

### Update Documentation
- **DOCUMENTATION_UPDATE_SUMMARY.md** - All changes made
- **DOCUMENTATION_VERIFICATION_CHECKLIST.md** - Verification results

---

## ⚡ Common Commands

```bash
# Development
php artisan serve                    # Start dev server
npm run dev                         # Build assets with hot reload

# Database
php artisan migrate                 # Run migrations
php artisan migrate:fresh --seed   # Fresh database with seeds
php artisan db:seed                # Run seeders only

# Cache
php artisan optimize:clear         # Clear all caches
php artisan config:cache          # Cache configuration
php artisan route:cache           # Cache routes
php artisan view:cache            # Cache views

# Queue
php artisan queue:work            # Process queue jobs
php artisan queue:failed          # Show failed jobs
php artisan queue:retry all       # Retry failed jobs

# Session Testing
# Set in .env: SESSION_LIFETIME=2 (for 2-minute test)
```

---

## 📞 Support Resources

- **GitHub Repository:** [SLPA-Permit_System](https://github.com/sahanSS98/SLPA-Permit_System)
- **Documentation Files:** Located in project root directory
- **Issue Tracking:** GitHub Issues

---

**Quick Reference Version:** 1.0  
**System Version:** 1.0 (Production Ready)  
**Last Updated:** December 10, 2025
