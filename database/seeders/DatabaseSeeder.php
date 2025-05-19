<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductCategorySeeder::class,
            SiteCategorySeeder::class,
            // Add other seeders here in order of dependency (e.g., Categories before Products/Sites)
            TouristSiteSeeder::class,
            ProductSeeder::class,
            HotelSeeder::class,
            HotelRoomTypeSeeder::class,
            HotelRoomSeeder::class,
            // TouristActivitySeeder::class,
            // SiteExperienceSeeder::class,
            // ArticleSeeder::class,
            // Add seeders for polymorphic data (Favorites, Ratings, Comments) and orders if needed
            // FavoritesRatingsCommentsSeeder::class,
            // ProductOrderSeeder::class,
            // HotelBookingSeeder::class,
        ]);
    }
}