# Project Structure

## Overview

This is a full-stack application with a clear separation between backend (Laravel API) and frontend (Vue.js SPA).

```
LaravelVueJS/
├── backend/              # Laravel 12 API
├── frontend/             # Vue.js 3 SPA
├── README.md            # Main documentation
├── QUICK_START.md       # Quick setup guide
└── PROJECT_STRUCTURE.md # This file
```

## Backend Structure (Laravel)

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php      # Authentication endpoints
│   │   │       └── ProductController.php   # Product CRUD operations
│   │   └── Middleware/
│   └── Models/
│       ├── User.php                        # User model with Sanctum
│       └── Product.php                     # Product model
│
├── config/
│   └── cors.php                            # CORS configuration
│
├── database/
│   ├── migrations/
│   │   ├── *_create_users_table.php
│   │   ├── *_create_products_table.php     # Product table migration
│   │   └── *_personal_access_tokens.php    # Sanctum tokens
│   └── database.sqlite                     # SQLite database
│
├── routes/
│   ├── api.php                             # API routes
│   ├── web.php                             # Web routes
│   └── console.php                         # Console commands
│
├── bootstrap/
│   └── app.php                             # Application bootstrap
│
├── .env                                    # Environment variables
├── .env.example                            # Environment template
├── composer.json                           # PHP dependencies
├── setup.sh                                # Linux/Mac setup script
└── setup.bat                               # Windows setup script
```

### Key Backend Files

#### [app/Http/Controllers/Api/AuthController.php](backend/app/Http/Controllers/Api/AuthController.php)
Handles user authentication:
- `register()` - User registration
- `login()` - User login
- `user()` - Get authenticated user
- `logout()` - User logout

#### [app/Http/Controllers/Api/ProductController.php](backend/app/Http/Controllers/Api/ProductController.php)
RESTful product controller:
- `index()` - List all products
- `store()` - Create new product
- `show($id)` - Get single product
- `update($id)` - Update product
- `destroy($id)` - Delete product

#### [routes/api.php](backend/routes/api.php)
API route definitions:
```php
POST   /api/register         # Public
POST   /api/login            # Public
GET    /api/user             # Protected
POST   /api/logout           # Protected
CRUD   /api/products         # Protected
```

## Frontend Structure (Vue.js)

```
frontend/
├── src/
│   ├── components/
│   │   └── Navbar.vue                      # Navigation component
│   │
│   ├── views/
│   │   ├── Login.vue                       # Login page
│   │   ├── Register.vue                    # Registration page
│   │   ├── Dashboard.vue                   # Dashboard with stats
│   │   └── Products.vue                    # Product management
│   │
│   ├── stores/
│   │   ├── auth.js                         # Auth state (Pinia)
│   │   └── products.js                     # Product state (Pinia)
│   │
│   ├── services/
│   │   └── api.js                          # Axios configuration
│   │
│   ├── router/
│   │   └── index.js                        # Vue Router setup
│   │
│   ├── App.vue                             # Root component
│   ├── main.js                             # Application entry
│   └── style.css                           # Global styles
│
├── public/                                 # Static assets
├── index.html                              # HTML template
├── vite.config.js                          # Vite configuration
├── package.json                            # npm dependencies
├── .env                                    # Environment variables
├── .env.example                            # Environment template
├── setup.sh                                # Linux/Mac setup script
└── setup.bat                               # Windows setup script
```

### Key Frontend Files

#### [src/services/api.js](frontend/src/services/api.js)
Axios instance with:
- Request interceptor (adds auth token)
- Response interceptor (handles 401 errors)
- Base URL configuration

#### [src/stores/auth.js](frontend/src/stores/auth.js)
Authentication store (Pinia):
- `user` - Current user object
- `token` - JWT token
- `isAuthenticated` - Auth status
- `login()` - Login user
- `register()` - Register user
- `logout()` - Logout user
- `checkAuth()` - Verify token

#### [src/stores/products.js](frontend/src/stores/products.js)
Product store (Pinia):
- `products` - Products array
- `loading` - Loading state
- `fetchProducts()` - Get all products
- `createProduct()` - Create product
- `updateProduct()` - Update product
- `deleteProduct()` - Delete product

#### [src/router/index.js](frontend/src/router/index.js)
Route definitions with guards:
```
/login           # Login page (guest only)
/register        # Register page (guest only)
/                # Dashboard (protected)
/products        # Products page (protected)
```

## Data Flow

### Authentication Flow
```
1. User submits login form
   ↓
2. Login.vue calls authStore.login()
   ↓
3. authStore makes API call via services/api.js
   ↓
