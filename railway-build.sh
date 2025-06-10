#!/bin/bash
set -e # Exit immediately if a command exits with a non-zero status.

echo "--- Starting Railway Build Process ---"

echo "1. Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "2. Setting up database..."
# The application expects the database at /tmp/database.sqlite
# Let's ensure the directory and file exist.
mkdir -p /tmp
touch /tmp/database.sqlite
echo "Created /tmp/database.sqlite"

echo "3. Running database migrations..."
# Add --verbose for more output
php artisan migrate:fresh --force --verbose

echo "4. Seeding database..."
php artisan db:seed --class=JsonSeeder --verbose

echo "5. Optimizing application..."
php artisan optimize

echo "--- Railway Build Process Finished ---"
