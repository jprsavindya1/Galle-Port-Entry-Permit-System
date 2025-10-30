# Database Seeders Documentation

## Overview
This directory contains seeders for populating the SLPA Port Entry Permit System with initial and sample data.

## Available Seeders

### 1. DatabaseSeeder (Main)
The main seeder that orchestrates all other seeders.

**Usage:**
```bash
php artisan db:seed
```

### 2. UserSeeder
Creates default system users with different roles.

**Created Users:**
- **Super Admin** (superadmin@slpa.lk) - Full system access
- **Admin** (admin@slpa.lk) - Administrative access
- **Clerk 1** (clerk1@slpa.lk) - Permit management
- **Clerk 2** (clerk2@slpa.lk) - Permit management
- **Staff** (staff@slpa.lk) - Basic access

**Default Password:** `password` (⚠️ Change in production!)

**Usage:**
```bash
php artisan db:seed --class=UserSeeder
```

### 3. CompanySeeder
Populates the companies table with 25 common port-related organizations.

**Includes:**
- Sri Lanka Ports Authority
- Container terminal operators
- Shipping lines
- Government agencies
- Service providers

**Usage:**
```bash
php artisan db:seed --class=CompanySeeder
```

### 4. DesignationSeeder
Creates 34 common job designations for port operations.

**Includes:**
- Management positions (General Manager, Port Director, etc.)
- Operational roles (Crane Operator, Pilot, etc.)
- Support staff (Clerk, Technician, etc.)
- Visitors and contractors

**Usage:**
```bash
php artisan db:seed --class=DesignationSeeder
```

### 5. VehicleSeeder
Seeds vehicle types with their codes and rates.

**Includes:**
- 20 vehicle types from motorcycles to heavy equipment
- Each with unique code (e.g., CAR, TRK, PM)
- Pre-configured rates (LKR 200 - 5000)

**Usage:**
```bash
php artisan db:seed --class=VehicleSeeder
```

### 6. ReasonSeeder
Populates entry reasons for permit applications.

**Includes:**
- 36 common entry reasons
- Operational, administrative, and emergency purposes
- Includes "Other" for custom reasons

**Usage:**
```bash
php artisan db:seed --class=ReasonSeeder
```

### 7. PaymentSettingSeeder
Creates default payment configuration.

**Default Settings:**
- Temporary Permit Rate: LKR 100.00
- Monthly Permit Rate: LKR 2,000.00
- Vehicle Permit Rate: LKR 500.00
- Stamp Duty: LKR 50.00
- SSC Rate: 2.5%

**Usage:**
```bash
php artisan db:seed --class=PaymentSettingSeeder
```

## Running Seeders

### All Seeders
```bash
php artisan db:seed
```

### Specific Seeder
```bash
php artisan db:seed --class=UserSeeder
```

### Fresh Migration + Seed
```bash
php artisan migrate:fresh --seed
```

### Production Environment
```bash
# Only seed specific seeders in production
php artisan db:seed --class=CompanySeeder --force
php artisan db:seed --class=DesignationSeeder --force
php artisan db:seed --class=VehicleSeeder --force
php artisan db:seed --class=ReasonSeeder --force
php artisan db:seed --class=PaymentSettingSeeder --force

# DO NOT seed UserSeeder in production with default passwords
```

## Seeding Order

The seeders are executed in the following order:
1. UserSeeder (authentication foundation)
2. PaymentSettingSeeder (system configuration)
3. CompanySeeder (master data)
4. DesignationSeeder (master data)
5. VehicleSeeder (master data)
6. ReasonSeeder (master data)

## Important Notes

### Security
- ⚠️ **Default passwords must be changed in production**
- All users are created with `password` as the default password
- Users are automatically verified (email_verified_at is set)

### Data Integrity
- All seeders can be run multiple times safely (use truncate or migrate:fresh)
- Foreign key constraints are respected
- Unique constraints are handled

### Customization
- Edit individual seeder files to add/modify data
- Rates and amounts can be adjusted in respective seeders
- Add more users, companies, etc., as needed

### Testing
```bash
# Test seeders on a fresh database
php artisan migrate:fresh --seed

# Verify seeded data
php artisan tinker
>>> User::count()
>>> Company::count()
>>> Vehicle::count()
```

## Production Deployment

### Recommended Approach:
1. Run migrations first:
   ```bash
   php artisan migrate --force
   ```

2. Seed only master data (not users):
   ```bash
   php artisan db:seed --class=PaymentSettingSeeder --force
   php artisan db:seed --class=CompanySeeder --force
   php artisan db:seed --class=DesignationSeeder --force
   php artisan db:seed --class=VehicleSeeder --force
   php artisan db:seed --class=ReasonSeeder --force
   ```

3. Create admin users manually through:
   - Registration interface
   - Direct database insert with secure passwords
   - Custom artisan command

### Never in Production:
- Do not use default passwords
- Do not seed test/demo users
- Always use `--force` flag in production

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
php artisan db:seed
```

### Error: Duplicate entry
```bash
# Clear database and reseed
php artisan migrate:fresh --seed
```

### Error: Foreign key constraint
- Ensure seeders run in correct order
- Check DatabaseSeeder.php calls

## Adding New Seeders

1. Create seeder:
   ```bash
   php artisan make:seeder YourSeeder
   ```

2. Implement the `run()` method

3. Add to DatabaseSeeder.php:
   ```php
   $this->call(YourSeeder::class);
   ```

---

**Last Updated:** October 2025
