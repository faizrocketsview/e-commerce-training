### E‑Commerce Training API (Formation)

Base URL: `http://e-commerce-training.test/api`

This project exposes RESTful endpoints auto-wired by the Formation package. All CRUD endpoints follow the pattern:

- Index: `GET /api/{moduleSection}/{moduleGroup}/{modules}`
- Show: `GET /api/{moduleSection}/{moduleGroup}/{modules}/{id}`
- Create: `POST /api/{moduleSection}/{moduleGroup}/{modules}`
- Update: `PUT /api/{moduleSection}/{moduleGroup}/{modules}/{id}`
- Delete: `DELETE /api/{moduleSection}/{moduleGroup}/{modules}/{id}`

For this app:

- `{moduleSection}`: `ecommerce`
- `{moduleGroup}`: `managements`
- `{modules}`: `users`, `products`, `categories`, `orders`, `items`

All protected routes require a Sanctum bearer token.

---

### Authentication (Sanctum)

Request a token:

POST `http://e-commerce-training.test/api/sanctum/token`

Headers:

- `Content-Type: application/json`

Body:

```json
{
  "email": "admin@example.com",
  "password": "password123",
  "device_name": "postman"
}
```

Response:

```
<plain_token_string>
```

Use it as `Authorization: Bearer <plain_token_string>` for subsequent requests.

Get current user:

GET `http://e-commerce-training.test/api/user`

Header: `Authorization: Bearer <token>`

---

### Common Query Parameters (Index)

- `perPage` — integer, default 20
- `search` — string, applied to resource-defined searchable columns
- `sorts[field]=asc|desc` — can supply multiple fields
  - Example: `?sorts[created_at]=desc&sorts[name]=asc`
- `filters[field]=value` — resource-specific filters (see formations)

Example (products index):

GET `http://e-commerce-training.test/api/ecommerce/managements/products?perPage=20&search=shirt&sorts[created_at]=desc&filters[status]=active`

---

### Request/Response Envelope (Create/Update)

Content-Type: `application/json`

Body shape:

```json
{
  "data": {
    "attributes": {
      /* module fields here */
    }
  }
}
```

Responses for `store`/`update` return:

```json
{
  "data": {
    "attributes": { /* saved model attributes */ }
  }
}
``;

---

### Users

Endpoints:

- Index: GET `/api/ecommerce/managements/users`
- Show: GET `/api/ecommerce/managements/users/{id}`
- Create: POST `/api/ecommerce/managements/users`
- Update: PUT `/api/ecommerce/managements/users/{id}`
- Delete: DELETE `/api/ecommerce/managements/users/{id}`

Create body example:

```json
{
  "data": {
    "attributes": {
      "username": "jdoe",
      "name": "John Doe",
      "email": "john@example.com",
      "contact_number": "0123456789",
      "password": "password123",
      "role": "user",
      "permissions_categories": [191, 192],
      "permissions_products": [197, 198, 199],
      "permissions_orders": [],
      "permissions_items": [],
      "permissions_users": []
    }
  }
}
```

Notes:

- Permission arrays accept permission IDs from the `permissions` table (Spatie). Navigation visibility relies on `:read` permissions; `:show` is not used.

Update body example (fields you wish to change):

```json
{
  "data": {
    "attributes": {
      "name": "Johnathan Doe",
      "role": "admin",
      "permissions_products": [197, 199, 200]
    }
  }
}
```

---

### Products

Endpoints:

- Index: GET `/api/ecommerce/managements/products`
- Show: GET `/api/ecommerce/managements/products/{id}`
- Create: POST `/api/ecommerce/managements/products`
- Update: PUT `/api/ecommerce/managements/products/{id}`
- Delete: DELETE `/api/ecommerce/managements/products/{id}`

Create body example:

```json
{
  "data": {
    "attributes": {
      "name": "Basic Tee",
      "slug": "basic-tee",
      "sku": "TEE-0001",
      "category_id": 1,
      "status": "active",
      "description": "Comfortable cotton t-shirt",
      "price": 29.99,
      "stock": 100
    }
  }
}
```

Update body example:

```json
{
  "data": {
    "attributes": {
      "price": 27.99,
      "stock": 120,
      "status": "active"
    }
  }
}
```

---

### Categories

Endpoints:

- Index: GET `/api/ecommerce/managements/categories`
- Show: GET `/api/ecommerce/managements/categories/{id}`
- Create: POST `/api/ecommerce/managements/categories`
- Update: PUT `/api/ecommerce/managements/categories/{id}`
- Delete: DELETE `/api/ecommerce/managements/categories/{id}`

Typical fields (verify against your formation):

```json
{
  "data": {
    "attributes": {
      "name": "Accessories",
      "slug": "accessories"
    }
  }
}
```

---

### Orders & Items

Endpoints:

- Orders: `/api/ecommerce/managements/orders`
- Items: `/api/ecommerce/managements/items`

Bodies follow the same envelope. Use the formations for exact field names. Example skeleton for order create:

```json
{
  "data": {
    "attributes": {
      "user_id": 1,
      "status": "pending",
      "total": 99.90
    }
  }
}
```

---

### Errors & Validation

- Validation errors return standard Laravel validation responses (HTTP 422) keyed by attribute names used in `attributes`.
- Authorization is enforced via policies and Spatie roles/permissions. Ensure the bearer user has `:read`/`:create`/`:update`/`:delete` permissions as appropriate.

---

### Postman Setup

1. Create a collection with `{{base}} = http://e-commerce-training.test`.
2. Add a collection variable `token` and set a `Pre-request Script` to inject the bearer token, or set it manually:
   - Header: `Authorization: Bearer {{token}}`
3. Set request headers: `Accept: application/json`; `Content-Type: application/json` (POST/PUT).

---

### Notes

- Formation reads the allowed fields from each `{Module}Formation` file and ignores unknown attributes.
- File uploads (if added later) must be sent as multipart and mapped to the formation field names; current modules use JSON bodies only.
- Navigation visibility in the UI uses `:read` permissions; `:show` is deprecated/removed.


