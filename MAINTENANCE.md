# System Maintenance Guide - SLPA Port Entry Permit System

## 📋 Table of Contents
- [Daily Maintenance](#daily-maintenance)
- [Weekly Maintenance](#weekly-maintenance)
- [Monthly Maintenance](#monthly-maintenance)
- [Quarterly Maintenance](#quarterly-maintenance)
- [Common Maintenance Tasks](#common-maintenance-tasks)
- [Troubleshooting Guide](#troubleshooting-guide)
- [Emergency Procedures](#emergency-procedures)

---

## 📅 Daily Maintenance

### 1. System Health Check (5 minutes)
```bash
# Check if web server is running
sudo systemctl status nginx  # or apache2

# Check if database is running
sudo systemctl status mysql

# Check if Redis is running
sudo systemctl status redis-server

# Check if queue workers are running
sudo supervisorctl status

# Check disk space
df -h

# Check memory usage
free -h
```

### 2. Monitor Application Logs
```bash
# Check for errors in Laravel logs
tail -n 100 /var/www/slpa-permit/storage/logs/laravel.log

# Check Nginx error logs
sudo tail -n 100 /var/log/nginx/error.log

# Check PHP-FPM logs
sudo tail -n 100 /var/log/php8.2-fpm.log
```

### 3. Queue Status
```bash
# Check failed jobs
cd /var/www/slpa-permit
php artisan queue:failed

# Retry failed jobs if needed
php artisan queue:retry all
```

### 4. Backup Verification
```bash
# Check if daily backups ran
ls -lh /var/backups/slpa-permit/
```

---

## 📅 Weekly Maintenance

### 1. Database Maintenance (15 minutes)
```sql
-- Login to MySQL
mysql -u slpa_prod -p

-- Use database
USE port_entry_permit_prod;

-- Check table sizes
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'port_entry_permit_prod'
ORDER BY (data_length + index_length) DESC;

-- Optimize tables
OPTIMIZE TABLE permits;
OPTIMIZE TABLE monthly_permits;
OPTIMIZE TABLE cancelled_permits;
OPTIMIZE TABLE payments;
OPTIMIZE TABLE activity_logs;

EXIT;
```

### 2. Clean Old Logs
```bash
cd /var/www/slpa-permit

# Clear logs older than 30 days
find storage/logs -name "*.log" -mtime +30 -delete

# Clear old queue jobs
php artisan queue:flush
```

### 3. Review User Activity
```bash
# Check recent user logins and activities
php artisan tinker
>>> \App\Models\ActivityLog::latest()->take(50)->get();
>>> \App\Models\User::where('created_at', '>', now()->subDays(7))->count();
```

### 4. Security Updates
```bash
# Update system packages
sudo apt update
sudo apt list --upgradable

# Apply security updates only
sudo apt upgrade -y
```

---

## 📅 Monthly Maintenance

### 1. Performance Review (30 minutes)

#### Check Slow Queries
```sql
-- Enable slow query log (if not already enabled)
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;

-- Review slow queries
SELECT * FROM mysql.slow_log LIMIT 100;
```

#### Check Database Indexes
```bash
cd /var/www/slpa-permit
php artisan tinker

# Check for missing indexes on frequently queried columns
>>> DB::select('SHOW INDEX FROM permits');
```

### 2. Storage Cleanup
```bash
# Check storage usage
du -sh /var/www/slpa-permit/storage/*

# Clean old temporary files
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear

# Remove old compiled views
rm -rf storage/framework/views/*
php artisan view:cache

# Clean old session files
find storage/framework/sessions -type f -mtime +7 -delete
```

### 3. Review and Archive Old Data
```sql
-- Archive cancelled permits older than 1 year
-- Create archive table if needed
CREATE TABLE cancelled_permits_archive LIKE cancelled_permits;

-- Move old records
INSERT INTO cancelled_permits_archive
SELECT * FROM cancelled_permits
WHERE deleted_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Verify count
SELECT COUNT(*) FROM cancelled_permits_archive;

-- Delete from main table (after verification)
-- DELETE FROM cancelled_permits WHERE deleted_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

### 4. Update Dependencies
```bash
cd /var/www/slpa-permit

# Check for outdated packages
composer outdated

# Update packages (test in staging first!)
# composer update

# Check for NPM updates
npm outdated
```

### 5. SSL Certificate Check
```bash
# Check certificate expiry
sudo certbot certificates

# Test renewal
sudo certbot renew --dry-run
```

### 6. Backup Testing
```bash
# Test database restore on a test server
# 1. Copy latest backup
cp /var/backups/slpa-permit/db_backup_latest.sql.gz /tmp/

# 2. Create test database
mysql -u root -p -e "CREATE DATABASE test_restore;"

# 3. Restore
gunzip < /tmp/db_backup_latest.sql.gz | mysql -u root -p test_restore

# 4. Verify data
mysql -u root -p test_restore -e "SELECT COUNT(*) FROM permits;"

# 5. Clean up
mysql -u root -p -e "DROP DATABASE test_restore;"
```

---

## 📅 Quarterly Maintenance

### 1. Security Audit (1-2 hours)

#### Review User Accounts
```bash
php artisan tinker
>>> User::where('last_login_at', '<', now()->subMonths(3))->get();
```

#### Check File Permissions
```bash
# Verify permissions are correct
find /var/www/slpa-permit -type f ! -perm 644
find /var/www/slpa-permit -type d ! -perm 755
```

#### Review Access Logs
```bash
# Check for suspicious activity
sudo grep "POST" /var/log/nginx/access.log | grep -E "(admin|login)" | tail -100
```

### 2. Performance Testing
```bash
# Install Apache Bench (if not installed)
sudo apt install apache2-utils

# Test homepage
ab -n 1000 -c 10 https://permit.slpa.lk/

# Test login page
ab -n 100 -c 10 https://permit.slpa.lk/login
```

### 3. Database Optimization
```sql
-- Analyze tables
ANALYZE TABLE permits;
ANALYZE TABLE monthly_permits;
ANALYZE TABLE payments;

-- Check for fragmentation
SELECT 
    table_name,
    ROUND(data_free / 1024 / 1024, 2) AS data_free_mb
FROM information_schema.tables
WHERE table_schema = 'port_entry_permit_prod'
AND data_free > 0;

-- Rebuild indexes if needed
ALTER TABLE permits ENGINE=InnoDB;
```

### 4. Comprehensive System Report
```bash
# System information
uname -a
cat /etc/os-release

# PHP version
php -v

# MySQL version
mysql --version

# Disk usage
df -h

# Memory usage
free -h

# CPU usage
top -bn1 | head -20

# Network connections
netstat -tuln
```

---

## 🔧 Common Maintenance Tasks

### Clear Application Cache
```bash
cd /var/www/slpa-permit
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuild Application Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Restart Services
```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart Nginx
sudo systemctl restart nginx

# Restart MySQL
sudo systemctl restart mysql

# Restart Queue Workers
sudo supervisorctl restart slpa-permit-worker:*

# Restart Redis
sudo systemctl restart redis-server
```

### Update Application Code
```bash
cd /var/www/slpa-permit

# Pull latest code
sudo -u www-data git pull origin main

# Install dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Rebuild assets
sudo -u www-data npm run build

# Clear and rebuild cache
php artisan optimize:clear
php artisan optimize

# Restart services
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart slpa-permit-worker:*
```

### Manually Run Database Backup
```bash
# Create backup directory if not exists
sudo mkdir -p /var/backups/slpa-permit

# Backup database
sudo mysqldump -u slpa_prod -p port_entry_permit_prod | gzip > /var/backups/slpa-permit/db_backup_$(date +%Y%m%d_%H%M%S).sql.gz

# Backup files
sudo tar -czf /var/backups/slpa-permit/files_backup_$(date +%Y%m%d_%H%M%S).tar.gz \
    /var/www/slpa-permit/storage/app \
    /var/www/slpa-permit/.env
```

### Monitor System Resources
```bash
# Real-time monitoring
htop

# Disk I/O
iostat -x 1

# Network traffic
iftop

# Process list
ps aux | grep php

# MySQL processes
mysqladmin -u root -p processlist
```

---

## 🔍 Troubleshooting Guide

### Issue: Application is Slow

**Diagnosis:**
```bash
# Check server load
uptime

# Check database connections
mysql -u root -p -e "SHOW PROCESSLIST;"

# Check slow queries
tail -f /var/log/mysql/mysql-slow.log

# Check PHP-FPM status
sudo systemctl status php8.2-fpm
```

**Solutions:**
1. Clear application cache
2. Optimize database tables
3. Check for slow queries
4. Increase PHP-FPM workers
5. Enable Redis cache

---

### Issue: Queue Not Processing

**Diagnosis:**
```bash
# Check supervisor status
sudo supervisorctl status

# Check failed jobs
php artisan queue:failed

# Check logs
tail -f /var/www/slpa-permit/storage/logs/worker.log
```

**Solutions:**
```bash
# Restart queue workers
sudo supervisorctl restart slpa-permit-worker:*

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

### Issue: High Database Load

**Diagnosis:**
```sql
-- Check running queries
SHOW PROCESSLIST;

-- Check table locks
SHOW OPEN TABLES WHERE In_use > 0;

-- Check slow queries
SELECT * FROM mysql.slow_log ORDER BY start_time DESC LIMIT 20;
```

**Solutions:**
1. Kill long-running queries
2. Add missing indexes
3. Optimize queries
4. Increase MySQL resources

---

### Issue: Disk Space Full

**Diagnosis:**
```bash
# Check disk usage
df -h

# Find large files
sudo du -sh /var/www/slpa-permit/*
sudo du -sh /var/log/*
```

**Solutions:**
```bash
# Clear old logs
sudo find /var/log -name "*.log" -mtime +30 -delete

# Clear Laravel logs
sudo find /var/www/slpa-permit/storage/logs -name "*.log" -mtime +14 -delete

# Clear old backups
sudo find /var/backups/slpa-permit -mtime +60 -delete

# Clear APT cache
sudo apt clean
```

---

### Issue: Memory Leak

**Diagnosis:**
```bash
# Check memory usage
free -h

# Check processes consuming memory
ps aux --sort=-%mem | head -n 10
```

**Solutions:**
```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart MySQL
sudo systemctl restart mysql

# Restart queue workers
sudo supervisorctl restart slpa-permit-worker:*
```

---

## 🚨 Emergency Procedures

### Emergency: Site Down

1. **Check if server is reachable:**
   ```bash
   ping your-server-ip
   ```

2. **Check web server:**
   ```bash
   sudo systemctl status nginx
   sudo systemctl restart nginx
   ```

3. **Check database:**
   ```bash
   sudo systemctl status mysql
   sudo systemctl restart mysql
   ```

4. **Check application logs:**
   ```bash
   tail -f /var/www/slpa-permit/storage/logs/laravel.log
   ```

5. **Put site in maintenance mode:**
   ```bash
   php artisan down --message="System maintenance in progress"
   ```

6. **Restore from backup if needed:**
   ```bash
   # Restore database
   gunzip < /var/backups/slpa-permit/db_backup_latest.sql.gz | mysql -u slpa_prod -p port_entry_permit_prod
   
   # Clear cache
   php artisan optimize:clear
   ```

7. **Bring site back up:**
   ```bash
   php artisan up
   ```

---

### Emergency: Data Corruption

1. **Take immediate backup:**
   ```bash
   mysqldump -u slpa_prod -p port_entry_permit_prod > emergency_backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Identify corrupted tables:**
   ```sql
   CHECK TABLE permits;
   CHECK TABLE payments;
   ```

3. **Repair tables:**
   ```sql
   REPAIR TABLE table_name;
   ```

4. **Restore from last good backup if repair fails**

---

### Emergency: Security Breach

1. **Take site offline immediately:**
   ```bash
   php artisan down
   ```

2. **Change all passwords:**
   - Database passwords
   - Admin user passwords
   - Server access passwords

3. **Check for unauthorized changes:**
   ```bash
   git status
   git diff
   ```

4. **Review access logs:**
   ```bash
   sudo grep "POST" /var/log/nginx/access.log | tail -1000
   ```

5. **Restore from clean backup**

6. **Update all software**

7. **Review and strengthen security measures**

---

## 📊 Maintenance Checklist

### Daily
- [ ] Check system health
- [ ] Review error logs
- [ ] Verify backups ran
- [ ] Check queue status

### Weekly
- [ ] Optimize database
- [ ] Clean old logs
- [ ] Review user activity
- [ ] Apply security updates

### Monthly
- [ ] Performance review
- [ ] Storage cleanup
- [ ] Archive old data
- [ ] Test backups
- [ ] Check SSL certificates

### Quarterly
- [ ] Security audit
- [ ] Performance testing
- [ ] Comprehensive system report
- [ ] Review and update documentation

---

## 📞 Support Contacts

Maintain updated contact information for:
- System Administrator
- Database Administrator
- Hosting Provider Support
- Laravel Developer
- Security Team

---

**Last Updated:** October 2025  
**Version:** 1.0
