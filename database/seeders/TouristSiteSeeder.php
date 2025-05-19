<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TouristSite;
use App\Models\SiteCategory; // Need categories to assign

class TouristSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure Site Categories exist first
        $historical = SiteCategory::where('name', 'Historical')->first() ?? SiteCategory::create(['name' => 'Historical']);
        $natural = SiteCategory::where('name', 'Natural')->first() ?? SiteCategory::create(['name' => 'Natural']);
         $cultural = SiteCategory::where('name', 'Cultural')->first() ?? SiteCategory::create(['name' => 'Cultural']);

        TouristSite::create([
            'name' => 'قلعة حلب',
            'description' => 'قلعة أثرية تاريخية في قلب مدينة حلب القديمة.',
            'location_text' => 'حلب، سوريا',
            'latitude' => 36.2007, // Example coordinates
            'longitude' => 36.1629,
            'city' => 'حلب',
            'category_id' => $historical->category_id,
            'main_image_url' => '/images/aleppo_castle.jpg', // Example path
        ]);

         TouristSite::create([
            'name' => 'تدمر',
            'description' => 'مدينة أثرية سورية قديمة تقع في البادية السورية.',
            'location_text' => 'تدمر، سوريا',
             'latitude' => 34.5606,
             'longitude' => 38.2725,
            'city' => 'تدمر',
            'category_id' => $historical->category_id,
            'main_image_url' => '/images/palmyra.jpg',
        ]);

         TouristSite::create([
            'name' => 'شلالات الزاوي',
            'description' => 'شلالات طبيعية جميلة في ريف اللاذقية.',
            'location_text' => 'اللاذقية، ريف اللاذقية',
             'latitude' => null, // Example without coordinates
             'longitude' => null,
            'city' => 'اللاذقية',
            'category_id' => $natural->category_id,
            'main_image_url' => '/images/zawyi_waterfalls.jpg',
        ]);

         TouristSite::create([
            'name' => 'المتحف الوطني بدمشق',
            'description' => 'أكبر المتاحف السورية ويضم آثاراً تعود لعصور مختلفة.',
            'location_text' => 'دمشق، شارع الشعلان',
             'latitude' => 33.5127,
             'longitude' => 36.2920,
            'city' => 'دمشق',
            'category_id' => $cultural->category_id,
            'main_image_url' => '/images/damascus_museum.jpg',
        ]);
    }
}