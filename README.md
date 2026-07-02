# SLPA Port Entry Permit System

A comprehensive web-based permit management system for Sri Lanka Ports Authority (SLPA) to streamline and automate the process of issuing, tracking, and managing entry permits for personnel and vehicles accessing port facilities.

## 📋 Table of Contents

- [About The Project](#about-the-project)
- [Features](#features)
- [Built With](#built-with)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [User Roles](#user-roles)
- [System Modules](#system-modules)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## 🎯 About The Project

The SLPA Port Entry Permit System is designed to digitize and manage the entire permit lifecycle for port entry authorization. The system handles temporary permits, monthly permits, vehicle permits, payment processing, blacklist management, and comprehensive reporting capabilities.

**Project Timeline:** June 2025  
**Repository:** [SLPA-Permit_System](https://github.com/sahanSS98/SLPA-Permit_System)

## ✨ Features

### Core Functionality
- **Multi-Type Permit Management**
  - Temporary (Daily) Permits
  - Monthly Permits
  - Vehicle Permits
  
- **Payment Processing**
  - Automated payment calculation
  - Invoice generation
  - Payment tracking and history
  - Configurable payment settings

- **Blacklist Management**
  - Add/Remove blacklisted individuals or vehicles
  - Automatic validation during permit creation
  - Blacklist history tracking
  - Export blacklist data (PDF/Excel)

- **Permit Operations**
  - Create permits in batches
  - Edit and update permits
  - Cancel active permits
  - Restore cancelled permits
  - Print permits (single/batch)
  - Search and filter permits

- **Administrative Features**
  - User management with role-based access control
  - Master data management (Companies, Designations, Vehicles, Reasons)
  - Payment settings configuration
  - System activity logging

- **Reporting & Analytics**
  - Interactive dashboard with statistics
  - User activity reports
  - Payment/Financial reports
  - Export capabilities (PDF/Excel/CSV)
  - Date range filtering

## 🛠 Built With

- **Framework:** Laravel 12.0
- **PHP:** ^8.2
- **Frontend:** 
  - Tailwind CSS 3.1
  - Alpine.js 3.4
  - Vite 6.2
- **Database:** MySQL
- **Key Packages:**
  - Laravel Breeze (Authentication)
  - DomPDF (PDF Generation)
  - Maatwebsite Excel (Excel Export)

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.0 & NPM
- **MySQL** >= 8.0 (or MariaDB)
- **XAMPP** (recommended for local development) or similar server environment

## 🚀 Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/sahanSS98/SLPA-Permit_System.git
cd port-entry-permit
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

Copy the example environment file and generate application key:

```bash
# Windows (Command Prompt)
copy .env.example .env

# Windows (PowerShell)
Copy-Item .env.example .env

# Linux/Mac
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 5: Configure Database

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 6: Run Migrations

Create the database tables:

```bash
php artisan migrate
```

### Step 7: Seed the Database (Optional)

Populate with sample data:

```bash
php artisan db:seed
```

### Step 8: Build Frontend Assets

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

### Step 9: Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## ⚙️ Configuration

### Mail Configuration

Update the `.env` file with your mail server settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SLPA Permit System"
```

**Note:** For Gmail, you need to use an App Password, not your regular password.

### Queue Configuration

For background jobs and email notifications:

```env
QUEUE_CONNECTION=database
```

Run the queue worker:

```bash
php artisan queue:work
```

### Storage Link

Create symbolic link for public storage:

```bash
php artisan storage:link
```

## 🗄️ Database Setup

### Create Database

```sql
CREATE DATABASE port_entry_permit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Run Migrations

```bash
php artisan migrate
```

### Database Tables

The system includes the following main tables:
- `users` - System users and authentication
- `temporary_permits` - Temporary permit records (TP type)
- `monthly_permits` - Monthly permit records (MP type)
- `vehicle_permits` - Vehicle permit records (VH type)
- `vehicles` - Vehicle master data with rates
- `companies` - Company master data
- `designations` - Designation master data
- `reasons` - Entry reason master data
- `payments` - Payment transactions with invoice IDs
- `payment_settings` - Payment configuration
- `blacklists` - Blacklisted entities with history
- `blacklist_histories` - Blacklist history tracking
- `cancelled_permits` - Cancelled permit records with soft deletes
- `activity_logs` - System activity tracking

## 👥 User Roles

The system supports user roles with different permissions:

### 1. super-admin
- Full system access
- User management
- System configuration
- All administrative functions
- Master data management

### 2. admin
- Permit management
- Master data management
- Blacklist management
- Reports and analytics
- Payment settings
- Cancelled permits management

### 3. clerk
- Create and manage permits
- Search permits
- Print permits
- View reports
- Basic permit operations

### 4. staff
- Create permits
- View permits
- Print permits
- Limited administrative access

## 📚 System Modules

### 1. Dashboard
- Real-time statistics and charts
- Monthly permit trends
- Recent activities
- Quick access to key functions

### 2. Permit Management
- **Temporary Permits:** Single-day entry permits
- **Monthly Permits:** Recurring monthly access
- **Vehicle Permits:** Vehicle entry authorization
- Batch creation with session management
- Availability checking
- Print functionality

### 3. Payment Module
- Automatic calculation based on permit type
- Invoice generation with submission ID
- Payment history tracking
- Configurable rates

### 4. Blacklist Management
- Add/Edit/Delete blacklist entries
- Automatic validation during permit creation
- History tracking
- Export capabilities

### 5. Master Data Management
- Companies
- Designations
- Vehicle Types
- Entry Reasons

### 6. User Management
- Create/Edit/Delete users
- Role assignment
- Access control

### 7. Reports
- **User Activity Reports:** Track user actions with date filters
- **Payment Reports:** Financial summaries and transaction details
- Export options: PDF, Excel, CSV

### 8. Cancelled Permits
- View cancelled permits
- Restore cancelled permits
- Activate/Deactivate permits
- Trash management with soft deletes

## 🖥️ Usage

### First Time Login

1. Access the application at `http://localhost:8000`
2. Use seeded credentials or register a new account
3. Default seeded credentials:
   - **Super Admin:** `superadmin@slpa.lk` / `password`
   - **Admin:** `admin@slpa.lk` / `password`
   - **Clerk:** `clerk1@slpa.lk` / `password`
   - **Staff:** `staff@slpa.lk` / `password`

**Important:** Change these passwords immediately in production!

### Creating Permits

1. Navigate to the appropriate permit type (Temporary/Monthly/Vehicle)
2. Fill in the required information
3. Add entries to session (for batch creation)
4. Review summary
5. Submit for processing
6. Generate payment invoice
7. Print permits

### Managing Blacklist

1. Go to Admin → Blacklist Management
2. Click "Add to Blacklist"
3. Enter NIC, name, and reason
4. Submit to blacklist the entity
5. System will automatically prevent permit creation for blacklisted entries

### Generating Reports

1. Navigate to Reports section
2. Select report type (User Activity or Payment)
3. Set date range filters
4. View results
5. Export as PDF, Excel, or CSV

## 🔧 Troubleshooting

### Common Issues

**Issue:** Migration errors
```bash
# Clear config cache and retry
php artisan config:clear
php artisan migrate:fresh
```

**Issue:** Permission denied errors
```bash
# Windows (Run as Administrator)
# Set proper permissions for storage and cache folders manually

# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Issue:** Assets not loading
```bash
# Rebuild assets
npm run build
php artisan optimize:clear
```

**Issue:** Database connection error
- Verify MySQL service is running
- Check `.env` database credentials
- Ensure database exists
- Test connection: `php artisan migrate:status`

**Issue:** Queue jobs not processing
```bash
# Start queue worker
php artisan queue:work

# Or restart queue
php artisan queue:restart
```

**Issue:** Vite connection error during development
```bash
# Make sure npm run dev is running in a separate terminal
npm run dev
```

**Issue:** Class not found errors
```bash
# Regenerate autoload files
composer dump-autoload
```

## 🔒 Security Notes

### Production Deployment

Before deploying to production:

1. **Set proper environment:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   LOG_LEVEL=error
   ```

2. **Generate new application key:**
   ```bash
   php artisan key:generate
   ```

3. **Remove sensitive credentials from version control**
   - Never commit `.env` file
   - Use environment variables or secret management
   - Rotate all passwords and API keys

4. **Set up proper file permissions:**
   ```bash
   # Linux/Mac
   chmod -R 755 storage bootstrap/cache
   chmod -R 644 .env
   ```

5. **Enable HTTPS and configure trusted proxies**

6. **Set up regular database backups**

7. **Configure proper session and cache drivers** (Redis recommended)

8. **Enable CSRF protection** (already enabled in Laravel)

9. **Set up rate limiting** for API endpoints

10. **Regular security updates:**
    ```bash
    composer update
    npm update
    ```

## 📈 Performance Optimization

### Production Optimizations

```bash
# Optimize configuration loading
php artisan config:cache

# Optimize route loading
php artisan route:cache

# Optimize view loading
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build optimized assets
npm run build
```

### Clear All Caches

```bash
php artisan optimize:clear
```

## 🔄 Maintenance

### Regular Tasks

1. **Clear old logs:**
   ```bash
   php artisan log:clear
   ```

2. **Database backup:**
   ```bash
   # Example backup command
   mysqldump -u root -p port_entry_permit > backup_$(date +%Y%m%d).sql
   ```

3. **Check failed jobs:**
   ```bash
   php artisan queue:failed
   ```

4. **Clear expired sessions:**
   ```bash
   php artisan session:gc
   ```

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Developers & Contributors

This project was originally developed by **sahanSS98** and subsequently enhanced and maintained by **Rashini Savindya**.

### 🌟 Enhancements & Upgrades by Rashini Savindya (July 2026)
*   **Galle Port Gap Analysis & Roadmap**: Authored [GALLE_PORT_REPORT.md](file:///d:/New-GllePermitSystem/port-entry-permit/port-entry-permit/GALLE_PORT_REPORT.md) outlining the security and permit lifecycle gaps for Galle Port.
*   **Database Schema Extensions**: Implemented migration `2026_06_28_143500_add_phase_a_columns_to_permits_tables.php` to add support for:
    *   Applicant profile photograph paths (`photo_path`)
    *   Scanned identity and validation documents (`scanned_nic`, `scanned_police_report`)
    *   Yacht crew and international tourist fields (`yacht_name`, `yacht_agent`, `passport_country`, `visa_expiry`)
*   **UI/UX Modernization**:
    *   Re-designed the main Dashboard and Security Verification Dashboard with high-fidelity controls.
    *   Redesigned Authentication interfaces (Login and Forgot Password screens).
    *   Upgraded application layouts (authenticated and guest views) with a new Galle Lighthouse sidebar and custom branding.
    *   Reconstructed Permit Edit and Batch Edit interfaces.
*   **Printing & Invoicing Refinements**: Polished payment receipt previews, batch permit submittal reviews, and invoice layouts.
*   **Controller Improvements**: Refined the data handling pipelines across controllers to support Phase A fields and dashboard enhancements.

**Repository:** [github.com/jprsavindya1/Galle-Port-Entry-Permit-System](https://github.com/jprsavindya1/Galle-Port-Entry-Permit-System)  
**Maintainer:** Rashini Savindya

## 📞 Support

For technical support or inquiries:
- Create an issue in the GitHub repository
- Contact the system administrator
- Refer to Laravel documentation: https://laravel.com/docs

## 🙏 Acknowledgments

- Laravel Framework Team
- Tailwind CSS Team
- All contributors to open-source packages used in this project

---

**Version:** 1.1 (Enhanced)  
**Last Updated:** July 2026  
**Status:** Feature Enhanced & Stable Ready
