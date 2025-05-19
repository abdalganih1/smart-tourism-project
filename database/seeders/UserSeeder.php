<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Hash; // Import Hash facade

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an Admin user
        User::create([
            'username' => 'admin',
            'email' => 'admin@app.com',
            'password' => Hash::make('password'), // Use password and Hash::make
            'user_type' => 'Admin',
            'is_active' => true,
        ]);

         // Create a Tourist user
        User::create([
            'username' => 'tourist1',
            'email' => 'tourist1@app.com',
            'password' => Hash::make('password'),
            'user_type' => 'Tourist',
            'is_active' => true,
        ]);

         // Create a Vendor user
        User::create([
            'username' => 'vendor1',
            'email' => 'vendor1@app.com',
            'password' => Hash::make('password'),
            'user_type' => 'Vendor',
            'is_active' => true,
        ]);

         // Create a HotelBookingManager user
        User::create([
            'username' => 'hotelmanager1',
            'email' => 'hotelmanager1@app.com',
            'password' => Hash::make('password'),
            'user_type' => 'HotelBookingManager',
            'is_active' => true,
        ]);

        // Create some random users using the User factory (if you set it up)
        // User::factory()->count(10)->create();
    }
}