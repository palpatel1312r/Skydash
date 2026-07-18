<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; // ✅ Import File facade
use Illuminate\Support\Facades\Storage; // ✅ Import Storage facade

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define the folder where your dummy images are located on your computer
        $sourceFolder = database_path('seeders/images');

        // 2. Define the destination folder inside storage (this will be linked to public/storage)
        $destinationFolder = 'uploads/products';

        // 3. Ensure the destination folder exists
        if (!Storage::exists($destinationFolder)) {
            Storage::makeDirectory($destinationFolder);
        }

        $products = [
            [
                'title' => 'Classic White Sneakers',
                // 'description' => 'Comfortable and stylish white sneakers for everyday wear.',
                'price' => 2500.00,
                'quantity' => 50,
                'category' => 'Shoes',
                'type' => 'Best Sellers',
                'image' => 'sneakers.jpg', // Filename inside your images folder
            ],
            [
                'title' => 'Wireless Bluetooth Headphones',
                // 'description' => 'High-quality sound with noise cancellation and 20-hour battery life.',
                'price' => 4000.00,
                'quantity' => 30,
                'category' => 'Electronics',
                'type' => 'New Arrivals',
                'image' => 'headphones.jpg',
            ],
            [
                'title' => 'Cotton T-Shirt (Pack of 3)',
                // 'description' => 'Soft and breathable cotton t-shirts. Available in multiple colors.',
                'price' => 900.00,
                'quantity' => 100,
                'category' => 'Clothes',
                'type' => 'Sale',
                'image' => 'tshirt.jpg',
            ],
            [
                'title' => 'Leather Wallet',
                // 'description' => 'Genuine leather wallet with multiple card slots and coin pocket.',
                'price' => 600.00,
                'quantity' => 75,
                'category' => 'Accessories',
                'type' => 'Featured',
                'image' => 'wallet.jpg',
            ],
            [
                'title' => 'Smart LED Desk Lamp',
                // 'description' => 'Adjustable brightness, touch control, and built-in USB charging port.',
                'price' => 1500.00,
                'quantity' => 40,
                'category' => 'Home',
                'type' => 'Best Sellers',
                'image' => 'lamp.jpg',
            ],
            [
                'title' => 'Running Shoes',
                // 'description' => 'Lightweight running shoes with cushioned sole for maximum comfort.',
                'price' => 3300.00,
                'quantity' => 60,
                'category' => 'Shoes',
                'type' => 'New Arrivals',
                'image' => 'running.jpg',
            ],
            [
                'title' => 'Laptop Backpack',
                // 'description' => 'Water-resistant backpack with padded laptop compartment.',
                'price' => 1200.00,
                'quantity' => 45,
                'category' => 'Accessories',
                'type' => 'Sale',
                'image' => 'backpack.jpg',
            ],
            [
                'title' => 'Smartphone Stand',
                // 'description' => 'Adjustable aluminum stand for smartphones and tablets.',
                'price' => 300.00,
                'quantity' => 200,
                'category' => 'Electronics',
                'type' => 'Featured',
                'image' => 'stand.jpg',
            ],
        ];

        foreach ($products as $productData) {
            // 4. Handle the image copying
            $imageName = $productData['image'];
            $sourcePath = $sourceFolder . '/' . $imageName;

            // If the source image exists in the seeder images folder
            if (File::exists($sourcePath)) {
                // Copy the file to the storage folder
                $newPath = Storage::putFile($destinationFolder, new \Illuminate\Http\File($sourcePath));
                $productData['image'] = basename($newPath); // Save only the filename to DB
            } else {
                $productData['image'] = null; // If file missing, leave as null
                $this->command->warn("⚠️ Image not found: {$imageName}. Skipping image.");
            }

            Product::create($productData);
        }

        $this->command->info('✅ ' . count($products) . ' Products seeded successfully!');
    }
}
