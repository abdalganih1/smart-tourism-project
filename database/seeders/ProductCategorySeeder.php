<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory; // Import the model

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent categories
        $textiles = ProductCategory::create(['name' => 'Textiles']);
        $pottery = ProductCategory::create(['name' => 'Pottery']);
        $jewelry = ProductCategory::create(['name' => 'Jewelry']);
        $woodwork = ProductCategory::create(['name' => 'Woodwork']);

        // Create some child categories
        ProductCategory::create(['name' => 'Embroidery', 'parent_category_id' => $textiles->category_id]);
        ProductCategory::create(['name' => 'Carpets', 'parent_category_id' => $textiles->category_id]);
        ProductCategory::create(['name' => 'Ceramic Art', 'parent_category_id' => $pottery->category_id]);
    }
}