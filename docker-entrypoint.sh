#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
RETRIES=30
while ! mysqladmin ping -h"mysql" -u"root" -p"root" --silent; do
    echo "Waiting for MySQL... (${RETRIES} retries left)"
    RETRIES=$((RETRIES - 1))
    if [ $RETRIES -eq 0 ]; then
        echo "❌ MySQL connection failed after 30 attempts"
        exit 1
    fi
    sleep 2
done

echo "✅ MySQL is ready!"

# Wait a bit more for initialization scripts to complete
sleep 5

# Test database connection with our application user
echo "🔗 Testing application database connection..."
RETRIES=10
while ! mysql -h"mysql" -u"sludinajumi" -p"password" -e "USE sludinajumi; SELECT 1;" >/dev/null 2>&1; do
    echo "Testing sludinajumi user connection... (${RETRIES} retries left)"
    RETRIES=$((RETRIES - 1))
    if [ $RETRIES -eq 0 ]; then
        echo "⚠️ sludinajumi user connection failed, attempting to fix..."

        # Try to create/fix the user using root
        mysql -h"mysql" -u"root" -p"root" -e "
            CREATE DATABASE IF NOT EXISTS sludinajumi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            DROP USER IF EXISTS 'sludinajumi'@'%';
            CREATE USER 'sludinajumi'@'%' IDENTIFIED WITH mysql_native_password BY 'password';
            GRANT ALL PRIVILEGES ON sludinajumi.* TO 'sludinajumi'@'%';
            FLUSH PRIVILEGES;
        " || {
            echo "❌ Failed to create sludinajumi user, falling back to root user for development"
            # Update .env to use root user temporarily
            sed -i 's/DB_USERNAME=sludinajumi/DB_USERNAME=root/' /var/www/html/.env
            sed -i 's/DB_PASSWORD=password/DB_PASSWORD=root/' /var/www/html/.env
            break
        }

        # Test again after fix
        if mysql -h"mysql" -u"sludinajumi" -p"password" -e "USE sludinajumi; SELECT 1;" >/dev/null 2>&1; then
            echo "✅ sludinajumi user fixed and working"
            break
        fi
    fi
    sleep 2
done

# Verify database connection with Laravel
echo "🔍 Testing Laravel database configuration..."
if ! php artisan migrate:status >/dev/null 2>&1; then
    echo "⚠️ Database connection test failed, checking configuration..."

    # Show current database config for debugging
    echo "Current database configuration:"
    echo "DB_HOST: $DB_HOST"
    echo "DB_DATABASE: $DB_DATABASE"
    echo "DB_USERNAME: $DB_USERNAME"

    # Last resort: try with root user
    if [ "$DB_USERNAME" != "root" ]; then
        echo "🔄 Switching to root user for database connection..."
        export DB_USERNAME=root
        export DB_PASSWORD=root

        # Update .env file
        sed -i 's/DB_USERNAME=sludinajumi/DB_USERNAME=root/' /var/www/html/.env
        sed -i 's/DB_PASSWORD=password/DB_PASSWORD=root/' /var/www/html/.env

        # Clear config cache
        php artisan config:clear || true
    fi
fi

# Generate app key if not exists or if APP_KEY is empty
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --no-interaction --force
fi

# Clear any cached config before running migrations
echo "🧹 Clearing configuration cache..."
php artisan config:clear || true

# Test database connection and run migrations
echo "📊 Running database migrations..."
if php artisan migrate --force; then
    echo "✅ Migrations completed successfully"

    # Only seed if migration was successful and in local environment
    if [ "$APP_ENV" = "local" ]; then
        echo "🌱 Seeding database..."
        php artisan db:seed --force || echo "⚠️ Database seeding failed, continuing..."
    fi
else
    echo "❌ Migrations failed, but continuing to start server..."
fi

# Set proper permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Cache configuration for better performance (do this after migrations)
echo "⚡ Caching configuration..."
php artisan config:cache || echo "⚠️ Config caching failed, continuing..."
php artisan route:cache || echo "⚠️ Route caching failed, continuing..."

echo "🎉 Laravel application is ready!"

# Start the Laravel development server
exec php artisan serve --host=0.0.0.0 --port=8000
