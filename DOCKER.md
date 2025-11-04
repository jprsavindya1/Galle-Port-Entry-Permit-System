# Docker Setup Guide - SLPA Port Entry Permit System

This document provides comprehensive instructions for running the SLPA Port Entry Permit System using Docker.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Docker Commands](#docker-commands)
- [Services](#services)
- [Troubleshooting](#troubleshooting)
- [Production Deployment](#production-deployment)

---

## Prerequisites

Before you begin, ensure you have the following installed:

- **Docker**: Version 20.10 or higher
- **Docker Compose**: Version 2.0 or higher

### Installation Links

- [Docker Desktop for Windows](https://docs.docker.com/desktop/install/windows-install/)
- [Docker Desktop for Mac](https://docs.docker.com/desktop/install/mac-install/)
- [Docker Engine for Linux](https://docs.docker.com/engine/install/)

---

## Quick Start

### 1. Clone the Repository

```bash
git clone <repository-url>
cd port-entry-permit
```

### 2. Create Environment File

Copy the Docker environment template:

```bash
# Windows (PowerShell)
Copy-Item docker\.env.docker .env

# Linux/Mac
cp docker/.env.docker .env
```

### 3. Update Environment Variables

Edit `.env` file and update the following:

```env
# Set a strong database password
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_root_password

# Configure mail settings (if needed)
MAIL_MAILER=smtp
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
```

### 4. Build and Start Containers

```bash
docker-compose up -d --build
```

This will:
- Build the Docker images
- Start all services (app, database, redis, queue worker)
- Run database migrations
- Generate application key
- Optimize Laravel application

### 5. Access the Application

Open your browser and navigate to:
- **Application**: http://localhost:8000
- **phpMyAdmin** (optional): http://localhost:8080

**Default phpMyAdmin credentials:**
- Server: `db`
- Username: `root`
- Password: Value of `DB_ROOT_PASSWORD` from `.env`

---

## Configuration

### Environment Variables

Key environment variables in `.env`:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_PORT` | Port for the application | `8000` |
| `DB_DATABASE` | Database name | `port_entry_permit` |
| `DB_USERNAME` | Database user | `laravel` |
| `DB_PASSWORD` | Database password | `secret` |
| `DB_ROOT_PASSWORD` | MySQL root password | `rootsecret` |
| `REDIS_PORT` | Redis port | `6379` |
| `PHPMYADMIN_PORT` | phpMyAdmin port | `8080` |
| `RUN_SEEDERS` | Run seeders on startup | `false` |

### Custom Ports

If port 8000 is already in use, change it in `.env`:

```env
APP_PORT=8080
```

Then restart containers:

```bash
docker-compose down
docker-compose up -d
```

---

## Docker Commands

### Start Services

```bash
# Start all services
docker-compose up -d

# Start with logs visible
docker-compose up

# Start specific service
docker-compose up -d app
```

### Stop Services

```bash
# Stop all services
docker-compose down

# Stop and remove volumes (⚠️ deletes database data)
docker-compose down -v
```

### View Logs

```bash
# View logs from all services
docker-compose logs

# View logs from specific service
docker-compose logs app
docker-compose logs db

# Follow logs in real-time
docker-compose logs -f app
```

### Execute Commands in Container

```bash
# Access container shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan config:clear

# Run composer commands
docker-compose exec app composer install
docker-compose exec app composer update
```

### Rebuild Containers

```bash
# Rebuild all images
docker-compose build

# Rebuild without cache
docker-compose build --no-cache

# Rebuild and start
docker-compose up -d --build
```

---

## Services

### Application (app)

The main Laravel application running on Apache.

- **Port**: 8000 (configurable)
- **Container**: `slpa-permit-app`
- **Image**: Built from Dockerfile

### Database (db)

MySQL 8.0 database server.

- **Port**: 3306
- **Container**: `slpa-permit-db`
- **Image**: `mysql:8.0`
- **Data**: Persisted in `db-data` volume

### Redis (redis)

Redis server for caching, sessions, and queues.

- **Port**: 6379
- **Container**: `slpa-permit-redis`
- **Image**: `redis:7-alpine`
- **Data**: Persisted in `redis-data` volume

### Queue Worker (queue)

Background job processor for queued tasks.

- **Container**: `slpa-permit-queue`
- **Command**: `php artisan queue:work --tries=3 --timeout=90`

### phpMyAdmin (phpmyadmin) - Optional

Web-based database management tool.

- **Port**: 8080 (configurable)
- **Container**: `slpa-permit-phpmyadmin`
- **Image**: `phpmyadmin:latest`

To start phpMyAdmin:

```bash
docker-compose --profile tools up -d phpmyadmin
```

---

## Troubleshooting

### Container Won't Start

**Check logs:**
```bash
docker-compose logs app
```

**Common issues:**
- Port already in use → Change `APP_PORT` in `.env`
- Missing `.env` file → Copy from `docker/.env.docker`
- Database connection error → Ensure `db` service is running

### Database Connection Failed

```bash
# Check database service status
docker-compose ps db

# Check database logs
docker-compose logs db

# Test database connection
docker-compose exec app php artisan db:show
```

### Permission Errors

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Application Key Not Set

```bash
# Generate new application key
docker-compose exec app php artisan key:generate
```

### Clear Caches

```bash
# Clear all caches
docker-compose exec app php artisan optimize:clear

# Or individually
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear
```

### Reset Database

```bash
# Fresh migration
docker-compose exec app php artisan migrate:fresh

# With seeders
docker-compose exec app php artisan migrate:fresh --seed
```

### Complete Reset

```bash
# Stop and remove everything
docker-compose down -v

# Rebuild from scratch
docker-compose up -d --build
```

---

## Production Deployment

### 1. Update Environment for Production

Edit `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Use strong passwords
DB_PASSWORD=very_secure_random_password
DB_ROOT_PASSWORD=another_secure_password

# Configure production mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=production@yourdomain.com
MAIL_PASSWORD=app_specific_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com

# Set log level
LOG_LEVEL=error

# Session and cache
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

### 2. HTTPS Configuration

For production, use a reverse proxy like **Nginx** or **Traefik** with SSL certificates.

**Example with Nginx:**

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /etc/ssl/certs/your-domain.crt;
    ssl_certificate_key /etc/ssl/private/your-domain.key;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 3. Security Best Practices

- ✅ Use strong, unique passwords
- ✅ Enable HTTPS
- ✅ Set `APP_DEBUG=false`
- ✅ Regular backups of database
- ✅ Keep Docker images updated
- ✅ Implement firewall rules
- ✅ Monitor logs regularly
- ✅ Use environment-specific `.env` files

### 4. Database Backups

Create automated backup script:

```bash
#!/bin/bash
# backup-database.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="./backups"
BACKUP_FILE="$BACKUP_DIR/database_$DATE.sql"

mkdir -p $BACKUP_DIR

docker-compose exec -T db mysqldump \
  -u root \
  -p$DB_ROOT_PASSWORD \
  $DB_DATABASE > $BACKUP_FILE

echo "Backup created: $BACKUP_FILE"
```

Schedule with cron:
```bash
# Run daily at 2 AM
0 2 * * * /path/to/backup-database.sh
```

### 5. Monitoring

```bash
# Monitor container resources
docker stats

# Monitor logs
docker-compose logs -f --tail=100 app

# Health check
docker-compose ps
```

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Docker Compose Documentation](https://docs.docker.com/compose)

---

## Support

For issues and questions:
1. Check the [Troubleshooting](#troubleshooting) section
2. Review application logs: `docker-compose logs app`
3. Contact the development team

---

**Last Updated**: November 2025
