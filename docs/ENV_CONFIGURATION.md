# Environment Configuration Guide

## 📋 Overview

This guide explains how to properly configure the `.env` file for different environments (Development, Staging, Production) in the SLPA Port Entry Permit System.

## 🔐 Security First

**CRITICAL SECURITY RULES:**
1. ⚠️ **NEVER** commit `.env` file to Git
2. ⚠️ **ALWAYS** use strong, unique passwords in production
3. ⚠️ **ALWAYS** set `APP_DEBUG=false` in production
4. ⚠️ **ALWAYS** use HTTPS in production (`APP_URL=https://...`)
5. ⚠️ **ALWAYS** generate a new `APP_KEY` for each environment

---

## 🚀 Quick Setup

### Step 1: Create .env File
```bash
# Windows PowerShell
Copy-Item .env.example .env

# Windows Command Prompt
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

### Step 2: Generate Application Key
```bash
php artisan key:generate
```

### Step 3: Configure Database
Edit `.env` and update database credentials:
```env
DB_DATABASE=port_entry_permit
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Create Database
```sql
CREATE DATABASE port_entry_permit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 5: Run Migrations
```bash
php artisan migrate
```

---

## 🔧 Environment-Specific Configurations

### 1. Development Environment

**Best for:** Local development on your machine

```env
# Application
APP_NAME="SLPA Permit System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit_dev
DB_USERNAME=root
DB_PASSWORD=

# Mail (log to file for testing)
MAIL_MAILER=log

# Cache & Session (use file for simplicity)
CACHE_STORE=file
SESSION_DRIVER=file

# Queue (process immediately for testing)
QUEUE_CONNECTION=sync

# Logging (verbose for debugging)
LOG_LEVEL=debug
LOG_STACK=single
```

**Features:**
- ✅ Immediate error display
- ✅ Detailed error messages
- ✅ Email saved to logs
- ✅ No queue workers needed
- ✅ Simple setup

---

### 2. Staging Environment

**Best for:** Testing before production deployment

```env
# Application
APP_NAME="SLPA Permit System [STAGING]"
APP_ENV=staging
APP_DEBUG=true
APP_URL=https://staging.permit.slpa.lk

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit_staging
DB_USERNAME=staging_user
DB_PASSWORD=staging_secure_password_here

# Mail (use real SMTP for testing)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=staging@slpa.lk
MAIL_PASSWORD=app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=staging@slpa.lk
MAIL_FROM_NAME="SLPA Permit System [STAGING]"

# Cache & Session (use database or Redis)
CACHE_STORE=database
SESSION_DRIVER=database

# Queue (use database with workers)
QUEUE_CONNECTION=database

# Logging (less verbose)
LOG_LEVEL=info
LOG_STACK=daily
```

**Features:**
- ✅ Production-like environment
- ✅ Real email sending for testing
- ✅ Error display for debugging
- ✅ Performance closer to production
- ✅ Safe testing environment

---

### 3. Production Environment

**Best for:** Live production deployment

```env
# Application
APP_NAME="SLPA Permit System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://permit.slpa.lk

# Database (use separate user with minimal privileges)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit_prod
DB_USERNAME=prod_user
DB_PASSWORD=very_secure_random_password_here

# Mail (production email account)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@slpa.lk
MAIL_PASSWORD=secure_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@slpa.lk
MAIL_FROM_NAME="SLPA Permit System"

# Cache & Session (use Redis for performance)
CACHE_STORE=redis
SESSION_DRIVER=redis
CACHE_PREFIX=slpa_permit

# Queue (use Redis with workers)
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Logging (errors only to minimize log size)
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error
```

**Features:**
- ✅ Maximum security
- ✅ No error display to users
- ✅ Optimized performance
- ✅ Production-grade email
- ✅ Minimal logging

---

## 📧 Email Configuration

### Gmail Setup (Most Common)

**Step 1: Enable 2-Step Verification**
1. Go to https://myaccount.google.com/security
2. Enable 2-Step Verification

**Step 2: Generate App Password**
1. Go to https://myaccount.google.com/apppasswords
2. Select "Mail" and "Other (Custom name)"
3. Enter "SLPA Permit System"
4. Copy the generated 16-character password

**Step 3: Configure .env**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SLPA Permit System"
```

**Test Email:**
```bash
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

---

### Other SMTP Providers

#### Outlook/Office 365
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

#### Custom SMTP Server
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

---

## 🗄️ Database Configuration

### MySQL Configuration

**Development:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit_dev
DB_USERNAME=root
DB_PASSWORD=
```

**Production (Secure Setup):**
```sql
-- Create production database
CREATE DATABASE port_entry_permit_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated user with minimal privileges
CREATE USER 'slpa_prod'@'localhost' IDENTIFIED BY 'SECURE_RANDOM_PASSWORD';

-- Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, REFERENCES, LOCK TABLES 
ON port_entry_permit_prod.* TO 'slpa_prod'@'localhost';

FLUSH PRIVILEGES;
```

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit_prod
DB_USERNAME=slpa_prod
DB_PASSWORD=SECURE_RANDOM_PASSWORD
```

### Remote Database
```env
DB_HOST=db.example.com
DB_PORT=3306
DB_DATABASE=port_entry_permit
DB_USERNAME=remote_user
DB_PASSWORD=remote_password
```

---

## 💾 Cache & Session Configuration

### Development (Simple File-Based)
```env
CACHE_STORE=file
SESSION_DRIVER=file
```

### Production (Database-Based)
```env
CACHE_STORE=database
SESSION_DRIVER=database
CACHE_PREFIX=slpa_permit
```

### Production (Redis - Recommended)

**Install Redis:**
```bash
# Ubuntu/Debian
sudo apt install redis-server

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

