# ProductController Architecture Diagram

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         Client (Vue.js)                          │
│                    Frontend Application                          │
└─────────────────┬───────────────────────────────────────────────┘
                  │ HTTP Request (JSON)
                  │ Authorization: Bearer {token}
                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Laravel API (Backend)                         │
│                                                                   │
│  ┌────────────────────────────────────────────────────────┐    │
│  │                    Route Layer                          │    │
│  │              routes/api.php                             │    │
│  │  • POST /products                                       │    │
│  │  • GET /products                                        │    │
│  │  • PUT /products/{id}                                   │    │
│  │  • DELETE /products/{id}                                │    │
│  │  • PATCH /products/{id}/stock                           │    │
│  │  • GET /products/statistics                             │    │
│  │  • GET /products/low-stock                              │    │
│  └─────────────────────┬──────────────────────────────────┘    │
│                        │                                         │
│                        ▼                                         │
│  ┌────────────────────────────────────────────────────────┐    │
│  │                Middleware Layer                         │    │
│  │           auth:sanctum (Laravel Sanctum)                │    │
│  │  • Verify Bearer Token                                  │    │
│  │  • Load Authenticated User                              │    │
│  │  • CORS Headers                                         │    │
│  └─────────────────────┬──────────────────────────────────┘    │
│                        │                                         │
│                        ▼                                         │
│  ┌────────────────────────────────────────────────────────┐    │
│  │            Form Request Layer                           │    │
│  │  ┌──────────────────────────────────────────────┐      │    │
│  │  │      StoreProductRequest.php                 │      │    │
│  │  │  • Validate input data                       │      │    │
│  │  │  • Return custom errors                      │      │    │
│  │  └──────────────────────────────────────────────┘      │    │
│  │  ┌──────────────────────────────────────────────┐      │    │
│  │  │      UpdateProductRequest.php                │      │    │
│  │  │  • Validate update data                      │      │    │
│  │  │  • Handle partial updates                    │      │    │
│  │  └──────────────────────────────────────────────┘      │    │
│  └─────────────────────┬──────────────────────────────────┘    │
│                        │                                         │
│                        ▼                                         │
│  ┌────────────────────────────────────────────────────────┐    │
│  │               Controller Layer                          │    │
│  │        ProductController.php                            │    │
│  │  ┌──────────────────────────────────────────────┐      │    │
│  │  │  • index() - List products                   │      │    │
│  │  │  • show() - Get single product               │      │    │
│  │  │  • store() - Create product                  │      │    │
│  │  │  • update() - Update product                 │      │    │
│  │  │  • destroy() - Delete product                │      │    │
│  │  │  • updateStock() - Manage stock              │      │    │
│  │  │  • lowStock() - Get low stock items          │      │    │
│  │  │  • statistics() - Get statistics             │      │    │
│  │  │  • handleException() - Error handler         │      │    │
│  │  └──────────────────────────────────────────────┘      │    │
│  └─────────────────────┬──────────────────────────────────┘    │
│                        │                                         │
│                        ▼                                         │
│  ┌────────────────────────────────────────────────────────┐    │
│  │               Service Layer                             │    │
│  │           ProductService.php                            │    │
│  │  ┌──────────────────────────────────────────────┐      │    │
│  │  │  Business Logic Methods:                     │      │    │
│  │  │  • getPaginatedProducts()                    │      │    │
│  │  │  • findProduct()                             │      │    │
│  │  │  • createProduct()                           │      │    │
│  │  │  • updateProduct()                           │      │    │
│  │  │  • deleteProduct()                           │      │    │
│  │  │  • updateStock()                             │      │    │
│  │  │  • getLowStockProducts()                     │      │    │
│  │  │  • getStatistics()                           │      │    │
│  │  │  • clearCache()                              │      │    │
│  │  └──────────────────────────────────────────────┘      │    │
│  └─────────────────────┬──────────────────────────────────┘    │
│                        │                                         │
│             ┌──────────┴──────────┐                             │
│             ▼                     ▼                             │
│  ┌─────────────────┐   ┌─────────────────┐                     │
│  │  Cache Layer    │   │  Model Layer    │                     │
│  │   (Redis)       │   │  Product.php    │                     │
│  │  • Get cached   │   │  • Eloquent ORM │                     │
│  │  • Set cache    │   │  • Relationships│                     │
│  │  • Clear cache  │   │  • Accessors    │                     │
│  └─────────────────┘   └────────┬────────┘                     │
│                                  │                               │
│                                  ▼                               │
│                      ┌─────────────────┐                        │
│                      │   Database      │                        │
│                      │   (SQLite)      │                        │
│                      │  • products     │                        │
│                      └─────────────────┘                        │
│                                  │                               │
│                                  │                               │
│                        ◄─────────┘                               │
│                        Data Retrieved                            │
│                                                                   │
│            Data flows back through layers                        │
│                        │                                         │
│                        ▼                                         │
│  ┌────────────────────────────────────────────────────────┐    │
│  │            Resource Layer                               │    │
│  │  ┌──────────────────────────────────────────────┐      │    │
│  │  │  ProductResource.php                         │      │    │
│  │  │  • Transform single product                  │      │    │
│  │  │  • Add computed fields                       │      │    │
│  │  │  • Format data                               │      │    │
│  │  └──────────────────────────────────────────────┘      │    │
│  │  ┌──────────────────────────────────────────────┐      │    │
│  │  │  ProductCollection.php                       │      │    │
│  │  │  • Transform collections                     │      │    │
│  │  │  • Add pagination meta                       │      │    │
│  │  │  • Add navigation links                      │      │    │
│  │  └──────────────────────────────────────────────┘      │    │
│  └─────────────────────┬──────────────────────────────────┘    │
│                        │                                         │
└────────────────────────┼─────────────────────────────────────────┘
                         │ JSON Response
                         ▼
          ┌──────────────────────────────┐
          │      Client (Vue.js)          │
          │   • Update UI                 │
          │   • Show notification         │
          │   • Update state (Pinia)      │
          └──────────────────────────────┘
