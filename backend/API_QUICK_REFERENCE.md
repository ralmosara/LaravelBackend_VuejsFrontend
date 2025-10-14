# Product API - Quick Reference

## Base URL
```
http://localhost:8000/api
```

## Authentication
All endpoints require authentication token:
```
Authorization: Bearer {your-token}
```

---

## Endpoints

### 📋 List Products (Paginated)
```http
GET /products
```

**Query Parameters:**
| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| page | integer | Page number | ?page=2 |
| per_page | integer | Items per page (max 100) | ?per_page=20 |
| search | string | Search in name/description | ?search=laptop |
| min_price | number | Minimum price filter | ?min_price=100 |
| max_price | number | Maximum price filter | ?max_price=1000 |
| in_stock | boolean | Only in-stock items | ?in_stock=1 |
| sort_by | string | Sort field | ?sort_by=price |
| sort_order | string | asc or desc | ?sort_order=desc |

**Example:**
```bash
curl -X GET "http://localhost:8000/api/products?search=laptop&min_price=500&sort_by=price" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 👁️ Get Single Product
```http
GET /products/{id}
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/products/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**
```json
{
  "success": true,
  "message": "Product retrieved successfully",
  "data": {
    "id": 1,
    "name": "Laptop",
    "description": "High-performance laptop",
    "price": "999.99",
    "price_formatted": "$999.99",
    "stock": 50,
    "stock_status": "in_stock",
    "created_at": "2024-01-15 10:30:00",
    "created_at_human": "2 hours ago"
  }
}
```

---

### ➕ Create Product
```http
POST /products
```

**Request Body:**
```json
{
  "name": "Laptop",
  "description": "High-performance laptop",
  "price": 999.99,
  "stock": 50
}
```

**Validation Rules:**
- `name`: required, string, max 255, unique
- `description`: optional, string, max 1000
- `price`: required, number, 0-9999999.99
- `stock`: required, integer, 0-999999

**Example:**
```bash
curl -X POST "http://localhost:8000/api/products" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "MacBook Pro",
    "description": "Latest model",
    "price": 2499.99,
    "stock": 25
  }'
```

---

### ✏️ Update Product
```http
PUT /products/{id}
```

**Request Body:** (all fields optional)
```json
{
  "name": "Updated Laptop",
  "description": "Updated description",
  "price": 1099.99,
  "stock": 45
}
```

**Example:**
```bash
curl -X PUT "http://localhost:8000/api/products/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "price": 899.99,
    "stock": 30
  }'
```

---

### 🗑️ Delete Product
```http
DELETE /products/{id}
```

**Example:**
```bash
curl -X DELETE "http://localhost:8000/api/products/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 📦 Update Stock
```http
PATCH /products/{id}/stock
```

**Request Body:**
```json
{
  "quantity": 10,
  "operation": "add"
}
```

**Operations:**
- `add` - Increase stock
- `subtract` - Decrease stock
- `set` - Set exact amount

**Examples:**
```bash
# Add 50 units
curl -X PATCH "http://localhost:8000/api/products/1/stock" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 50, "operation": "add"}'

# Remove 10 units
curl -X PATCH "http://localhost:8000/api/products/1/stock" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 10, "operation": "subtract"}'

# Set to exactly 100
curl -X PATCH "http://localhost:8000/api/products/1/stock" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 100, "operation": "set"}'
```

---

### ⚠️ Low Stock Products
```http
GET /products/low-stock?threshold=10
```

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| threshold | integer | 10 | Stock level threshold |

**Example:**
```bash
curl -X GET "http://localhost:8000/api/products/low-stock?threshold=20" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 📊 Product Statistics
```http
GET /products/statistics
```

**Example:**
```bash
curl -X GET "http://localhost:8000/api/products/statistics" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_products": 150,
    "in_stock": 120,
    "out_of_stock": 30,
    "low_stock": 15,
    "total_value": 125000.00,
    "average_price": 833.33,
    "total_stock": 5430
  }
}
```

