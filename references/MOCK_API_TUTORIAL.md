# Mock API Tutorial - Complete Guide

## 🏗️ **How My Mock API Works** 

### FAIZ NASIR

#### **1. Data Storage - In-Memory Arrays**

```php
// In MockDataController.php
private static $mockData = [
    'users' => [
        1 => [
            'id' => 1,
            'name' => 'Ahmad Rahman',
            'email' => 'ahmad@example.com',
            'phone' => '+60123456789',
            'address' => '123 Jalan Kuchai Lama, Kuala Lumpur',
            'created_at' => '2025-09-01T10:00:00Z',
            'updated_at' => '2025-09-01T10:00:00Z'
        ],
        // ... more users
    ],
    'products' => [
        1 => [
            'id' => 1,
            'name' => 'Samsung Galaxy S24',
            'description' => 'Latest Samsung smartphone',
            'price' => 3999.00,
            'category' => 'Electronics',
            'stock' => 50,
            'is_active' => true,
            'created_at' => '2025-09-01T10:00:00Z',
            'updated_at' => '2025-09-01T10:00:00Z'
        ],
        // ... more products
    ],
    'orders' => [
        // ... orders data
    ]
];

// Auto-increment IDs
private static $nextId = [
    'users' => 4,
    'products' => 4,
    'orders' => 3
];
```

### **2. CRUD Operations Explained**

#### **CREATE (POST) - Add New Data**

```php
public function createUser(Request $request): JsonResponse
{
    try {
        // 1. Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        // 2. Generate new ID
        $id = self::$nextId['users']++;
        
        // 3. Create new user data
        $user = [
            'id' => $id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ];

        // 4. Store in memory
        self::$mockData['users'][$id] = $user;

        // 5. Return success response
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
```

#### **READ (GET) - Retrieve Data**

```php
// Get all users
public function getUsers(): JsonResponse
{
    $users = array_values(self::$mockData['users']);
    
    return response()->json([
        'success' => true,
        'data' => $users,
        'pagination' => [
            'current_page' => 1,
            'per_page' => 10,
            'total' => count($users),
            'last_page' => 1
        ]
    ]);
}

// Get single user
public function getUser(int $id): JsonResponse
{
    if (!isset(self::$mockData['users'][$id])) {
        return response()->json([
            'success' => false,
            'error' => 'User not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => self::$mockData['users'][$id]
    ]);
}
```

#### **UPDATE (PUT) - Modify Data**

```php
public function updateUser(Request $request, int $id): JsonResponse
{
    try {
        // 1. Check if user exists
        if (!isset(self::$mockData['users'][$id])) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        }

        // 2. Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        // 3. Update user data
        $user = self::$mockData['users'][$id];
        $user['updated_at'] = now()->toISOString();
        
        // Update only provided fields
        if ($request->has('name')) $user['name'] = $request->input('name');
        if ($request->has('email')) $user['email'] = $request->input('email');
        if ($request->has('phone')) $user['phone'] = $request->input('phone');
        if ($request->has('address')) $user['address'] = $request->input('address');

        // 4. Save updated data
        self::$mockData['users'][$id] = $user;

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
```

#### **DELETE - Remove Data**

```php
public function deleteUser(int $id): JsonResponse
{
    try {
        // 1. Check if user exists
        if (!isset(self::$mockData['users'][$id])) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        }

        // 2. Remove user
        unset(self::$mockData['users'][$id]);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
```

### **3. API Routes - Connect URLs to Methods**

```php
// In routes/api.php
use App\Http\Controllers\MockDataController;

// Users CRUD
Route::get('/users', [MockDataController::class, 'getUsers']);
Route::get('/users/{id}', [MockDataController::class, 'getUser']);
Route::post('/users', [MockDataController::class, 'createUser']);
Route::put('/users/{id}', [MockDataController::class, 'updateUser']);
Route::delete('/users/{id}', [MockDataController::class, 'deleteUser']);

// Products CRUD
Route::get('/products', [MockDataController::class, 'getProducts']);
Route::get('/products/{id}', [MockDataController::class, 'getProduct']);
Route::post('/products', [MockDataController::class, 'createProduct']);
Route::put('/products/{id}', [MockDataController::class, 'updateProduct']);
Route::delete('/products/{id}', [MockDataController::class, 'deleteProduct']);
```

