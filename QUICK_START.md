# Quick Start Guide

## Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm or yarn

## Quick Setup (Windows)

### Option 1: Automated Setup

**Backend:**
```bash
cd backend
setup.bat
```

**Frontend:**
```bash
cd frontend
setup.bat
```

### Option 2: Manual Setup

**Backend:**
```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
type nul > database\database.sqlite
php artisan migrate
php artisan serve
```

**Frontend:**
```bash
cd frontend
npm install
copy .env.example .env
npm run dev
```

## Quick Setup (Linux/Mac)

**Backend:**
```bash
cd backend
chmod +x setup.sh
./setup.sh
php artisan serve
```

**Frontend:**
```bash
cd frontend
chmod +x setup.sh
./setup.sh
npm run dev
```

## Access the Application

- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api

## First Steps

1. Open http://localhost:5173 in your browser
2. Click "Register here" to create a new account
3. Fill in your details (name, email, password)
4. You'll be automatically logged in and redirected to the dashboard
5. Navigate to "Products" to manage products

## API Testing

You can test the API using tools like Postman or curl:

### Register User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Products (with token)
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

## Troubleshooting

### Backend Issues

**Port 8000 already in use:**
```bash
php artisan serve --port=8001
```
Then update frontend .env: `VITE_API_URL=http://localhost:8001/api`

**Database errors:**
```bash
php artisan migrate:fresh
```

**Permission errors:**
```bash
chmod -R 775 storage bootstrap/cache
```

### Frontend Issues

**Port 5173 already in use:**
Update `vite.config.js`:
```js
server: {
  port: 3000,
}
```

**Module not found:**
```bash
rm -rf node_modules package-lock.json
npm install
```

**API connection errors:**
- Check backend is running on http://localhost:8000
- Check .env file has correct VITE_API_URL
- Check CORS configuration in backend/config/cors.php

## Development Tips

### Hot Reload
Both frontend and backend support hot reload:
- Frontend: Vite automatically reloads on file changes
- Backend: Use `php artisan serve` (restarts on most changes)

### Database Reset
To reset database with fresh migrations:
```bash
cd backend
php artisan migrate:fresh
```

### Clear Caches
```bash
cd backend
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Next Steps

- Customize the UI in `frontend/src/`
- Add more API endpoints in `backend/app/Http/Controllers/Api/`
- Add new models and migrations
- Implement additional features (file uploads, roles, etc.)

## Need Help?

Check the main [README.md](README.md) for detailed documentation.
