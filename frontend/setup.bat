@echo off
echo Setting up Vue.js Frontend...

:: Install dependencies
echo Installing npm dependencies...
call npm install

:: Copy env file if it doesn't exist
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
)

echo.
echo ✓ Frontend setup complete!
echo Run 'npm run dev' to start the development server
pause
