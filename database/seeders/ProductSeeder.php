<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample category if it doesn't exist
        $category = ProductCategory::firstOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'parent_id' => null,
                'created_by' => 1,
            ]
        );

        // Create sample products
        $products = [
            [
                'category_id' => $category->id,
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'sku' => 'IPH15P-001',
                'description' => 'Latest iPhone with advanced camera system',
                'price' => 999.99,
                'stock' => 50,
                'is_active' => true,
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'category_id' => $category->id,
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'sku' => 'SGS24-001',
                'description' => 'Premium Android smartphone',
                'price' => 899.99,
                'stock' => 30,
                'is_active' => true,
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'category_id' => $category->id,
                'name' => 'MacBook Pro 16"',
                'slug' => 'macbook-pro-16',
                'sku' => 'MBP16-001',
                'description' => 'Professional laptop for developers',
                'price' => 2499.99,
                'stock' => 20,
                'is_active' => true,
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'category_id' => $category->id,
                'name' => 'Dell XPS 13',
                'slug' => 'dell-xps-13',
                'sku' => 'DXPS13-001',
                'description' => 'Ultrabook with excellent performance',
                'price' => 1299.99,
                'stock' => 25,
                'is_active' => true,
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'category_id' => $category->id,
                'name' => 'AirPods Pro',
                'slug' => 'airpods-pro',
                'sku' => 'APP-001',
                'description' => 'Wireless earbuds with noise cancellation',
                'price' => 249.99,
                'stock' => 100,
                'is_active' => true,
                'status' => 'active',
                'created_by' => 1,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }
    }
}
