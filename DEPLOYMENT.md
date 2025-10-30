# Production Deployment Guide - SLPA Port Entry Permit System

## 📋 Table of Contents
- [Pre-Deployment Checklist](#pre-deployment-checklist)
- [Server Requirements](#server-requirements)
- [Deployment Options](#deployment-options)
- [Step-by-Step Deployment](#step-by-step-deployment)
- [Security Hardening](#security-hardening)
- [Performance Optimization](#performance-optimization)
- [Monitoring & Maintenance](#monitoring--maintenance)
- [Backup Strategy](#backup-strategy)
- [Rollback Procedures](#rollback-procedures)

---

## ✅ Pre-Deployment Checklist

### Code Preparation
- [ ] All features tested and working
- [ ] No debug code (dd, dump, console.log)
- [ ] Error handling implemented
- [ ] Validation rules in place
- [ ] Database migrations reviewed
- [ ] .gitignore properly configured

### Security
- [ ] Change all default passwords
- [ ] Remove test/demo users
- [ ] Generate new APP_KEY
- [ ] Set APP_DEBUG=false
- [ ] Set APP_ENV=production
- [ ] Configure HTTPS/SSL
- [ ] Review file permissions
- [ ] Enable CSRF protection
- [ ] Configure CORS if needed
- [ ] Set up rate limiting

### Configuration
- [ ] Production database credentials
- [ ] Production mail settings
- [ ] Queue configuration (Redis/Database)
- [ ] Cache driver configured (Redis recommended)
- [ ] Session driver configured
- [ ] File storage configured
- [ ] Logging level set to 'error'
- [ ] Backup strategy planned

### Dependencies
- [ ] Run `composer install --no-dev`
- [ ] Run `npm run build`
- [ ] Clear development dependencies
- [ ] Optimize autoloader

---

## 🖥️ Server Requirements

### Recommended Production Specifications

**Hardware:**
- **CPU:** 4 cores minimum (8 cores recommended)
- **RAM:** 8GB minimum (16GB recommended)
- **Storage:** 50GB SSD minimum
- **Bandwidth:** 100Mbps minimum

**Software Stack:**
- **OS:** Ubuntu 22.04 LTS or CentOS 8
- **Web Server:** Nginx 1.20+ or Apache 2.4+
- **PHP:** 8.2 with OPcache enabled
- **Database:** MySQL 8.0 or MariaDB 10.6
- **Cache:** Redis 6.0+
- **SSL:** Let's Encrypt or commercial certificate

**PHP Configuration (php.ini):**
```ini
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 20M
post_max_size = 20M
max_input_vars = 3000
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60
```

---

## 🚀 Deployment Options

### Option 1: Traditional Server (VPS/Dedicated)
Best for: Full control, custom configurations
- Manual server setup
- Complete control over environment
- Requires system administration knowledge

### Option 2: Managed Hosting (Laravel Forge/Envoyer)
Best for: Easier management, automated deployments
- Automated server setup
- One-click deployments
- Built-in monitoring

### Option 3: Cloud Platform (AWS/DigitalOcean/Linode)
Best for: Scalability, reliability
- Elastic resources
- Load balancing
- Managed services available

### Option 4: Containerized (Docker/Kubernetes)
Best for: Consistency, scalability
- Environment consistency
- Easy scaling
- Complex setup

---

## 📦 Step-by-Step Deployment

### Phase 1: Server Preparation

#### 1. Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### 2. Install Required Packages
```bash
# Install PHP 8.2 and extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update

sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml \
    php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-gd \
    php8.2-intl php8.2-soap php8.2-redis

# Install MySQL
sudo apt install -y mysql-server

# Install Redis
sudo apt install -y redis-server

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor (for queue workers)
sudo apt install -y supervisor

# Install Certbot (for SSL)
sudo apt install -y certbot python3-certbot-nginx
```

#### 3. Configure MySQL
```bash
sudo mysql_secure_installation
```

Create production database:
```sql
sudo mysql -u root -p

CREATE DATABASE port_entry_permit_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'slpa_prod'@'localhost' IDENTIFIED BY 'SECURE_RANDOM_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON port_entry_permit_prod.* TO 'slpa_prod'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 4. Configure Redis
```bash
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

---

### Phase 2: Application Deployment

#### 1. Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/sahanSS98/SLPA-Permit_System.git slpa-permit
cd slpa-permit
```

#### 2. Set Proper Ownership
```bash
sudo chown -R www-data:www-data /var/www/slpa-permit
sudo chmod -R 755 /var/www/slpa-permit
sudo chmod -R 775 /var/www/slpa-permit/storage
sudo chmod -R 775 /var/www/slpa-permit/bootstrap/cache
```

#### 3. Install Dependencies
```bash
cd /var/www/slpa-permit

# Install PHP dependencies (no dev dependencies)
sudo -u www-data composer install --no-dev --optimize-autoloader

# Install Node dependencies and build
sudo -u www-data npm install
sudo -u www-data npm run build

# Clean up
sudo -u www-data rm -rf node_modules
```

#### 4. Configure Environment
```bash
sudo cp .env.example .env
sudo nano .env
```

**Production .env Configuration:**
```env
APP_NAME="SLPA Permit System"
APP_ENV=production
APP_KEY=base64:GENERATE_NEW_KEY_HERE
APP_DEBUG=false
APP_URL=https://permit.slpa.lk

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit_prod
DB_USERNAME=slpa_prod
DB_PASSWORD=SECURE_RANDOM_PASSWORD_HERE

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis

CACHE_STORE=redis
CACHE_PREFIX=slpa_permit

SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@slpa.lk
MAIL_PASSWORD=SECURE_APP_PASSWORD_HERE
MAIL_FROM_ADDRESS=noreply@slpa.lk
MAIL_FROM_NAME="SLPA Permit System"
```

#### 5. Generate Application Key
```bash
sudo php artisan key:generate
```

#### 6. Run Migrations
```bash
sudo php artisan migrate --force
```

#### 7. Seed Master Data (NOT users with default passwords)
```bash
sudo php artisan db:seed --class=PaymentSettingSeeder --force
sudo php artisan db:seed --class=CompanySeeder --force
sudo php artisan db:seed --class=DesignationSeeder --force
sudo php artisan db:seed --class=VehicleSeeder --force
sudo php artisan db:seed --class=ReasonSeeder --force
```

#### 8. Create Storage Link
```bash
sudo php artisan storage:link
```

#### 9. Optimize Application
```bash
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
sudo php artisan optimize
```

---

### Phase 3: Web Server Configuration

#### Nginx Configuration

Create `/etc/nginx/sites-available/slpa-permit`:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name permit.slpa.lk www.permit.slpa.lk;
    root /var/www/slpa-permit/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Increase upload size
    client_max_body_size 20M;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/slpa-permit /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Apache Configuration (Alternative)

Create `/etc/apache2/sites-available/slpa-permit.conf`:
```apache
<VirtualHost *:80>
    ServerName permit.slpa.lk
    ServerAlias www.permit.slpa.lk
    ServerAdmin admin@slpa.lk
    DocumentRoot /var/www/slpa-permit/public

    <Directory /var/www/slpa-permit/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/slpa-permit-error.log
    CustomLog ${APACHE_LOG_DIR}/slpa-permit-access.log combined

    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite slpa-permit.conf
sudo a2enmod rewrite headers
sudo systemctl reload apache2
```

---

### Phase 4: SSL Certificate (HTTPS)

#### Using Let's Encrypt (Free)
```bash
sudo certbot --nginx -d permit.slpa.lk -d www.permit.slpa.lk
```

#### Auto-renewal
```bash
sudo certbot renew --dry-run
```

Certbot will automatically update Nginx configuration for HTTPS.

---

### Phase 5: Queue Worker Setup

Create Supervisor configuration `/etc/supervisor/conf.d/slpa-permit-worker.conf`:
```ini
[program:slpa-permit-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/slpa-permit/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/slpa-permit/storage/logs/worker.log
stopwaitsecs=3600
```

Start worker:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start slpa-permit-worker:*
```

Check status:
```bash
sudo supervisorctl status
```

---

### Phase 6: Cron Job Setup

```bash
sudo crontab -e -u www-data
```

Add:
```cron
* * * * * cd /var/www/slpa-permit && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Security Hardening

### 1. File Permissions
```bash
# Set strict permissions
sudo find /var/www/slpa-permit -type f -exec chmod 644 {} \;
sudo find /var/www/slpa-permit -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/slpa-permit/storage
sudo chmod -R 775 /var/www/slpa-permit/bootstrap/cache
sudo chmod 600 /var/www/slpa-permit/.env
```

### 2. Disable Directory Listing
Already configured in Nginx/Apache configs above.

### 3. Hide Sensitive Files
```bash
# Ensure .env is not accessible
sudo chmod 600 .env
```

### 4. Configure Firewall (UFW)
```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

### 5. Fail2Ban (Optional but recommended)
```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 6. Disable PHP Functions
Edit `/etc/php/8.2/fpm/php.ini`:
```ini
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source
```

### 7. Regular Security Updates
```bash
# Enable automatic security updates
sudo apt install unattended-upgrades
sudo dpkg-reconfigure --priority=low unattended-upgrades
```

---

## ⚡ Performance Optimization

### 1. Enable OPcache
Edit `/etc/php/8.2/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 2. Configure PHP-FPM
Edit `/etc/php/8.2/fpm/pool.d/www.conf`:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

### 3. Laravel Optimizations
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
composer install --optimize-autoloader --no-dev
```

### 4. Database Optimization
```sql
-- Index commonly queried fields
CREATE INDEX idx_permit_nic ON permits(nic_number);
CREATE INDEX idx_permit_date ON permits(entry_date);
CREATE INDEX idx_permit_status ON permits(status);
```

### 5. Enable Redis for Cache and Sessions
Already configured in .env above.

### 6. CDN for Static Assets (Optional)
- Upload public/build assets to CDN
- Update asset URLs

---

## 📊 Monitoring & Maintenance

### 1. Application Monitoring

**Install Laravel Telescope (Development/Staging only):**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Install Laravel Horizon (For queue monitoring):**
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

### 2. Server Monitoring Tools
```bash
# Install htop
sudo apt install htop

# Install netstat
sudo apt install net-tools
```

### 3. Log Management
```bash
# Rotate logs to prevent disk space issues
sudo nano /etc/logrotate.d/slpa-permit
```

Add:
```
/var/www/slpa-permit/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 4. Health Check Endpoint
Create a simple health check route in `routes/web.php`:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()
    ]);
});
```

### 5. Uptime Monitoring
Use services like:
- UptimeRobot (free)
- Pingdom
- New Relic
- AWS CloudWatch

---

## 💾 Backup Strategy

### 1. Database Backups

**Automated Daily Backups:**
```bash
sudo nano /usr/local/bin/backup-slpa-db.sh
```

Add:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/slpa-permit"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

mysqldump -u slpa_prod -p'PASSWORD' port_entry_permit_prod | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
```

Make executable:
```bash
sudo chmod +x /usr/local/bin/backup-slpa-db.sh
```

Add to cron:
```bash
sudo crontab -e
```

Add:
```cron
0 2 * * * /usr/local/bin/backup-slpa-db.sh
```

### 2. File Backups
```bash
sudo nano /usr/local/bin/backup-slpa-files.sh
```

Add:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/slpa-permit"
DATE=$(date +%Y%m%d_%H%M%S)
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz \
    /var/www/slpa-permit/storage/app \
    /var/www/slpa-permit/.env

# Keep only last 30 days
find $BACKUP_DIR -name "files_backup_*.tar.gz" -mtime +30 -delete
```

### 3. Offsite Backups
Upload backups to cloud storage (AWS S3, Google Cloud Storage, etc.)

---

## 🔄 Rollback Procedures

### 1. Database Rollback
```bash
# Restore from backup
gunzip < /var/backups/slpa-permit/db_backup_YYYYMMDD_HHMMSS.sql.gz | mysql -u slpa_prod -p port_entry_permit_prod
```

### 2. Code Rollback
```bash
cd /var/www/slpa-permit
git log  # Find commit hash to rollback to
git reset --hard COMMIT_HASH
composer install --no-dev --optimize-autoloader
php artisan migrate:rollback --step=1
php artisan optimize:clear
php artisan optimize
```

### 3. Full Application Restore
```bash
# Extract file backup
tar -xzf /var/backups/slpa-permit/files_backup_YYYYMMDD_HHMMSS.tar.gz -C /

# Restore database
gunzip < /var/backups/slpa-permit/db_backup_YYYYMMDD_HHMMSS.sql.gz | mysql -u slpa_prod -p port_entry_permit_prod

# Clear caches
php artisan optimize:clear
```

---

## 📝 Post-Deployment Checklist

- [ ] Application loads without errors
- [ ] HTTPS/SSL working
- [ ] Login functionality works
- [ ] Permit creation works
- [ ] Payment processing works
- [ ] Email notifications work
- [ ] PDF generation works
- [ ] Excel export works
- [ ] Search functionality works
- [ ] Reports generate correctly
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] Backups running
- [ ] Monitoring enabled
- [ ] Error logging working
- [ ] All default passwords changed
- [ ] Admin users created
- [ ] Documentation provided to client

---

## 📞 Emergency Contacts

Maintain a list of emergency contacts:
- System Administrator
- Database Administrator
- Hosting Provider Support
- Laravel Developer

---

## 📚 Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.configuration.php)

---

**Last Updated:** October 2025  
**Version:** 1.0  
**Status:** Production Ready
