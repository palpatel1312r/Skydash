<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'title' => 'Classic White Sneakers',
                'description' => 'Comfortable and stylish white sneakers for everyday wear.',
                'price' => 2499.00,
                'quantity' => 50,
                'category' => 'Shoes',
                'type' => 'Best Sellers',
                'image' => null,
            ],
            [
                'title' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality sound with noise cancellation and 20-hour battery life.',
                'price' => 3999.00,
                'quantity' => 30,
                'category' => 'Electronics',
                'type' => 'New Arrivals',
                'image' => null,
            ],
            [
                'title' => 'Cotton T-Shirt (Pack of 3)',
                'description' => 'Soft and breathable cotton t-shirts. Available in multiple colors.',
                'price' => 899.00,
                'quantity' => 100,
                'category' => 'Clothes',
                'type' => 'Sale',
                'image' => null,
            ],
            [
                'title' => 'Leather Wallet',
                'description' => 'Genuine leather wallet with multiple card slots and coin pocket.',
                'price' => 599.00,
                'quantity' => 75,
                'category' => 'Accessories',
                'type' => 'Featured',
                'image' => null,
            ],
            [
                'title' => 'Smart LED Desk Lamp',
                'description' => 'Adjustable brightness, touch control, and built-in USB charging port.',
                'price' => 1499.00,
                'quantity' => 40,
                'category' => 'Home',
                'type' => 'Best Sellers',
                'image' => null,
            ],
            [
                'title' => 'Running Shoes',
                'description' => 'Lightweight running shoes with cushioned sole for maximum comfort.',
                'price' => 3299.00,
                'quantity' => 60,
                'category' => 'Shoes',
                'type' => 'New Arrivals',
                'image' => null,
            ],
            [
                'title' => 'Laptop Backpack',
                'description' => 'Water-resistant backpack with padded laptop compartment.',
                'price' => 1299.00,
                'quantity' => 45,
                'category' => 'Accessories',
                'type' => 'Sale',
                'image' => null,
            ],
            [
                'title' => 'Smartphone Stand',
                'description' => 'Adjustable aluminum stand for smartphones and tablets.',
                'price' => 299.00,
                'quantity' => 200,
                'category' => 'Electronics',
                'type' => 'Featured',
                'image' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ ' . count($products) . ' Products seeded successfully!');
    }
}
