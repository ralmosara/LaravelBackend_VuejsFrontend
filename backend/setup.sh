#!/bin/bash

echo "Setting up Laravel Backend..."

# Install dependencies
echo "Installing composer dependencies..."
composer install

# Copy env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Generate application key
echo "Generating application key..."
php artisan key:generate

# Create SQLite database if it doesn't exist
if [ ! -f database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch database/database.sqlite
fi

# Install Sanctum
echo "Installing Laravel Sanctum..."
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations
echo "Running migrations..."
php artisan migrate

echo "✓ Backend setup complete!"
echo "Run 'php artisan serve' to start the server"
