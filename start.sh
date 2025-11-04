#!/bin/sh

# Run migrations
php artisan migrate --force

# Run seeders only if users table is empty
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();")
if [ "$USER_COUNT" = "0" ]; then
    echo "No users found. Running seeders..."
    php artisan db:seed --force
fi

# Start the application
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
