<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin; // Import Admin controllers namespace
use App\Http\Controllers\ProfileController; // Default Breeze/Jetstream profile controller
use App\Http\Controllers\Auth\AuthenticatedSessionController; // أو متحكم مشابه

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

// ... other auth routes ...


// Default welcome page
Route::view('/', 'welcome');

// Default dashboard and profile routes from Breeze (for regular users)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// The profile route created by Breeze Blade stack often uses a controller
Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // If using Breeze Blade defaults
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout'); 
    // Your existing profile route using Volt
     Route::view('profile', 'profile')->name('profile'); // Keeping your Volt route
});


require __DIR__.'/auth.php'; // Includes Breeze/Volt auth routes (login, register, etc.)


// --- Admin Panel Routes ---
// Group admin routes under a prefix and apply middleware (e.g., auth, admin role/permission)
Route::prefix('admin')
     ->name('admin.') // ** تم إضافة هذا السطر لإضافة البادئة 'admin.' لأسماء المسارات **
     ->middleware(['auth', 'can:access-admin-panel'])
     ->group(function () {

    // Admin Dashboard - Its name will now be 'admin.dashboard'
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Resource Management Routes - Names will now be 'admin.users.index', 'admin.products.index', etc.
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

// Optional: Redirect authenticated admin users trying to access /login etc. to admin dashboard
// ...

// Fallback route for SPA or generic welcome
// ...