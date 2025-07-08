<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin; // Import Admin controllers namespace
use App\Http\Controllers\HotelManager; // Import HotelManager controllers namespace
use App\Http\Controllers\Vendor; // Import Vendor controllers namespace
use App\Http\Controllers\ProfileController; // Default Breeze/Jetstream profile controller
use App\Http\Controllers\Auth\AuthenticatedSessionController; // Import if using this controller for logout

use Illuminate\Support\Facades\App; // Import App Facade
use Illuminate\Support\Facades\Redirect; // Import Redirect Facade
use Illuminate\Support\Facades\URL; // Import URL Facade
use Illuminate\Support\Facades\Auth; // Import Auth Facade for check
use Illuminate\Support\Str; // ** استيراد Str Facade **


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route to set the locale
Route::get('/set-locale/{locale}', function ($locale) {
    // Validate the locale
    if (! in_array($locale, ['en', 'ar'])) { // List supported locales
        abort(400); // Invalid locale
    }

    // Set the application locale
    App::setLocale($locale);

    // Store the locale in the session (optional but recommended)
    session()->put('locale', $locale);

    // Redirect back to the previous page
    // Ensure the previous URL is not the set-locale route itself in a loop
    $previousUrl = Redirect::back()->getTargetUrl();
    $baseUrl = URL::to('/');

     // Prevent redirect loop if previous URL is the set-locale route
     // Use Str::contains correctly
    if (Str::contains($previousUrl, '/set-locale/')) {
         // Redirect to a default page (like dashboard or welcome)
         // Auth::check() needs Auth facade imported (which we did)
         return Redirect::to(Auth::check() ? route('dashboard') : '/'); // Redirect to user dashboard or welcome
    }

    return Redirect::back();

})->name('set-locale');


// Default welcome page
Route::view('/', 'welcome');

// Default dashboard and profile routes from Breeze (for regular users like Tourists)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'can:isTourist']) // Add can:isTourist check
    ->name('dashboard');

// The profile route created by Breeze Blade stack often uses a controller
Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // If using Breeze Blade defaults
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        // Default logout route provided by Breeze auth scaffolding
        Route::post('logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');

    // Your existing profile route using Volt
     Route::view('profile', 'profile')->name('profile'); // Keeping your Volt route
});


require __DIR__.'/auth.php'; // Includes Breeze/Volt auth routes (login, register, etc.)


// --- Admin Panel Routes ---
Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'can:access-admin-panel']) // Requires authentication and 'Admin' role
     ->group(function () {

    // Admin Dashboard
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Resource Management Routes
    Route::resource('users', Admin\UserController::class);
    Route::resource('product-categories', Admin\ProductCategoryController::class);
    Route::resource('products', Admin\ProductController::class);
    Route::resource('product-orders', Admin\ProductOrderController::class)->only(['index', 'show']);

    Route::resource('site-categories', Admin\SiteCategoryController::class);
    Route::resource('tourist-sites', Admin\TouristSiteController::class);
    Route::resource('tourist-activities', Admin\TouristActivityController::class);

    Route::resource('hotels', Admin\HotelController::class);
    Route::resource('hotel-room-types', Admin\HotelRoomTypeController::class);
    Route::resource('hotel-rooms', Admin\HotelRoomController::class);
    Route::resource('hotel-bookings', Admin\HotelBookingController::class)->only(['index', 'show']);

    Route::resource('site-experiences', Admin\SiteExperienceController::class);
    Route::resource('articles', Admin\ArticleController::class);

    // Add more admin-specific routes here as needed

});

// --- Hotel Booking Manager Panel Routes ---
Route::prefix('hotel-manager') // Or 'hotels' or 'hotel-bookings' as prefix
     ->name('hotelmanager.') // Name prefix for routes
     ->middleware(['auth', 'can:access-hotelmanager-panel']) // Requires auth and 'HotelBookingManager' role
     ->group(function () {

    // Hotel Manager Dashboard
    Route::get('/', [HotelManager\DashboardController::class, 'index'])->name('dashboard');

    // Resource Management Routes specific to Hotel Managers
    Route::resource('hotels', HotelManager\HotelController::class); // Manage assigned hotels
    Route::resource('hotel-rooms', HotelManager\HotelRoomController::class); // Manage rooms for assigned hotels
    Route::resource('hotel-bookings', HotelManager\HotelBookingController::class); // Manage bookings for assigned hotels

    // Add more hotel manager specific routes

});

// --- Vendor Panel Routes ---
Route::prefix('vendor')
     ->name('vendor.')
     ->middleware(['auth', 'can:access-vendor-panel']) // Requires auth and 'Vendor' role
     ->group(function () {

    // Vendor Dashboard
    Route::get('/', [Vendor\DashboardController::class, 'index'])->name('dashboard');

    // Resource Management Routes specific to Vendors
    Route::resource('products', Vendor\ProductController::class); // Manage their own products
    Route::resource('product-orders', Vendor\ProductOrderController::class)->only(['index', 'show']); // View orders for their products

    // Add more vendor specific routes

});


// Optional: Redirect authenticated users trying to access inappropriate panels
// This can be handled by middleware or the Gate logic itself, but a dedicated middleware is cleaner.
// Example: If authenticated user is not Admin and tries to access /admin/*, redirect them.
// Can add this middleware to the 'web' middleware group or a custom middleware group.

// Fallback route (keep or adjust based on your needs)
// ...