# Quick Start Guide - SLPA Port Entry Permit System

## 🚀 Get Started in 5 Minutes

### Prerequisites Check
- ✅ PHP 8.2+ installed
- ✅ MySQL running
- ✅ Composer installed
- ✅ Node.js & NPM installed

### Installation Steps

```bash
# 1. Clone the repository
git clone https://github.com/sahanSS98/SLPA-Permit_System.git
cd port-entry-permit

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
Copy-Item .env.example .env
php artisan key:generate

# 4. Update .env database settings
# Edit .env file:
DB_DATABASE=port_entry_permit
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Create database
# In MySQL: CREATE DATABASE port_entry_permit;

# 6. Run migrations and seeds
php artisan migrate:fresh --seed

# 7. Build assets
npm run build

# 8. Start server
php artisan serve
```

### First Login

Visit: `http://localhost:8000`

**Login Credentials:**
- Super Admin: `superadmin@slpa.lk` / `password`
- Admin: `admin@slpa.lk` / `password`
- Clerk: `clerk1@slpa.lk` / `password`

## 📋 Quick Feature Access

### Creating Permits
1. **Temporary Permit**: Menu → Temporary Permit
2. **Monthly Permit**: Menu → Monthly Permit
3. **Vehicle Permit**: Menu → Vehicle Permit

### Admin Functions (Admin/Super Admin only)
- **Master Data**: Menu → Master Data Management
- **Users**: Menu → User Management
- **Blacklist**: Menu → Blacklist Management
- **Payment Settings**: Menu → Payment Settings
- **Cancelled Permits**: Menu → Cancelled Permits

### Reports
- **User Activity**: Menu → Reports → User Activity
- **Payment Reports**: Menu → Reports → Payment Reports

### Search & Print
- **Search Permits**: Menu → Search Permits
- **Print Permit**: From permit details page

## 🔧 Common Commands

```bash
# Start development server
php artisan serve

# Run with hot reload (separate terminal)
npm run dev

# Clear all caches
php artisan optimize:clear

# Run queue worker (for emails/jobs)
php artisan queue:work

# Fresh database
php artisan migrate:fresh --seed

# Check database status
php artisan migrate:status

# View logs
Get-Content storage/logs/laravel.log -Tail 50
```

## 🎯 Quick Testing Checklist

- [ ] Login with admin credentials
- [ ] View dashboard statistics
- [ ] Create a temporary permit
- [ ] Generate payment invoice
- [ ] Print permit
- [ ] Search for permit
- [ ] Add entry to blacklist
- [ ] View reports
- [ ] Manage master data
- [ ] Create new user

## ⚠️ Before Production

- [ ] Change all default passwords
- [ ] Set `APP_ENV=production` in .env
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Configure real mail settings
- [ ] Set up regular database backups
- [ ] Review and update payment rates
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Test all features thoroughly

## 📞 Need Help?

- 📖 Full Documentation: `README.md`
- 🌱 Seeding Guide: `SEEDING_SUMMARY.md`
- 🗄️ Database Seeders: `database/seeders/README.md`
- 🐛 Issues: [GitHub Issues](https://github.com/sahanSS98/SLPA-Permit_System/issues)

---

**Version:** 1.0 | **Last Updated:** October 2025
