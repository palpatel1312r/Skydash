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

        $sourceFolder = database_path('seeders/images');

        $destinationFolder = 'uploads/products';

        if (!Storage::exists($destinationFolder)) {
            Storage::makeDirectory($destinationFolder);
        }

        $products = [
            [
                'title' => 'Classic White Sneakers',
                'price' => 2500.00,
                'quantity' => 50,
                'category' => 'Shoes',
                'type' => 'Best Sellers',
                'image' => 'null',
            ],
            [
                'title' => 'Wireless Bluetooth Headphones',
                'price' => 4000.00,
                'quantity' => 30,
                'category' => 'Electronics',
                'type' => 'New Arrivals',
                'image' => 'null',
            ],
            [
                'title' => 'Cotton T-Shirt (Pack of 3)',
                'price' => 900.00,
                'quantity' => 100,
                'category' => 'Clothes',
                'type' => 'Sale',
                'image' => 'null',
            ],
            [
                'title' => 'Leather Wallet',
                'price' => 600.00,
                'quantity' => 75,
                'category' => 'Accessories',
                'type' => 'Featured',
                'image' => 'null',
            ],
            [
                'title' => 'Smart LED Desk Lamp',
                'price' => 1500.00,
                'quantity' => 40,
                'category' => 'Home',
                'type' => 'Best Sellers',
                'image' => 'null',
            ],
            [
                'title' => 'Running Shoes',
                'price' => 3300.00,
                'quantity' => 60,
                'category' => 'Shoes',
                'type' => 'New Arrivals',
                'image' => 'null',
            ],
            [
                'title' => 'Laptop Backpack',
                'price' => 1200.00,
                'quantity' => 45,
                'category' => 'Accessories',
                'type' => 'Sale',
                'image' => 'null',
            ],
            [
                'title' => 'Smartphone Stand',
                'price' => 300.00,
                'quantity' => 200,
                'category' => 'Electronics',
                'type' => 'Featured',
                'image' => 'null',
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
