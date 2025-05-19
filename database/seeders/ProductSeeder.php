<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User; // To link products to a vendor
use App\Models\ProductCategory; // To link products to a category

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a vendor user (assuming UserSeeder ran)
        $vendor = User::where('user_type', 'Vendor')->first();

        // Get product categories (assuming ProductCategorySeeder ran)
        $textiles = ProductCategory::where('name', 'Textiles')->first();
        $embroidery = ProductCategory::where('name', 'Embroidery')->first();
         $pottery = ProductCategory::where('name', 'Pottery')->first();


        if ($vendor) {
            Product::create([
                'seller_user_id' => $vendor->id,
                'name' => 'وشاح حرير يدوي',
                'description' => 'وشاح مصنوع يدوياً من الحرير الطبيعي بتطريز دمشقي.',
                'stock_quantity' => 15,
                'price' => 5000.00,
                'main_image_url' => '/images/scarf.jpg',
                'category_id' => $embroidery ? $embroidery->category_id : ($textiles ? $textiles->category_id : null),
                'is_available' => true,
            ]);

             Product::create([
                'seller_user_id' => $vendor->id,
                'name' => 'صحن فخاري مزجج',
                'description' => 'صحن فخاري تقليدي مزجج بألوان زاهية.',
                'stock_quantity' => 8,
                'price' => 1200.00,
                'main_image_url' => '/images/pottery_plate.jpg',
                'category_id' => $pottery ? $pottery->category_id : null,
                'is_available' => true,
            ]);

            // Add more products...
        } else {
            $this->command->info('Vendor user not found. Skipping ProductSeeder.');
        }
    }
}