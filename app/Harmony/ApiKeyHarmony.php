<?php

namespace App\Harmony;

use Harmony\Collection;
use Harmony\Collection\Body;
use Harmony\Collection\Query;
use Harmony\Collection\Request;
use Harmony\Connector;
use Harmony\Connector\Authorization;
use Harmony\Connector\Header;
use Harmony\Harmony;

/**
 * ApiKeyHarmony - Example API with API Key Authentication
 * 
 * This class demonstrates how to implement API key authentication
 * using the Harmony package for APIs that require authentication.
 * 
 * @author Faiz Nasir
 */
class ApiKeyHarmony
{
    public static $baseUrl = 'https://api.example.com/v1/';
    public static $apiKey = 'the-api-key-here';
    public static $token = null; // Not used for API key auth

    /**
     * Configure connector with API key authentication
     * 
     * @return Connector
     */
    public static function connector(): Connector
    {
        return Harmony::createConnector(function (Connector $connector) {
            $connector
            ->authorization(function (Authorization $authorization) {
                $authorization->type('api_key'); // Custom type for API key
            })
            ->header(function (Header $header){
                $header->type('Content-Type', 'application/json');
                $header->type('Accept', 'application/json');
                $header->type('X-API-Key', self::$apiKey); // API key in header
                $header->type('User-Agent', 'Laravel-Harmony-App/1.0');
            });
        });
    }

    /**
     * Get user data with API key authentication
     * 
     * @param int $userId
     * @return Collection
     */
    public function getUser(int $userId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($userId) {
            $collection->group(function (Request $request) use ($userId) {
                $request->endpoint('users/' . $userId);
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('include');
                    $query->field('fields');
                });
            });
        });
    }

    /**
     * Create user with API key authentication
     * 
     * @return Collection
     */
    public function createUser(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('users');
                $request->method('POST');

                $request->body(function (Body $body) {
                    $body->field('name');
                    $body->field('email');
                    $body->field('phone');
                    $body->field('address');
                });
            });
        });
    }

    /**
     * Update user with API key authentication
     * 
     * @param int $userId
     * @return Collection
     */
    public function updateUser(int $userId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($userId) {
            $collection->group(function (Request $request) use ($userId) {
                $request->endpoint('users/' . $userId);
                $request->method('PUT');

                $request->body(function (Body $body) {
                    $body->field('name');
                    $body->field('email');
                    $body->field('phone');
                    $body->field('address');
                });
            });
        });
    }

    /**
     * Delete user with API key authentication
     * 
     * @param int $userId
     * @return Collection
     */
    public function deleteUser(int $userId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($userId) {
            $collection->group(function (Request $request) use ($userId) {
                $request->endpoint('users/' . $userId);
                $request->method('DELETE');
            });
        });
    }

    /**
     * Get products with API key authentication
     * 
     * @return Collection
     */
    public function getProducts(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('products');
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('page');
                    $query->field('limit');
                    $query->field('category');
                    $query->field('search');
                    $query->field('sort');
                });
            });
        });
    }

    /**
     * Get product by ID with API key authentication
     * 
     * @param int $productId
     * @return Collection
     */
    public function getProduct(int $productId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($productId) {
            $collection->group(function (Request $request) use ($productId) {
                $request->endpoint('products/' . $productId);
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('include');
                    $query->field('fields');
                });
            });
        });
    }

    /**
     * Create product with API key authentication
     * 
     * @return Collection
     */
    public function createProduct(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('products');
                $request->method('POST');

                $request->body(function (Body $body) {
                    $body->field('name');
                    $body->field('description');
                    $body->field('price');
                    $body->field('category');
                    $body->field('stock');
                    $body->field('is_active');
                });
            });
        });
    }

    /**
     * Update product with API key authentication
     * 
     * @param int $productId
     * @return Collection
     */
    public function updateProduct(int $productId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($productId) {
            $collection->group(function (Request $request) use ($productId) {
                $request->endpoint('products/' . $productId);
                $request->method('PUT');

                $request->body(function (Body $body) {
                    $body->field('name');
                    $body->field('description');
                    $body->field('price');
                    $body->field('category');
                    $body->field('stock');
                    $body->field('is_active');
                });
            });
        });
    }

    /**
     * Delete product with API key authentication
     * 
     * @param int $productId
     * @return Collection
     */
    public function deleteProduct(int $productId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($productId) {
            $collection->group(function (Request $request) use ($productId) {
                $request->endpoint('products/' . $productId);
                $request->method('DELETE');
            });
        });
    }

    /**
     * Get orders with API key authentication
     * 
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('orders');
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('page');
                    $query->field('limit');
                    $query->field('user_id');
                    $query->field('status');
                    $query->field('date_from');
                    $query->field('date_to');
                });
            });
        });
    }

    /**
     * Get order by ID with API key authentication
     * 
     * @param int $orderId
     * @return Collection
     */
    public function getOrder(int $orderId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($orderId) {
            $collection->group(function (Request $request) use ($orderId) {
                $request->endpoint('orders/' . $orderId);
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('include');
                    $query->field('fields');
                });
            });
        });
    }

    /**
     * Create order with API key authentication
     * 
     * @return Collection
     */
    public function createOrder(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('orders');
                $request->method('POST');

                $request->body(function (Body $body) {
                    $body->field('user_id');
                    $body->field('product_id');
                    $body->field('quantity');
                    $body->field('status');
                    $body->field('notes');
                });
            });
        });
    }

    /**
     * Update order with API key authentication
     * 
     * @param int $orderId
     * @return Collection
     */
    public function updateOrder(int $orderId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($orderId) {
            $collection->group(function (Request $request) use ($orderId) {
                $request->endpoint('orders/' . $orderId);
                $request->method('PUT');

                $request->body(function (Body $body) {
                    $body->field('user_id');
                    $body->field('product_id');
                    $body->field('quantity');
                    $body->field('status');
                    $body->field('notes');
                });
            });
        });
    }

    /**
     * Delete order with API key authentication
     * 
     * @param int $orderId
     * @return Collection
     */
    public function deleteOrder(int $orderId): Collection
    {
        return Harmony::createCollection(function (Collection $collection) use ($orderId) {
            $collection->group(function (Request $request) use ($orderId) {
                $request->endpoint('orders/' . $orderId);
                $request->method('DELETE');
            });
        });
    }

    /**
     * Search with API key authentication
     * 
     * @return Collection
     */
    public function search(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('search');
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('q');
                    $query->field('type');
                    $query->field('page');
                    $query->field('limit');
                    $query->field('filters');
                });
            });
        });
    }

    /**
     * Get API statistics with API key authentication
     * 
     * @return Collection
     */
    public function getStats(): Collection
    {
        return Harmony::createCollection(function (Collection $collection) {
            $collection->group(function (Request $request) {
                $request->endpoint('stats');
                $request->method('GET');

                $request->query(function (Query $query) {
                    $query->field('period');
                    $query->field('type');
                });
            });
        });
    }
}
