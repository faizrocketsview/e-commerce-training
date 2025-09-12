<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

/**
 * MockDataController - CRUD Test API with Mock Data
 * 
 * This controller provides a complete CRUD API using mock data
 * to demonstrate Harmony package integration with various data types.
 * 
 * @author Faiz Nasir
 */
class MockDataController extends Controller
{
    private const STORAGE_PATH = 'mock_data.json';
    private static $loaded = false;
    /**
     * Mock data storage (in-memory for demonstration)
     */
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
            2 => [
                'id' => 2,
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'phone' => '+60123456790',
                'address' => '456 Jalan Puchong, Selangor',
                'created_at' => '2025-09-02T11:00:00Z',
                'updated_at' => '2025-09-02T11:00:00Z'
            ],
            3 => [
                'id' => 3,
                'name' => 'Muhammad Ali',
                'email' => 'ali@example.com',
                'phone' => '+60123456791',
                'address' => '789 Jalan Ampang, Kuala Lumpur',
                'created_at' => '2025-09-03T12:00:00Z',
                'updated_at' => '2025-09-03T12:00:00Z'
            ]
        ],
        'products' => [
            1 => [
                'id' => 1,
                'name' => 'Samsung Galaxy S24',
                'description' => 'Latest Samsung smartphone with advanced features',
                'price' => 3999.00,
                'category' => 'Electronics',
                'stock' => 50,
                'is_active' => true,
                'created_at' => '2025-09-01T10:00:00Z',
                'updated_at' => '2025-09-01T10:00:00Z'
            ],
            2 => [
                'id' => 2,
                'name' => 'MacBook Pro M3',
                'description' => 'Apple MacBook Pro with M3 chip',
                'price' => 8999.00,
                'category' => 'Electronics',
                'stock' => 25,
                'is_active' => true,
                'created_at' => '2025-09-02T11:00:00Z',
                'updated_at' => '2025-09-02T11:00:00Z'
            ],
            3 => [
                'id' => 3,
                'name' => 'Nike Air Max 270',
                'description' => 'Comfortable running shoes',
                'price' => 599.00,
                'category' => 'Fashion',
                'stock' => 100,
                'is_active' => true,
                'created_at' => '2025-09-03T12:00:00Z',
                'updated_at' => '2025-09-03T12:00:00Z'
            ]
        ],
        'orders' => [
            1 => [
                'id' => 1,
                'user_id' => 1,
                'product_id' => 1,
                'quantity' => 2,
                'total_amount' => 7998.00,
                'status' => 'pending',
                'order_date' => '2025-09-10T14:30:00Z',
                'created_at' => '2025-09-10T14:30:00Z',
                'updated_at' => '2025-09-10T14:30:00Z'
            ],
            2 => [
                'id' => 2,
                'user_id' => 2,
                'product_id' => 2,
                'quantity' => 1,
                'total_amount' => 8999.00,
                'status' => 'completed',
                'order_date' => '2025-09-11T09:15:00Z',
                'created_at' => '2025-09-11T09:15:00Z',
                'updated_at' => '2025-09-11T09:15:00Z'
            ]
        ]
    ];

    private static $nextId = [
        'users' => 4,
        'products' => 4,
        'orders' => 3
    ];

    /**
     * Get all users
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            self::ensureLoaded();
            $users = array_values(self::$mockData['users']);
            if ($request->filled('search')) {
                $search = strtolower($request->string('search'));
                $users = array_values(array_filter($users, fn($u) => str_contains(strtolower($u['name']), $search) || str_contains(strtolower($u['email']), $search)));
            }
            $paginated = $this->paginateArray($users, $request);
            return $this->ok($paginated);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Get user by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getUser(int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['users'][$id])) {
                return $this->fail('User not found', 404);
            }
            return $this->ok(['data' => self::$mockData['users'][$id]]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Create new user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
        try {
            self::ensureLoaded();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500'
            ]);

            if ($validator->fails()) return $this->fail('Validation failed', 422, ['details' => $validator->errors()]);

            $id = self::$nextId['users']++;
            $user = [
                'id' => $id,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];

            self::$mockData['users'][$id] = $user;
            self::save();

            return $this->ok(['message' => 'User created successfully', 'data' => $user], 201);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Update user
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateUser(Request $request, int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['users'][$id])) return $this->fail('User not found', 404);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:500'
            ]);

            if ($validator->fails()) return $this->fail('Validation failed', 422, ['details' => $validator->errors()]);

            $user = self::$mockData['users'][$id];
            $user['updated_at'] = now()->toISOString();

            // Update only provided fields
            if ($request->has('name')) $user['name'] = $request->input('name');
            if ($request->has('email')) $user['email'] = $request->input('email');
            if ($request->has('phone')) $user['phone'] = $request->input('phone');
            if ($request->has('address')) $user['address'] = $request->input('address');

            self::$mockData['users'][$id] = $user;
            self::save();

            return $this->ok(['message' => 'User updated successfully', 'data' => $user]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Delete user
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function deleteUser(int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['users'][$id])) return $this->fail('User not found', 404);

            unset(self::$mockData['users'][$id]);
            self::save();

            return $this->ok(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Get all products
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getProducts(Request $request): JsonResponse
    {
        try {
            self::ensureLoaded();
            $products = array_values(self::$mockData['products']);
            if ($request->filled('category')) {
                $category = $request->string('category');
                $products = array_values(array_filter($products, fn($p) => $p['category'] === $category));
            }
            if ($request->filled('search')) {
                $search = strtolower($request->string('search'));
                $products = array_values(array_filter($products, fn($p) => str_contains(strtolower($p['name']), $search) || str_contains(strtolower($p['description']), $search)));
            }
            $paginated = $this->paginateArray($products, $request);
            return $this->ok($paginated);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Get product by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getProduct(int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['products'][$id])) return $this->fail('Product not found', 404);
            return $this->ok(['data' => self::$mockData['products'][$id]]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Create new product
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function createProduct(Request $request): JsonResponse
    {
        try {
            self::ensureLoaded();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
                'stock' => 'required|integer|min:0',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) return $this->fail('Validation failed', 422, ['details' => $validator->errors()]);

            $id = self::$nextId['products']++;
            $product = [
                'id' => $id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'category' => $request->input('category'),
                'stock' => $request->input('stock'),
                'is_active' => $request->input('is_active', true),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];

            self::$mockData['products'][$id] = $product;
            self::save();

            return $this->ok(['message' => 'Product created successfully', 'data' => $product], 201);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Update product
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['products'][$id])) return $this->fail('Product not found', 404);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'price' => 'sometimes|numeric|min:0',
                'category' => 'sometimes|string|max:100',
                'stock' => 'sometimes|integer|min:0',
                'is_active' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) return $this->fail('Validation failed', 422, ['details' => $validator->errors()]);

            $product = self::$mockData['products'][$id];
            $product['updated_at'] = now()->toISOString();

            // Update only provided fields
            if ($request->has('name')) $product['name'] = $request->input('name');
            if ($request->has('description')) $product['description'] = $request->input('description');
            if ($request->has('price')) $product['price'] = $request->input('price');
            if ($request->has('category')) $product['category'] = $request->input('category');
            if ($request->has('stock')) $product['stock'] = $request->input('stock');
            if ($request->has('is_active')) $product['is_active'] = $request->input('is_active');

            self::$mockData['products'][$id] = $product;
            self::save();

            return $this->ok(['message' => 'Product updated successfully', 'data' => $product]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Delete product
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function deleteProduct(int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['products'][$id])) return $this->fail('Product not found', 404);

            unset(self::$mockData['products'][$id]);
            self::save();

            return $this->ok(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Get all orders
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            self::ensureLoaded();
            $orders = array_values(self::$mockData['orders']);
            if ($request->filled('user_id')) {
                $userId = (int) $request->input('user_id');
                $orders = array_values(array_filter($orders, fn($o) => $o['user_id'] === $userId));
            }
            if ($request->filled('status')) {
                $status = $request->string('status');
                $orders = array_values(array_filter($orders, fn($o) => $o['status'] === $status));
            }
            $paginated = $this->paginateArray($orders, $request);
            return $this->ok($paginated);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Get order by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function getOrder(int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['orders'][$id])) return $this->fail('Order not found', 404);
            return $this->ok(['data' => self::$mockData['orders'][$id]]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Create new order
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function createOrder(Request $request): JsonResponse
    {
        try {
            self::ensureLoaded();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'status' => 'string|in:pending,processing,shipped,delivered,cancelled'
            ]);

            if ($validator->fails()) return $this->fail('Validation failed', 422, ['details' => $validator->errors()]);

            // Check if user and product exist
            if (!isset(self::$mockData['users'][$request->input('user_id')])) return $this->fail('User not found', 404);

            if (!isset(self::$mockData['products'][$request->input('product_id')])) return $this->fail('Product not found', 404);

            $product = self::$mockData['products'][$request->input('product_id')];
            $quantity = $request->input('quantity');
            $totalAmount = $product['price'] * $quantity;

            $id = self::$nextId['orders']++;
            $order = [
                'id' => $id,
                'user_id' => $request->input('user_id'),
                'product_id' => $request->input('product_id'),
                'quantity' => $quantity,
                'total_amount' => $totalAmount,
                'status' => $request->input('status', 'pending'),
                'order_date' => now()->toISOString(),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];

            self::$mockData['orders'][$id] = $order;
            self::save();

            return $this->ok(['message' => 'Order created successfully', 'data' => $order], 201);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Update order
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateOrder(Request $request, int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['orders'][$id])) return $this->fail('Order not found', 404);

            $validator = Validator::make($request->all(), [
                'user_id' => 'sometimes|integer|exists:users,id',
                'product_id' => 'sometimes|integer|exists:products,id',
                'quantity' => 'sometimes|integer|min:1',
                'status' => 'sometimes|string|in:pending,processing,shipped,delivered,cancelled'
            ]);

            if ($validator->fails()) return $this->fail('Validation failed', 422, ['details' => $validator->errors()]);

            $order = self::$mockData['orders'][$id];
            $order['updated_at'] = now()->toISOString();

            // Update only provided fields
            if ($request->has('user_id')) $order['user_id'] = $request->input('user_id');
            if ($request->has('product_id')) $order['product_id'] = $request->input('product_id');
            if ($request->has('quantity')) $order['quantity'] = $request->input('quantity');
            if ($request->has('status')) $order['status'] = $request->input('status');

            // Recalculate total amount if quantity or product changed
            if ($request->has('quantity') || $request->has('product_id')) {
                $productId = $request->has('product_id') ? $request->input('product_id') : $order['product_id'];
                $quantity = $request->has('quantity') ? $request->input('quantity') : $order['quantity'];
                $product = self::$mockData['products'][$productId];
                $order['total_amount'] = $product['price'] * $quantity;
            }

            self::$mockData['orders'][$id] = $order;
            self::save();

            return $this->ok(['message' => 'Order updated successfully', 'data' => $order]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Delete order
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function deleteOrder(int $id): JsonResponse
    {
        try {
            self::ensureLoaded();
            if (!isset(self::$mockData['orders'][$id])) return $this->fail('Order not found', 404);

            unset(self::$mockData['orders'][$id]);
            self::save();

            return $this->ok(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Get API statistics
     * 
     * @return JsonResponse
     */
    public function getStats(): JsonResponse
    {
        try {
            self::ensureLoaded();
            $stats = [
                'users' => count(self::$mockData['users']),
                'products' => count(self::$mockData['products']),
                'orders' => count(self::$mockData['orders']),
                'total_revenue' => array_sum(array_column(self::$mockData['orders'], 'total_amount')),
                'active_products' => count(array_filter(self::$mockData['products'], function($product) {
                    return $product['is_active'];
                })),
                'pending_orders' => count(array_filter(self::$mockData['orders'], function($order) {
                    return $order['status'] === 'pending';
                }))
            ];

            return $this->ok(['data' => $stats]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Reset all mock data
     * 
     * @return JsonResponse
     */
    public function resetData(): JsonResponse
    {
        try {
            self::ensureLoaded();
            // Reset to initial state
            self::$mockData = [
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
                    2 => [
                        'id' => 2,
                        'name' => 'Siti Nurhaliza',
                        'email' => 'siti@example.com',
                        'phone' => '+60123456790',
                        'address' => '456 Jalan Puchong, Selangor',
                        'created_at' => '2025-09-02T11:00:00Z',
                        'updated_at' => '2025-09-02T11:00:00Z'
                    ],
                    3 => [
                        'id' => 3,
                        'name' => 'Muhammad Ali',
                        'email' => 'ali@example.com',
                        'phone' => '+60123456791',
                        'address' => '789 Jalan Ampang, Kuala Lumpur',
                        'created_at' => '2025-09-03T12:00:00Z',
                        'updated_at' => '2025-09-03T12:00:00Z'
                    ]
                ],
                'products' => [
                    1 => [
                        'id' => 1,
                        'name' => 'Samsung Galaxy S24',
                        'description' => 'Latest Samsung smartphone with advanced features',
                        'price' => 3999.00,
                        'category' => 'Electronics',
                        'stock' => 50,
                        'is_active' => true,
                        'created_at' => '2025-09-01T10:00:00Z',
                        'updated_at' => '2025-09-01T10:00:00Z'
                    ],
                    2 => [
                        'id' => 2,
                        'name' => 'MacBook Pro M3',
                        'description' => 'Apple MacBook Pro with M3 chip',
                        'price' => 8999.00,
                        'category' => 'Electronics',
                        'stock' => 25,
                        'is_active' => true,
                        'created_at' => '2025-09-02T11:00:00Z',
                        'updated_at' => '2025-09-02T11:00:00Z'
                    ],
                    3 => [
                        'id' => 3,
                        'name' => 'Nike Air Max 270',
                        'description' => 'Comfortable running shoes',
                        'price' => 599.00,
                        'category' => 'Fashion',
                        'stock' => 100,
                        'is_active' => true,
                        'created_at' => '2025-09-03T12:00:00Z',
                        'updated_at' => '2025-09-03T12:00:00Z'
                    ]
                ],
                'orders' => [
                    1 => [
                        'id' => 1,
                        'user_id' => 1,
                        'product_id' => 1,
                        'quantity' => 2,
                        'total_amount' => 7998.00,
                        'status' => 'pending',
                        'order_date' => '2025-09-10T14:30:00Z',
                        'created_at' => '2025-09-10T14:30:00Z',
                        'updated_at' => '2025-09-10T14:30:00Z'
                    ],
                    2 => [
                        'id' => 2,
                        'user_id' => 2,
                        'product_id' => 2,
                        'quantity' => 1,
                        'total_amount' => 8999.00,
                        'status' => 'completed',
                        'order_date' => '2025-09-11T09:15:00Z',
                        'created_at' => '2025-09-11T09:15:00Z',
                        'updated_at' => '2025-09-11T09:15:00Z'
                    ]
                ]
            ];

            self::$nextId = [
                'users' => 4,
                'products' => 4,
                'orders' => 3
            ];

            self::save();
            return $this->ok(['message' => 'Mock data reset successfully']);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 500);
        }
    }

    /**
     * Ensure in-memory store is loaded from JSON once per request lifecycle
     */
    private static function ensureLoaded(): void
    {
        if (self::$loaded) return;
        try {
            if (Storage::exists(self::STORAGE_PATH)) {
                $raw = Storage::get(self::STORAGE_PATH);
                $decoded = json_decode($raw, true);
                if (is_array($decoded) && isset($decoded['data']) && isset($decoded['nextId'])) {
                    self::$mockData = $decoded['data'];
                    self::$nextId = $decoded['nextId'];
                }
            } else {
                // write initial snapshot to disk
                self::save();
            }
        } catch (\Throwable $e) {
            // swallow; operate in-memory
        }
        self::$loaded = true;
    }

    /**
     * Persist in-memory store to JSON file
     */
    private static function save(): void
    {
        try {
            $payload = [
                'data' => self::$mockData,
                'nextId' => self::$nextId,
                'saved_at' => now()->toISOString(),
            ];
            Storage::put(self::STORAGE_PATH, json_encode($payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            // ignore disk errors for mock controller
        }
    }

    /**
     * Build a consistent success JSON response
     */
    private function ok(array $payload = [], int $status = 200): JsonResponse
    {
        return response()->json(array_merge(['success' => true], $payload), $status);
    }

    /**
     * Build a consistent error JSON response
     */
    private function fail(string $error, int $status = 400, array $extra = []): JsonResponse
    {
        return response()->json(array_merge(['success' => false, 'error' => $error], $extra), $status);
    }

    /**
     * Simple array paginator compatible with the controller response format
     */
    private function paginateArray(array $items, Request $request): array
    {
        $page = max(1, (int) $request->input('page', 1));
        $perPage = max(1, (int) $request->input('per_page', 10));
        $total = count($items);
        $offset = ($page - 1) * $perPage;
        $data = array_slice($items, $offset, $perPage);

        return [
            'data' => array_values($data),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage)
            ]
        ];
    }
}