```

## Layer Responsibilities

### 1. Route Layer
```
routes/api.php
├── Define HTTP endpoints
├── Group by middleware
├── Map URLs to controllers
└── RESTful resource routing
```

### 2. Middleware Layer
```
auth:sanctum
├── Authenticate requests
├── Verify bearer tokens
├── Load user context
└── Handle CORS
```

### 3. Form Request Layer
```
StoreProductRequest.php / UpdateProductRequest.php
├── Validate input data
├── Authorize requests
├── Custom error messages
└── Sanitize input
```

### 4. Controller Layer
```
ProductController.php
├── Handle HTTP requests
├── Call service methods
├── Return responses
├── Handle exceptions
└── HTTP status codes
```

### 5. Service Layer
```
ProductService.php
├── Business logic
├── Database transactions
├── Caching strategy
├── Error logging
└── Data manipulation
```

### 6. Model Layer
```
Product.php
├── Database representation
├── Eloquent ORM
├── Relationships
├── Accessors/Mutators
└── Query scopes
```

### 7. Resource Layer
```
ProductResource.php / ProductCollection.php
├── Transform data
├── Format output
├── Add computed fields
└── Consistent responses
```

## Request Flow Example

### Creating a Product

```
1. Client (Vue.js)
   POST /api/products
   {
     "name": "Laptop",
     "price": 999.99,
     "stock": 50
   }
   ↓

2. Route Layer
   routes/api.php
   → POST /products → ProductController@store
   ↓

3. Middleware Layer
   auth:sanctum
   → Verify token
   → Load user
   ↓

4. Form Request Layer
   StoreProductRequest
   → Validate name (required, unique)
   → Validate price (required, numeric, min:0)
   → Validate stock (required, integer, min:0)
   ↓

5. Controller Layer
   ProductController@store
   → Receive validated data
   → Call service method
   ↓

6. Service Layer
   ProductService@createProduct
   → Begin transaction
   → Create product
   → Commit transaction
   → Clear cache
   → Log action
   → Return product
   ↓

7. Model Layer
   Product::create()
   → Insert into database
   → Return model instance
   ↓

8. Database Layer
   INSERT INTO products
   VALUES (...)
   ↓

9. Resource Layer
   ProductResource
   → Transform product
   → Add computed fields
   → Format response
   ↓

10. Controller Layer
    → Wrap in success response
    → Return HTTP 201
    ↓

11. Client (Vue.js)
    {
      "success": true,
      "message": "Product created",
      "data": {
        "id": 1,
        "name": "Laptop",
        "price": "999.99",
        "price_formatted": "$999.99",
        "stock": 50,
        "stock_status": "in_stock"
      }
    }
