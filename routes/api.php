<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api; // Import the API controllers namespace

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| Note: The "api" middleware group is automatically applied to all routes
| defined in this file by the RouteServiceProvider.
|
*/

// --- Publicly Accessible Routes ---
// These routes do NOT require a Sanctum token. Users can access them without logging in.

// Authentication (Login & Registration)
// Breeze API stack automatically adds these or similar routes
Route::post('/register', [Api\AuthController::class, 'register']);
Route::post('/login', [Api\AuthController::class, 'login']);

// Browse Tourist Information
Route::get('/tourist-sites', [Api\TouristSiteController::class, 'index']);
Route::get('/tourist-sites/{touristSite}', [Api\TouristSiteController::class, 'show']); // Using Route Model Binding
Route::get('/site-categories', [Api\SiteCategoryController::class, 'index']); // List categories

Route::get('/tourist-activities', [Api\TouristActivityController::class, 'index']);
Route::get('/tourist-activities/{touristActivity}', [Api\TouristActivityController::class, 'show']); // Using Route Model Binding

// Browse Hotels & Rooms (Rooms typically shown via Hotel details)
Route::get('/hotels', [Api\HotelController::class, 'index']);
Route::get('/hotels/{hotel}', [Api\HotelController::class, 'show']); // Using Route Model Binding
// Optional: Get rooms for a specific hotel
// Route::get('/hotels/{hotel}/rooms', [Api\HotelController::class, 'rooms']); // Requires a 'rooms' method in HotelController

// Browse Products & Categories
Route::get('/products', [Api\ProductController::class, 'index']);
Route::get('/products/{product}', [Api\ProductController::class, 'show']); // Using Route Model Binding
Route::get('/product-categories', [Api\ProductCategoryController::class, 'index']); // List categories

// Browse Articles (Blog)
Route::get('/articles', [Api\ArticleController::class, 'index']);
Route::get('/articles/{article}', [Api\ArticleController::class, 'show']); // Using Route Model Binding


// --- Protected Routes ---
// These routes require a valid Sanctum token in the Authorization header.
// The 'auth:sanctum' middleware checks for the token.

Route::middleware('auth:sanctum')->group(function () {

    // Authentication (Logout & Get Authenticated User)
    // Breeze API stack automatically adds these or similar routes
    Route::post('/logout', [Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        // Returns the authenticated user model instance
        // This is a simple endpoint to get user details using the token
        return $request->user();
    });

    // User Profile Management
    Route::get('/profile', [Api\UserProfileController::class, 'show']); // Get authenticated user's profile
    Route::put('/profile', [Api\UserProfileController::class, 'update']); // Update authenticated user's profile (Use PUT or PATCH)
    // Optional: Manage phone numbers? (Less common for mobile apps directly via profile endpoint)
    // Route::apiResource('profile/phone-numbers', Api\UserPhoneNumberController); // If managing phone numbers separately

    // Shopping Cart Management
    Route::get('/cart', [Api\ShoppingCartController::class, 'index']); // Get user's cart items
    Route::post('/cart/add', [Api\ShoppingCartController::class, 'store']); // Add item to cart
    Route::put('/cart/{cartItem}', [Api\ShoppingCartController::class, 'update']); // Update item quantity in cart
    Route::delete('/cart/{cartItem}', [Api\ShoppingCartController::class, 'destroy']); // Remove item from cart

    // Product Orders (User's own orders)
    Route::get('/my-orders', [Api\ProductOrderController::class, 'index']); // View authenticated user's orders
    Route::get('/my-orders/{productOrder}', [Api\ProductOrderController::class, 'show']); // View a specific order
    Route::post('/orders', [Api\ProductOrderController::class, 'store']); // Place a new order (typically from cart)

    // Hotel Bookings (User's own bookings)
    Route::get('/my-bookings', [Api\HotelBookingController::class, 'index']); // View authenticated user's bookings
    Route::get('/my-bookings/{hotelBooking}', [Api\HotelBookingController::class, 'show']); // View a specific booking
    Route::post('/bookings', [Api\HotelBookingController::class, 'store']); // Place a new booking
    // Consider adding Cancel/Update booking logic if allowed

    // Site Experiences (User's own contributions)
    // Using apiResource for CRUD on user's own site experiences
    Route::apiResource('my-experiences', Api\SiteExperienceController::class);

    // Polymorphic Actions (Favorites, Ratings, Comments)
    // Note: These endpoints perform actions related to FAVORITING, RATING, or COMMENTING
    // Getting the favorites/ratings/comments *FOR* a specific target (site, product, etc.)
    // should be handled by a method on the target's controller (see public/protected sections)

    // Favorites
    Route::post('/favorites/toggle', [Api\FavoriteController::class, 'toggle']); // Add or remove a favorite
    Route::get('/my-favorites', [Api\FavoriteController::class, 'index']); // View authenticated user's favorite items (Polymorphic)

    // Ratings
    // User can store, update, delete their own ratings
    Route::apiResource('ratings', Api\RatingController::class)->only(['store', 'update', 'destroy']);
    // Getting ratings *for* a target: Handled by methods on target controllers (e.g., TouristSiteController@ratings)

    // Comments
    // User can store, update, delete their own comments
    Route::apiResource('comments', Api\CommentController::class)->only(['store', 'update', 'destroy']);
    Route::get('/comments/{comment}/replies', [Api\CommentController::class, 'replies']); // Get replies for a specific comment
    // Getting comments *for* a target: Handled by methods on target controllers (e.g., ArticleController@comments)


    // --- Protected Routes for Specific User Roles (Example) ---
    // You would add middleware here to check user_type (e.g., 'middleware:can:isVendor')

    // Vendor specific routes (Example: Manage their own products)
    // Route::middleware('can:isVendor')->group(function () {
    //     Route::apiResource('vendor/products', Api\Vendor\ProductController::class); // Requires new controller
    //     Route::get('vendor/orders', [Api\Vendor\ProductOrderController::class, 'index']); // Requires new controller
    //     Route::get('vendor/orders/{productOrder}', [Api\Vendor\ProductOrderController::class, 'show']);
    //     // ... other vendor specific actions
    // });

    // Admin/Employee specific routes (Example: Manage all users, sites, etc.)
    // Route::middleware('can:isAdminOrEmployee')->group(function () {
    //     Route::apiResource('admin/users', Api\Admin\UserController::class); // Requires new controller
    //     Route::apiResource('admin/tourist-sites', Api\Admin\TouristSiteController::class);
    //     // ... other admin/employee specific actions
    // });

});


// --- Optional Public/Protected Routes to Fetch Polymorphic Data FOR a Target ---
// Alternative pattern: Fetch ratings/comments/favorites specific to an item (site, product, etc.)
// These could be public OR protected depending on whether you need to know the user's own rating/favorite status
// A common approach is to make them public and add the user's status if authenticated.

// Example: Get ratings for a specific tourist site
// Route::get('/tourist-sites/{touristSite}/ratings', [Api\TouristSiteController::class, 'ratings']); // Requires 'ratings' method in TouristSiteController
// Route::get('/tourist-sites/{touristSite}/comments', [Api\TouristSiteController::class, 'comments']); // Requires 'comments' method in TouristSiteController
// Route::get('/products/{product}/ratings', [Api\ProductController::class, 'ratings']);
// Route::get('/products/{product}/comments', [Api\ProductController::class, 'comments']);
// ... and so on for Hotels, Articles, SiteExperiences

// Note: Implement these methods in the respective controllers (e.g., TouristSiteController, ProductController)
// and fetch the related polymorphic data using the morphMany relationship defined in the models.