#!/bin/bash
# docker-entrypoint.sh

set -e

echo "🚀 Starting Laravel application..."

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"mysql" -u"sludinajumi" -p"password" --silent; do
    echo "Waiting for MySQL..."
    sleep 2
done

echo "✅ MySQL is ready!"

# Generate app key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --no-interaction
fi

# Run database migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Seed the database if in local environment
if [ "$APP_ENV" = "local" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache

echo "🎉 Laravel application is ready!"

# Start the Laravel development server
php artisan serve --host=0.0.0.0 --port=8000