```

## Data Flow Diagram

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   Request    │─────▶│  Validation  │─────▶│  Controller  │
│   (JSON)     │      │   (Rules)    │      │   (HTTP)     │
└──────────────┘      └──────────────┘      └───────┬──────┘
                                                     │
                                                     ▼
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   Response   │◀─────│   Resource   │◀─────│   Service    │
│   (JSON)     │      │  (Transform) │      │  (Business)  │
└──────────────┘      └──────────────┘      └───────┬──────┘
                                                     │
                                       ┌─────────────┴─────────────┐
                                       │                           │
                                       ▼                           ▼
                              ┌──────────────┐          ┌──────────────┐
                              │    Cache     │          │    Model     │
                              │   (Redis)    │          │  (Eloquent)  │
                              └──────────────┘          └───────┬──────┘
                                                               │
                                                               ▼
                                                      ┌──────────────┐
                                                      │   Database   │
                                                      │   (SQLite)   │
                                                      └──────────────┘
```

## Error Handling Flow

```
Exception Occurs
       │
       ▼
┌─────────────────┐
│ Try-Catch Block │
│   in Service    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  DB Rollback    │
│  (if in trans)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Log Error     │
│ (with context)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Throw Exception │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│ Controller Catch Block  │
│  handleException()      │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Determine Exception Type│
│ • ValidationException   │
│ • ModelNotFoundException│
│ • General Exception     │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│  Return JSON Error      │
│  with appropriate code  │
│  • 404 Not Found        │
│  • 422 Validation Error │
│  • 500 Server Error     │
└─────────────────────────┘
```

## Caching Strategy

```
Request for Product ID 1
         │
         ▼
  ┌─────────────┐
  │ Check Cache │
  │  products.1 │
  └──────┬──────┘
         │
    ┌────┴────┐
    │         │
 Found    Not Found
    │         │
    ▼         ▼
 Return   Query DB
  Cache      │
    │        ▼
    │   Store Cache
    │   (5 min TTL)
    │        │
    └────┬───┘
         ▼
     Return Data

On Update/Delete:
         │
         ▼
  ┌──────────────┐
  │ Clear Cache  │
  │ products.1   │
  │ products.all │
  │ products.stats│
  └──────────────┘
```

## Transaction Flow

```
Begin Transaction
       │
       ▼
┌──────────────┐
│ Validate Data│
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ Perform DB   │
│  Operations  │
└──────┬───────┘
       │
  ┌────┴────┐
  │         │
Success  Exception
  │         │
  ▼         ▼
Commit   Rollback
  │         │
  └────┬────┘
       ▼
  Return Result
```

## Security Layers

```
1. Network Layer
   └── HTTPS/SSL

2. Application Layer
   └── CORS Configuration

3. Authentication Layer
   └── Laravel Sanctum (Bearer Tokens)

4. Authorization Layer
   └── Route Middleware (auth:sanctum)

5. Validation Layer
   └── Form Requests (Input Validation)

6. Database Layer
   └── Eloquent ORM (SQL Injection Prevention)

7. Output Layer
   └── Resource Transformers (XSS Prevention)
```

## Performance Optimization Points

```
1. Route Level
   └── Minimal middleware stack

2. Controller Level
   └── Thin controllers (delegate to services)

3. Service Level
   ├── Caching (5 min TTL)
   ├── Query optimization
   └── Lazy loading

4. Model Level
   ├── Selective column loading
   ├── Indexed columns
   └── Efficient relationships

5. Response Level
   ├── Pagination (limit data)
   └── Resource transformation (only needed fields)
```

## Class Dependencies

```
ProductController
    │
    ├──▶ ProductService (injected)
    │       │
    │       ├──▶ Product (model)
    │       ├──▶ Cache (facade)
    │       ├──▶ DB (facade)
    │       └──▶ Log (facade)
    │
    ├──▶ StoreProductRequest (type-hinted)
    ├──▶ UpdateProductRequest (type-hinted)
    ├──▶ ProductResource (static call)
    └──▶ ProductCollection (static call)
```

## File Structure Tree

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── ProductController.php ⭐
│   ├── Requests/
│   │   ├── StoreProductRequest.php ⭐
│   │   └── UpdateProductRequest.php ⭐
│   └── Resources/
│       ├── ProductResource.php ⭐
│       └── ProductCollection.php ⭐
├── Models/
│   └── Product.php
└── Services/
    └── ProductService.php ⭐

routes/
└── api.php ⭐

⭐ = New or significantly improved files
```

## Summary

This architecture follows:
- ✅ **SOLID Principles**
- ✅ **Separation of Concerns**
- ✅ **Dependency Injection**
- ✅ **Repository Pattern** (via Service Layer)
- ✅ **Resource Pattern** (API Resources)
- ✅ **Request-Response Cycle**
- ✅ **Error Handling Strategy**
- ✅ **Caching Strategy**
- ✅ **Transaction Management**
- ✅ **Security Best Practices**

**Result:** A professional, maintainable, scalable, and performant API.
