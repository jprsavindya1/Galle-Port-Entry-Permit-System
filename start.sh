#!/bin/sh

# Run migrations
php artisan migrate --force

# Create admin user if no users exist
php artisan user:create-admin admin@slpa.lk Admin123 || echo "Admin user already exists or created"

# Start the application (FrankenPHP handles this automatically on Railway)
# php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
