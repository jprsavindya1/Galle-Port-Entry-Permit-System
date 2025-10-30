# Installation Guide - SLPA Port Entry Permit System

## 📋 Table of Contents
- [System Requirements](#system-requirements)
- [Development Installation](#development-installation)
- [Windows Installation (XAMPP)](#windows-installation-xampp)
- [Linux Installation](#linux-installation)
- [Docker Installation](#docker-installation)
- [Post-Installation](#post-installation)
- [Troubleshooting](#troubleshooting)

---

## 🖥️ System Requirements

### Minimum Requirements
- **PHP:** 8.2 or higher
- **Database:** MySQL 8.0+ or MariaDB 10.3+
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **Composer:** 2.0 or higher
- **Node.js:** 18.0 or higher
- **NPM:** 9.0 or higher

### PHP Extensions Required
```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_MySQL
- Tokenizer
- XML
- GD or Imagick (for PDF generation)
- Zip
```

### Server Requirements
- **RAM:** Minimum 2GB (4GB recommended)
- **Disk Space:** Minimum 500MB
- **PHP Memory Limit:** 256MB minimum (512MB recommended)
- **PHP Max Execution Time:** 300 seconds minimum

---

## 🚀 Development Installation

### Step 1: Verify Prerequisites

```bash
# Check PHP version
php -v
# Should show PHP 8.2.x or higher

# Check Composer
composer -V
# Should show Composer version 2.x

# Check Node.js and NPM
node -v
npm -v

# Check MySQL
mysql --version
```

### Step 2: Clone Repository

```bash
git clone https://github.com/sahanSS98/SLPA-Permit_System.git
cd port-entry-permit
```

### Step 3: Install PHP Dependencies

```bash
composer install
```

**If you encounter memory errors:**
```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```

### Step 4: Install Node Dependencies

```bash
npm install
```

**If you encounter permission errors (Linux/Mac):**
```bash
sudo npm install --unsafe-perm
```

### Step 5: Environment Configuration

```bash
# Windows PowerShell
Copy-Item .env.example .env

# Windows Command Prompt
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### Step 6: Configure Environment File

Edit `.env` file with your settings:

```env
APP_NAME="SLPA Permit System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="SLPA Permit System"

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

### Step 7: Create Database

**Using MySQL Command Line:**
```sql
mysql -u root -p
CREATE DATABASE port_entry_permit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Using phpMyAdmin:**
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "New" in the sidebar
3. Database name: `port_entry_permit`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 8: Run Migrations

```bash
php artisan migrate
```

If successful, you'll see:
```
Migration table created successfully.
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table
...
```

### Step 9: Seed Database (Optional but Recommended)

```bash
php artisan db:seed
```

This creates:
- 5 default users (super-admin, admin, clerks, staff)
- 25 companies
- 34 designations
- 20 vehicle types with rates
- 36 entry reasons
- Payment settings

### Step 10: Create Storage Link

```bash
php artisan storage:link
```

### Step 11: Build Frontend Assets

**For Development (with hot reload):**
```bash
npm run dev
```
Keep this running in a separate terminal.

**For Production Build:**
```bash
npm run build
```

### Step 12: Start Development Server

```bash
php artisan serve
```

Application will be available at: **http://localhost:8000**

### Step 13: Start Queue Worker (Optional)

For email notifications and background jobs:
```bash
# In a separate terminal
php artisan queue:work
```

---

## 🪟 Windows Installation (XAMPP)

### Prerequisites
1. Download and install [XAMPP](https://www.apachefriends.org/) (PHP 8.2+)
2. Download and install [Composer](https://getcomposer.org/download/)
3. Download and install [Node.js](https://nodejs.org/)
4. Download and install [Git](https://git-scm.com/)

### Installation Steps

#### 1. Start XAMPP Services
- Open XAMPP Control Panel
- Start **Apache** and **MySQL** modules

#### 2. Clone Project to XAMPP htdocs
```powershell
cd C:\xampp\htdocs
git clone https://github.com/sahanSS98/SLPA-Permit_System.git
cd port-entry-permit
```

#### 3. Install Dependencies
```powershell
composer install
npm install
```

#### 4. Configure Environment
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=port_entry_permit
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Create Database
- Open http://localhost/phpmyadmin
- Create database: `port_entry_permit`
- Collation: `utf8mb4_unicode_ci`

#### 6. Run Migrations and Seeds
```powershell
php artisan migrate
php artisan db:seed
```

#### 7. Build Assets
```powershell
npm run build
```

#### 8. Configure Virtual Host (Optional)

Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/port-entry-permit/public"
    ServerName slpa-permit.local
    
    <Directory "C:/xampp/htdocs/port-entry-permit/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Edit `C:\Windows\System32\drivers\etc\hosts` (as Administrator):
```
127.0.0.1 slpa-permit.local
```

Restart Apache in XAMPP.

Access at: **http://slpa-permit.local**

---

## 🐧 Linux Installation

### Ubuntu/Debian

#### 1. Update System
```bash
sudo apt update
sudo apt upgrade -y
```

#### 2. Install PHP 8.2 and Extensions
```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update

sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
    php8.2-xml php8.2-curl php8.2-mbstring php8.2-zip \
    php8.2-bcmath php8.2-gd php8.2-intl php8.2-soap
```

#### 3. Install MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

Create database:
```bash
sudo mysql -u root -p
CREATE DATABASE port_entry_permit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'slpa_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON port_entry_permit.* TO 'slpa_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 4. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 5. Install Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 6. Install Apache (or Nginx)
```bash
sudo apt install -y apache2
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### 7. Clone and Setup Project
```bash
cd /var/www
sudo git clone https://github.com/sahanSS98/SLPA-Permit_System.git
sudo mv SLPA-Permit_System slpa-permit
cd slpa-permit

# Set permissions
sudo chown -R www-data:www-data /var/www/slpa-permit
sudo chmod -R 755 /var/www/slpa-permit
sudo chmod -R 775 storage bootstrap/cache
```

#### 8. Install Dependencies
```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
```

#### 9. Configure Environment
```bash
sudo cp .env.example .env
sudo php artisan key:generate
sudo nano .env  # Edit database credentials
```

#### 10. Run Migrations
```bash
sudo php artisan migrate --force
sudo php artisan db:seed --force
```

#### 11. Build Assets
```bash
sudo npm run build
```

#### 12. Configure Apache Virtual Host

Create `/etc/apache2/sites-available/slpa-permit.conf`:
```apache
<VirtualHost *:80>
    ServerName slpa-permit.com
    ServerAdmin admin@slpa-permit.com
    DocumentRoot /var/www/slpa-permit/public

    <Directory /var/www/slpa-permit/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/slpa-permit-error.log
    CustomLog ${APACHE_LOG_DIR}/slpa-permit-access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite slpa-permit.conf
sudo systemctl reload apache2
```

---

## 🐳 Docker Installation

### Prerequisites
- Docker
- Docker Compose

### Using Laravel Sail

#### 1. Clone Repository
```bash
git clone https://github.com/sahanSS98/SLPA-Permit_System.git
cd port-entry-permit
```

#### 2. Install Dependencies via Docker
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

#### 3. Configure Environment
```bash
cp .env.example .env
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=port_entry_permit
DB_USERNAME=sail
DB_PASSWORD=password
```

#### 4. Start Sail
```bash
./vendor/bin/sail up -d
```

#### 5. Generate Key and Migrate
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

#### 6. Build Assets
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Access at: **http://localhost**

---

## ✅ Post-Installation

### 1. Verify Installation
Visit your application URL and check:
- [ ] Application loads without errors
- [ ] Login page is accessible
- [ ] CSS/JS assets load correctly

### 2. Test Login
Use default credentials (if seeded):
- Email: `admin@slpa.lk`
- Password: `password`

### 3. Configure Cron Jobs (Production)

Add to crontab:
```bash
crontab -e
```

Add line:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Setup Queue Worker (Production)

Using Supervisor (Linux):
```bash
sudo apt install supervisor

sudo nano /etc/supervisor/conf.d/slpa-permit-worker.conf
```

Add:
```ini
[program:slpa-permit-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/slpa-permit/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/slpa-permit/storage/logs/worker.log
```

Start:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start slpa-permit-worker:*
```

### 5. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. "Permission Denied" Errors
```bash
# Windows (Run as Administrator)
# Set folder permissions manually in Properties

# Linux
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

#### 2. "Class Not Found" Errors
```bash
composer dump-autoload
php artisan optimize:clear
```

#### 3. Database Connection Errors
- Verify MySQL is running
- Check credentials in `.env`
- Test connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 4. NPM Install Errors
```bash
# Clear npm cache
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

#### 5. Migration Errors
```bash
# Fresh start
php artisan migrate:fresh

# Or drop all tables first
php artisan db:wipe
php artisan migrate
```

#### 6. Assets Not Loading
```bash
# Clear and rebuild
npm run build
php artisan optimize:clear
```

#### 7. "500 Internal Server Error"
- Check `storage/logs/laravel.log`
- Verify `.env` APP_KEY is set
- Check file permissions

#### 8. Queue Not Processing
```bash
# Clear failed jobs
php artisan queue:flush

# Restart queue
php artisan queue:restart

# Start worker
php artisan queue:work --tries=3
```

---

## 📞 Support

If you encounter issues not covered here:
1. Check `storage/logs/laravel.log` for detailed errors
2. Review [Laravel Documentation](https://laravel.com/docs)
3. Create an issue on [GitHub](https://github.com/sahanSS98/SLPA-Permit_System/issues)

---

**Last Updated:** October 2025  
**Version:** 1.0
