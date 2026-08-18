#!/bin/bash
set -e

# Replace port in apache config if PORT environment variable is set
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
fi

# Ensure database directory and sqlite file exist if using sqlite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
fi

# Run migrations and seeder
php artisan migrate --force || true
php artisan db:seed --force || true

# Storage link
php artisan storage:link || true

# Cache configurations for production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
