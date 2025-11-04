#!/bin/bash

# ============================================================================
# DOCKER ENTRYPOINT SCRIPT
# ============================================================================
# This script runs before starting the Apache server
# It handles Laravel setup tasks like migrations, caching, etc.
# ============================================================================

set -e

echo "=========================================="
echo "SLPA Port Entry Permit System"
echo "Docker Container Initialization"
echo "=========================================="

# Wait for database to be ready
echo "Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
    echo "Database not ready, waiting..."
    sleep 2
done
echo "✓ Database connection established"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "ERROR: .env file not found!"
    echo "Please create .env file from .env.example"
    exit 1
fi

# Generate application key if not set
if grep -q "APP_KEY=$" .env || ! grep -q "APP_KEY=" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
    echo "✓ Application key generated"
fi

# Create storage link if it doesn't exist
if [ ! -L public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
    echo "✓ Storage link created"
fi

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
echo "✓ Permissions set"

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force
echo "✓ Migrations completed"

# Seed database if needed (only in development or first run)
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
    if [ "$RUN_SEEDERS" = "true" ]; then
        echo "Running database seeders..."
        php artisan db:seed --force
        echo "✓ Seeders completed"
    fi
fi

# Clear and cache configuration
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Application optimized"

echo "=========================================="
echo "Initialization completed successfully!"
echo "Application is ready to serve requests"
echo "=========================================="

# Execute the main container command
exec "$@"