---

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { /* resource data */ }
}
```

### Error Response (404)
```json
{
  "success": false,
  "message": "Product not found"
}
```

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["A product with this name already exists."],
    "price": ["Product price must be a valid number."]
  }
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to process request",
  "error": "Error details (only in debug mode)"
}
```

---

## HTTP Status Codes

| Code | Meaning | When Used |
|------|---------|-----------|
| 200 | OK | Successful GET, PUT, PATCH, DELETE |
| 201 | Created | Successful POST |
| 404 | Not Found | Product doesn't exist |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Server Error | Unexpected error occurred |

---

## Stock Status

Products automatically get a `stock_status` field:

| Status | Condition | Display |
|--------|-----------|---------|
| `out_of_stock` | stock = 0 | Red badge |
| `low_stock` | stock < 10 | Yellow badge |
| `in_stock` | stock >= 10 | Green badge |

---

## Pagination Metadata

List responses include pagination info:

```json
{
  "data": { /* products */ },
  "meta": {
    "total": 100,
    "count": 10,
    "per_page": 10,
    "current_page": 1,
    "total_pages": 10,
    "has_more_pages": true
  },
  "links": {
    "first": "http://api.com/products?page=1",
    "last": "http://api.com/products?page=10",
    "prev": null,
    "next": "http://api.com/products?page=2"
  }
}
```

---

## Frontend Integration (Vue.js)

### Using API Service

```javascript
import api from '@/services/api'

// List products
const products = await api.get('/products', {
  params: { search: 'laptop', per_page: 20 }
})

// Get single product
const product = await api.get(`/products/${id}`)

// Create product
const newProduct = await api.post('/products', {
  name: 'New Product',
  price: 99.99,
  stock: 50
})

// Update product
await api.put(`/products/${id}`, { price: 89.99 })

// Delete product
await api.delete(`/products/${id}`)

// Update stock
await api.patch(`/products/${id}/stock`, {
  quantity: 10,
  operation: 'add'
})

// Get statistics
const stats = await api.get('/products/statistics')

// Get low stock
const lowStock = await api.get('/products/low-stock', {
  params: { threshold: 15 }
})
```

---

## Advanced Examples

### Complex Search & Filter
```bash
curl -X GET "http://localhost:8000/api/products?\
search=laptop&\
min_price=500&\
max_price=2000&\
in_stock=1&\
sort_by=price&\
sort_order=asc&\
per_page=20&\
page=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Bulk Stock Update (using loop)
```javascript
const products = [1, 2, 3, 4, 5]
for (const id of products) {
  await api.patch(`/products/${id}/stock`, {
    quantity: 100,
    operation: 'add'
  })
}
```

### Dashboard Statistics
```javascript
const stats = await api.get('/products/statistics')
console.log(`Total Inventory Value: $${stats.data.total_value}`)
console.log(`Average Product Price: $${stats.data.average_price}`)
console.log(`Products Needing Restock: ${stats.data.low_stock}`)
```

---

## Rate Limiting (Future)

When implemented, rate limits will be:
- 60 requests per minute per user
- Headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`

---

## Versioning (Future)

API versioning will follow this pattern:
```
/api/v1/products
/api/v2/products
```

---

## Testing with Postman

1. Import environment variable:
   - `BASE_URL`: `http://localhost:8000/api`
   - `TOKEN`: `your-auth-token`

2. Use `{{BASE_URL}}` and `{{TOKEN}}` in requests

3. Example request:
   ```
   GET {{BASE_URL}}/products
   Authorization: Bearer {{TOKEN}}
   ```

---

## Common Issues

### Issue: 401 Unauthorized
**Solution:** Make sure you include the Bearer token in the Authorization header

### Issue: 422 Validation Error
**Solution:** Check the `errors` object in the response for specific field errors

### Issue: 404 Not Found
**Solution:** Verify the product ID exists and the endpoint URL is correct

### Issue: 500 Server Error
**Solution:** Check Laravel logs at `storage/logs/laravel.log`

---

## Support

For issues or questions:
1. Check API documentation
2. Review Laravel logs
3. Test with Postman/curl
4. Check database records

---

**Last Updated:** 2024-01-15
**API Version:** 1.0
**Laravel Version:** 12.x
