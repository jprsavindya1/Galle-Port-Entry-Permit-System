# Database Seeding Summary

## ✅ Completed Seeders

All database seeders have been successfully created for the SLPA Port Entry Permit System.

## 📊 Seeded Data Overview

### 1. Users (5 accounts)
| Role | Email | Password | Access Level |
|------|-------|----------|------------|
| super-admin | superadmin@slpa.lk | password | Full system access |
| admin | admin@slpa.lk | password | Administrative functions |
| clerk | clerk1@slpa.lk | password | Permit management |
| clerk | clerk2@slpa.lk | password | Permit management |
| staff | staff@slpa.lk | password | Basic operations |

**Note:** All accounts have `email_verified_at` set and use bcrypt hashed passwords.

### 2. Companies (25 organizations)
- Sri Lanka Ports Authority
- Container terminal operators (CICT, SAGT, JCT)
- Shipping lines (Maersk, MSC, CMA CGM, etc.)
- Government agencies (Customs, Navy, Police, etc.)
- Service providers

### 3. Designations (34 roles)
- Management (General Manager, Port Director, etc.)
- Operations (Crane Operator, Pilot, Harbour Master, etc.)
- Technical (Engineer, Technician, Electrician, etc.)
- Support (Clerk, Security Officer, Driver, etc.)
- External (Contractor, Consultant, Visitor, etc.)

### 4. Vehicles (20 types with rates)
| Vehicle Type | Code | Rate (LKR) |
|--------------|------|------------|
| Motorcycle | MC | 200.00 |
| Three Wheeler | TW | 300.00 |
| Car | CAR | 500.00 |
| Van | VAN | 750.00 |
| Mini Bus | MB | 800.00 |
| Bus | BUS | 1,000.00 |
| Lorry (Small) | LS | 1,000.00 |
| Lorry (Medium) | LM | 1,500.00 |
| Lorry (Large) | LL | 2,000.00 |
| Forklift | FL | 2,000.00 |
| Truck | TRK | 2,500.00 |
| Prime Mover | PM | 3,000.00 |
| Trailer | TRL | 3,500.00 |
| Tanker | TNK | 3,500.00 |
| Container Trailer | CT | 4,000.00 |
| Heavy Equipment | HE | 4,000.00 |
| Crane | CRN | 5,000.00 |
| Bowser | BWS | 2,800.00 |
| Low Loader | LL | 4,500.00 |
| Other Vehicle | OTH | 1,000.00 |

### 5. Entry Reasons (36 purposes)
- Official operations (Official Duty, Meeting, Inspection)
- Work activities (Maintenance, Repair, Installation, Construction)
- Port operations (Cargo, Container Handling, Vessel Operations)
- Support services (Delivery, Fuel Supply, Waste Collection)
- Emergency services (Medical, Fire Safety, Security)
- Administrative (Training, Audit, Documentation)
- Business (Contractor Work, Consultancy, Visitor)

### 6. Payment Settings (Default configuration)
| Setting | Value |
|---------|-------|
| Temporary Permit Rate | LKR 100.00 |
| Monthly Permit Rate | LKR 2,000.00 |
| Vehicle Permit Rate | LKR 500.00 |
| Stamp Duty | LKR 50.00 |
| SSC Rate | 2.5% |

## 🚀 How to Use

### Run All Seeders
```bash
php artisan migrate:fresh --seed
```

### Run Specific Seeder
```bash
php artisan db:seed --class=CompanySeeder
```

### View Seeded Data
```bash
php artisan tinker
>>> User::all()
>>> Company::count()
>>> Vehicle::pluck('name', 'rate')
```

## ⚠️ Important Security Notes

### Before Production Deployment:

1. **NEVER use default passwords in production**
   ```bash
   # In production, seed only master data:
   php artisan db:seed --class=CompanySeeder --force
   php artisan db:seed --class=DesignationSeeder --force
   php artisan db:seed --class=VehicleSeeder --force
   php artisan db:seed --class=ReasonSeeder --force
   php artisan db:seed --class=PaymentSettingSeeder --force
   ```

2. **Create admin users manually** with secure passwords

3. **Update payment rates** according to actual SLPA rates

4. **Review and customize** companies, designations, and reasons

## 📝 Customization Guide

### Adding More Companies
Edit `database/seeders/CompanySeeder.php`:
```php
$companies = [
    ['name' => 'Your Company Name'],
    // ... add more
];
```

### Modifying Vehicle Rates
Edit `database/seeders/VehicleSeeder.php`:
```php
['name' => 'Car', 'code' => 'CAR', 'rate' => 500.00],
```

### Adding Custom Reasons
Edit `database/seeders/ReasonSeeder.php`:
```php
$reasons = [
    ['name' => 'Your Custom Reason'],
    // ... add more
];
```

### Changing Payment Settings
Edit `database/seeders/PaymentSettingSeeder.php`:
```php
PaymentSetting::create([
    'temporary_permit_rate' => 150.00, // Update as needed
    'monthly_permit_rate' => 2500.00,
    // ...
]);
```

## 🧪 Testing the Seeds

### Test Command
```bash
# Fresh database with seeds
php artisan migrate:fresh --seed

# Verify data
php artisan tinker
>>> User::count()        // Should return 5
>>> Company::count()     // Should return 25
>>> Designation::count() // Should return 34
>>> Vehicle::count()     // Should return 20
>>> Reason::count()      // Should return 36
>>> PaymentSetting::first() // Should return settings
```

### Expected Output
```
🌱 Starting database seeding...

👤 Seeding users...
Users seeded successfully!

💰 Seeding payment settings...
Payment settings seeded successfully!

🏢 Seeding companies...
Companies seeded successfully!

👔 Seeding designations...
Designations seeded successfully!

🚗 Seeding vehicles...
Vehicles seeded successfully!

📋 Seeding entry reasons...
Reasons seeded successfully!

✅ Database seeding completed successfully!

=================================================
            DEFAULT LOGIN CREDENTIALS            
=================================================
Super Admin:
  Email: superadmin@slpa.lk
  Password: password

Admin:
  Email: admin@slpa.lk
  Password: password
...
=================================================
⚠️  CHANGE THESE PASSWORDS IN PRODUCTION!  ⚠️
=================================================
```

## 📁 Created Files

1. `database/seeders/DatabaseSeeder.php` - Main orchestrator
2. `database/seeders/UserSeeder.php` - User accounts
3. `database/seeders/CompanySeeder.php` - Company master data
4. `database/seeders/DesignationSeeder.php` - Designation master data
5. `database/seeders/VehicleSeeder.php` - Vehicle types and rates
6. `database/seeders/ReasonSeeder.php` - Entry reasons
7. `database/seeders/PaymentSettingSeeder.php` - Payment configuration
8. `database/seeders/README.md` - Detailed documentation

## ✅ Benefits

1. **Quick Setup** - Get started immediately with pre-populated data
2. **Consistent Testing** - Same data across all environments
3. **Production Ready** - Real-world data examples
4. **Easy Customization** - Simple to modify and extend
5. **Well Documented** - Clear instructions and examples

## 🔄 Next Steps

After seeding:
1. ✅ Test login with provided credentials
2. ✅ Verify master data in admin panel
3. ✅ Create test permits to validate system
4. ✅ Adjust rates if needed
5. ✅ Add any additional companies/vehicles/reasons specific to your needs

---

**Status:** ✅ Complete and Ready to Use  
**Last Updated:** October 2025
