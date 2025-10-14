# Laravel Vue.js Full Stack Application

A professional full-stack application built with Laravel 12 (Backend API) and Vue.js 3 (Frontend).

## Features

- **Authentication System**: User registration and login with JWT tokens
- **Product Management**: Full CRUD operations for products
- **RESTful API**: Well-structured API endpoints
- **Modern UI**: Clean and responsive user interface
- **State Management**: Pinia for Vue.js state management
- **Routing**: Vue Router with route guards
- **API Integration**: Axios with interceptors

## Tech Stack

### Backend
- Laravel 12
- Laravel Sanctum (API Authentication)
- MySQL/SQLite Database
- RESTful API Architecture

### Frontend
- Vue.js 3 (Composition API)
- Vite (Build Tool)
- Vue Router 4
- Pinia (State Management)
- Axios (HTTP Client)

## Project Structure

```
LaravelVueJS/
├── backend/          # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   └── ProductController.php
│   │   └── Models/
│   │       └── Product.php
│   ├── database/migrations/
│   ├── routes/api.php
│   └── config/cors.php
│
└── frontend/         # Vue.js App
    ├── src/
    │   ├── components/
    │   │   └── Navbar.vue
    │   ├── views/
    │   │   ├── Login.vue
    │   │   ├── Register.vue
    │   │   ├── Dashboard.vue
    │   │   └── Products.vue
    │   ├── stores/
    │   │   ├── auth.js
    │   │   └── products.js
    │   ├── services/
    │   │   └── api.js
    │   ├── router/
    │   │   └── index.js
    │   └── App.vue
    └── package.json
```

## Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL or SQLite

### Backend Setup

1. Navigate to backend directory:
```bash
cd backend
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure database in `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_vue
DB_USERNAME=root
DB_PASSWORD=
```

Or use SQLite:
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

6. Create database (if using SQLite):
```bash
touch database/database.sqlite
```

7. Run migrations:
```bash
php artisan migrate
```

8. Install Laravel Sanctum:
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

9. Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### Frontend Setup

1. Navigate to frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Update `.env` if needed:
```env
VITE_API_URL=http://localhost:8000/api
```

5. Start development server:
```bash
npm run dev
```

The application will be available at `http://localhost:5173`

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `GET /api/user` - Get authenticated user (protected)
- `POST /api/logout` - Logout user (protected)

### Products (Protected Routes)
- `GET /api/products` - Get all products
- `GET /api/products/{id}` - Get single product
- `POST /api/products` - Create new product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

## Usage

1. Start the backend server (Laravel)
2. Start the frontend server (Vue.js)
3. Open your browser to `http://localhost:5173`
4. Register a new account or login
5. Access the dashboard and manage products

## Default Credentials

You need to register a new account first. Use the registration form at `/register`.

## Building for Production

### Backend
```bash
cd backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Frontend
```bash
cd frontend
npm run build
```

The production files will be in the `frontend/dist` directory.

## Features in Detail

### Authentication
- Secure user registration with password hashing
- JWT-based authentication using Laravel Sanctum
- Protected routes with authentication middleware
- Automatic token refresh

### Product Management
- Create, read, update, and delete products
- Form validation on both frontend and backend
- Real-time UI updates
- Error handling and user feedback

### UI/UX
- Responsive design
- Clean and modern interface
- Loading states
- Error messages
- Form validation feedback

## Contributing

Feel free to submit issues and enhancement requests!

## License

This project is open-sourced software licensed under the MIT license.
