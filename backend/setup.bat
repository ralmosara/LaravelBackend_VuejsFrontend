@echo off
echo Setting up Laravel Backend...

:: Install dependencies
echo Installing composer dependencies...
call composer install

:: Copy env file if it doesn't exist
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
)

:: Generate application key
echo Generating application key...
call php artisan key:generate

:: Create SQLite database if it doesn't exist
if not exist database\database.sqlite (
    echo Creating SQLite database...
    type nul > database\database.sqlite
)

:: Install Sanctum
echo Installing Laravel Sanctum...
call composer require laravel/sanctum
call php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

:: Run migrations
echo Running migrations...
call php artisan migrate

echo.
echo ✓ Backend setup complete!
echo Run 'php artisan serve' to start the server
pause