**Configure .env:**
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
```

**Benefits:**
- ⚡ Faster performance
- 📊 Better scalability
- 🔄 Shared across multiple servers

---

## 🔄 Queue Configuration

### Development (Sync - No Workers)
```env
QUEUE_CONNECTION=sync
```
Jobs run immediately, no worker needed.

### Production (Database)
```env
QUEUE_CONNECTION=database
```

**Start Worker:**
```bash
php artisan queue:work --tries=3
```

**Use Supervisor (Recommended):**
```ini
[program:slpa-permit-worker]
command=php /var/www/slpa-permit/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
```

### Production (Redis - Recommended)
```env
QUEUE_CONNECTION=redis
```

**Benefits:**
- ⚡ Faster job processing
- 📊 Better job prioritization
- 🔄 Job status tracking

---

## 📊 Logging Configuration

### Development (Verbose Logging)
```env
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=debug
```

### Production (Minimal Logging)
```env
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error
```

**Log Rotation:**
Laravel automatically rotates daily logs, keeping 14 days by default.

**Manual Log Clearing:**
```bash
# Clear all logs
rm storage/logs/*.log

# Keep only last 7 days
find storage/logs -name "*.log" -mtime +7 -delete
```

---

## 🔒 Security Configuration

### Password Hashing
```env
# Higher = more secure but slower
# 12 is good balance (recommended: 10-14)
BCRYPT_ROUNDS=12
```

### Session Security
```env
SESSION_LIFETIME=120        # Minutes (default: 120 = 2 hours)
SESSION_ENCRYPT=false       # Set to true if extra security needed
SESSION_EXPIRE_ON_CLOSE=false  # true = expires when browser closes
SESSION_DOMAIN=.slpa.lk     # For subdomains (production)
SESSION_SECURE_COOKIE=true  # Require HTTPS (production only)
SESSION_SAME_SITE=lax       # CSRF protection (lax or strict)
```

**Session Driver Options:**
```env
# Development - Simple file storage
SESSION_DRIVER=file

# Production - Database storage (recommended)
SESSION_DRIVER=database

# Production - Redis storage (best performance)
SESSION_DRIVER=redis
```

**Session Lifetime Recommendations:**
- Development: `SESSION_LIFETIME=480` (8 hours)
- Production: `SESSION_LIFETIME=120` (2 hours)
- High Security: `SESSION_LIFETIME=30` (30 minutes)

**Database Session Setup:**
```bash
# Create sessions table
php artisan session:table
php artisan migrate
```

---

## ✅ Validation Checklist

### Before Starting Development
- [ ] `.env` file created from `.env.example`
- [ ] `APP_KEY` generated
- [ ] Database created and credentials configured
- [ ] Migrations run successfully
- [ ] Database seeded (optional)
- [ ] Application loads without errors

### Before Staging Deployment
- [ ] `APP_ENV=staging`
- [ ] Strong database password set
- [ ] Email configuration tested
- [ ] HTTPS enabled
- [ ] All features tested

### Before Production Deployment
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` uses HTTPS
- [ ] New `APP_KEY` generated
- [ ] Strong, unique passwords for all services
- [ ] Database user has minimal privileges
- [ ] Email sending tested
- [ ] Cache optimized (Redis recommended)
- [ ] Queue workers configured
- [ ] Cron jobs configured
- [ ] Backups configured
- [ ] Monitoring configured
- [ ] All default passwords changed
- [ ] Security headers configured
- [ ] File permissions set correctly

---

## 🧪 Testing Configuration

### Test Email
```bash
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('admin@slpa.lk')->subject('Test'); });
```

### Test Database
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::table('users')->count();
```

### Test Cache
```bash
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');
```

### Test Queue
```bash
# In one terminal
php artisan queue:work

# In another terminal
php artisan tinker
>>> dispatch(new App\Jobs\TestJob());
```

---

## 🔧 Troubleshooting

### Issue: "APP_KEY not set"
```bash
php artisan key:generate
```

### Issue: Database connection failed
- Verify MySQL is running
- Check credentials in `.env`
- Ensure database exists
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

### Issue: Email not sending
- Check SMTP credentials
- For Gmail, use App Password (not regular password)
- Check firewall allows port 587/465
- Test with: `php artisan tinker` then send test email

### Issue: Permission denied
```bash
# Windows (Run as Administrator)
# Set folder permissions manually

# Linux
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Issue: Cache not working
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Rebuild cache
php artisan config:cache
```

---

## 📚 Additional Resources

- [Laravel Configuration Documentation](https://laravel.com/docs/configuration)
- [Laravel Environment Configuration](https://laravel.com/docs/configuration#environment-configuration)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Laravel Mail Documentation](https://laravel.com/docs/mail)

---

**Last Updated:** October 2025  
**Version:** 1.0