## 🔄 **Data Flow - How It Works**

```
1. Client Request → 2. Route → 3. Controller Method → 4. Mock Data → 5. Response
   POST /api/users  →  Route::post  →  createUser()  →  $mockData  →  JSON Response
```

## 📊 **Where Data is Stored**

### **In-Memory Storage:**
- **Location**: PHP memory (RAM)
- **Persistence**: Only during server runtime
- **Reset**: Data lost when server restarts
- **File**: `app/Http/Controllers/MockDataController.php`

### **Data Structure:**
```php
private static $mockData = [
    'users' => [1 => [...], 2 => [...], 3 => [...]],
    'products' => [1 => [...], 2 => [...], 3 => [...]],
    'orders' => [1 => [...], 2 => [...]]
];
```

## 🚀 **How to Test the API**

### **Using cURL:**
```bash
# Get all users
curl -X GET "http://localhost:8000/api/users"

# Create new user
curl -X POST "http://localhost:8000/api/users" \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","phone":"+60123456789","address":"123 Main St"}'

# Update user
curl -X PUT "http://localhost:8000/api/users/1" \
  -H "Content-Type: application/json" \
  -d '{"name":"John Updated"}'

# Delete user
curl -X DELETE "http://localhost:8000/api/users/1"
```

## 🔧 **How to Add New Data Types**

### **Step 1: Add to Mock Data**
```php
private static $mockData = [
    'users' => [...],
    'products' => [...],
    'orders' => [...],
    'categories' => [  // NEW!
        1 => [
            'id' => 1,
            'name' => 'Electronics',
            'description' => 'Electronic devices',
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString()
        ]
    ]
];

private static $nextId = [
    'users' => 4,
    'products' => 4,
    'orders' => 3,
    'categories' => 2  // NEW!
];
```

### **Step 2: Add CRUD Methods**
```php
public function getCategories(): JsonResponse { ... }
public function getCategory(int $id): JsonResponse { ... }
public function createCategory(Request $request): JsonResponse { ... }
public function updateCategory(Request $request, int $id): JsonResponse { ... }
public function deleteCategory(int $id): JsonResponse { ... }
```

### **Step 3: Add Routes**
```php
// Categories CRUD
Route::get('/categories', [MockDataController::class, 'getCategories']);
Route::get('/categories/{id}', [MockDataController::class, 'getCategory']);
Route::post('/categories', [MockDataController::class, 'createCategory']);
Route::put('/categories/{id}', [MockDataController::class, 'updateCategory']);
Route::delete('/categories/{id}', [MockDataController::class, 'deleteCategory']);
```

## 💡 **Key Benefits of Mock API**

1. **No Database Required** - Perfect for testing
2. **Fast Development** - No schema setup needed
3. **Easy to Understand** - Simple array operations
4. **Realistic Responses** - JSON format like real APIs
5. **Validation Included** - Input validation and error handling
6. **Complete CRUD** - All operations supported

## ⚠️ **Limitations**

1. **Data Not Persistent** - Lost on server restart
2. **Single Server Only** - Not shared between servers
3. **Memory Limited** - Large datasets may cause issues
4. **No Relationships** - Basic foreign key simulation only

## 🎯 **When to Use Mock APIs**

✅ **Perfect for:**
- Frontend development and testing
- API prototyping
- Learning and demonstrations
- Quick proof of concepts

❌ **Not suitable for:**
- Production applications
- Large datasets
- Data persistence requirements
- Multi-user applications

This mock API system gives you a complete, working API without any database setup! 🚀