4. Backend AuthController validates & returns token
   ↓
5. authStore saves token to localStorage
   ↓
6. Router redirects to dashboard
   ↓
7. Navbar.vue shows user info from authStore
```

### Product CRUD Flow
```
1. User clicks "Add Product"
   ↓
2. Products.vue shows modal form
   ↓
3. User submits form
   ↓
4. Products.vue calls productStore.createProduct()
   ↓
5. productStore makes API call with auth token
   ↓
6. Backend ProductController validates & saves
   ↓
7. productStore updates local products array
   ↓
8. Products.vue shows updated table
```

## API Communication

### Request Flow
```
Frontend Component
  ↓
Pinia Store
  ↓
services/api.js (Axios)
  ↓ [adds Bearer token]
Backend API Route
  ↓
Controller Method
  ↓
Model/Database
  ↓ [returns data]
JSON Response
  ↓
Pinia Store (updates state)
  ↓
Component (reactive update)
```

### Response Format
All API responses follow this structure:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { }
}
```

Error responses:
```json
{
  "success": false,
  "message": "Error message",
  "errors": { }
}
```

## State Management

### Frontend State (Pinia)
- **Auth Store**: User authentication state
- **Product Store**: Product data and operations
- **Local Storage**: Persists token and user data

### Backend State
- **Database**: SQLite (dev) / MySQL (production)
- **Sessions**: Stateless (token-based)
- **Cache**: Not implemented (can add Redis)

## Security Features

### Backend
- Password hashing (bcrypt)
- CSRF protection
- CORS configuration
- Rate limiting (can be added)
- Input validation
- SQL injection protection (Eloquent ORM)

### Frontend
- XSS protection (Vue automatic escaping)
- Token-based auth (no cookies)
- Route guards
- Automatic token refresh
- 401 handling (auto logout)

## Configuration Files

### Backend
- `.env` - Environment configuration
- `config/cors.php` - CORS settings
- `config/auth.php` - Authentication config
- `config/database.php` - Database config

### Frontend
- `.env` - Environment variables
- `vite.config.js` - Build tool config
- `package.json` - Dependencies and scripts

## Development Workflow

1. **Backend Changes**
   - Modify controllers/models
   - Create migrations if needed
   - Update routes if needed
   - Test via Postman/curl

2. **Frontend Changes**
   - Update components/views
   - Modify stores if needed
   - Update router if needed
   - Test in browser

3. **Full Stack Feature**
   - Create migration (backend)
   - Create model (backend)
   - Create controller (backend)
   - Add routes (backend)
   - Create store (frontend)
   - Create/update components (frontend)
   - Add routes (frontend)

## Customization Points

### Easy to Modify
- UI colors and styles ([frontend/src/style.css](frontend/src/style.css))
- Page layouts (views/*.vue)
- Navigation items ([frontend/src/components/Navbar.vue](frontend/src/components/Navbar.vue))
- API endpoints ([backend/routes/api.php](backend/routes/api.php))

### Medium Complexity
- Add new models and migrations
- Add new API controllers
- Add new Vue components
- Modify authentication logic

### Advanced
- Add file upload functionality
- Implement real-time features (WebSockets)
- Add roles and permissions
- Implement caching strategy
- Add email notifications

## Performance Considerations

### Backend
- Database indexing (add as needed)
- Query optimization (eager loading)
- API response caching
- Queue jobs for heavy tasks

### Frontend
- Component lazy loading (already implemented)
- Image optimization
- Bundle size optimization
- Service worker (PWA)

## Testing

### Backend (Not Included)
Can add:
- PHPUnit for unit tests
- Feature tests for API endpoints
- Database tests with factories

### Frontend (Not Included)
Can add:
- Vitest for unit tests
- Vue Test Utils for component tests
- Cypress/Playwright for E2E tests

## Deployment

### Backend
1. Set `APP_ENV=production` in `.env`
2. Run `composer install --optimize-autoloader --no-dev`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Set up proper database (MySQL)
7. Configure web server (Nginx/Apache)

### Frontend
1. Update `.env` with production API URL
2. Run `npm run build`
3. Deploy `dist/` folder to web server
4. Configure web server for SPA routing

## Common Extensions

### Adding Email Verification
1. Backend: Implement MustVerifyEmail
2. Frontend: Add verification UI

### Adding Roles/Permissions
1. Backend: Install Spatie Permission
2. Frontend: Add role-based UI

### Adding File Uploads
1. Backend: Configure storage, add upload endpoints
2. Frontend: Add file input components

### Adding Search/Filters
1. Backend: Add query parameters to controllers
2. Frontend: Add search UI and filters
