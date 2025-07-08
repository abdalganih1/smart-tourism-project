أعد بناء
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api; // Import the API controllers namespace
use App\Http\Resources\UserResource; // Import UserResource for consistency

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

// Authentication (Login & Registration) - Handled by AuthController
Route::post('/register', [Api\AuthController::class, 'register']);
Route::post('/login', [Api\AuthController::class, 'login']);

// Browse Tourist Information
Route::get('/tourist-sites', [Api\TouristSiteController::class, 'index']);
Route::get('/tourist-sites/{touristSite}', [Api\TouristSiteController::class, 'show']);
Route::get('/site-categories', [Api\SiteCategoryController::class, 'index']);

// Browse Tourist Activities
Route::get('/tourist-activities', [Api\TouristActivityController::class, 'index']);
Route::get('/tourist-activities/{touristActivity}', [Api\TouristActivityController::class, 'show']);

// Browse Hotels
Route::get('/hotels', [Api\HotelController::class, 'index']);
Route::get('/hotels/{hotel}', [Api\HotelController::class, 'show']);
// Get rooms for a specific hotel (often public or contextually authorized)
Route::get('/hotels/{hotel}/rooms', [Api\HotelController::class, 'rooms']);


// Browse Products (Crafts) & Categories
Route::get('/products', [Api\ProductController::class, 'index']);
Route::get('/products/{product}', [Api\ProductController::class, 'show']);
Route::get('/product-categories', [Api\ProductCategoryController::class, 'index']);

// Browse Articles (Blog)
Route::get('/articles', [Api\ArticleController::class, 'index']);
Route::get('/articles/{article}', [Api\ArticleController::class, 'show']);


// --- Public Routes to Fetch Polymorphic Data FOR a Target ---
// These endpoints retrieve comments/ratings/experiences *for* a specific item.
// They can be public to allow browsing.
// Note: These call custom methods in the respective Controllers.

// Get comments for a specific target (e.g., /api/article/1/comments)
// {targetType} and {targetId} are route parameters that map to arguments in indexForTarget method
Route::get('/{targetType}/{targetId}/comments', [Api\CommentController::class, 'indexForTarget']);

// Get ratings for a specific target (e.g., /api/product/5/ratings)
Route::get('/{targetType}/{targetId}/ratings', [Api\RatingController::class, 'indexForTarget']);

// Get experiences for a specific tourist site (e.g., /api/tourist-sites/1/experiences)
// Note: This is a specific endpoint for experiences under a site, not a generic polymorphic route
Route::get('/tourist-sites/{touristSite}/experiences', [Api\TouristSiteController::class, 'experiences']);


// --- Protected Routes ---
// These routes require a valid Sanctum token in the Authorization header.
// The 'auth:sanctum' middleware checks for the token and populates Auth::user().

Route::middleware('auth:sanctum')->group(function () {

    // Authentication (Logout & Get Authenticated User)
    Route::post('/logout', [Api\AuthController::class, 'logout']);
    // Get authenticated user details - includes profile & phone numbers via UserResource
    // The UserResource will load these relationships when $request->user() is passed to it.
    Route::get('/user', function (Request $request) {
        return new App\Http\Resources\UserResource($request->user()->load(['profile', 'phoneNumbers']));
    });


    // --- Profile Information ---
    // GET: /api/profile -> Fetches the user's profile information
    // PUT/PATCH: /api/profile -> Updates user's textual profile information
    Route::get('/profile', [Api\UserProfileController::class, 'show']);
    Route::put('/profile', [Api\UserProfileController::class, 'update']); // Use PUT for full replacement, or PATCH for partial update


    // --- Profile Picture Management ---
    // POST: /api/profile/picture -> Uploads a new profile picture
    // DELETE: /api/profile/picture -> Removes the current profile picture
    Route::post('/profile/picture', [Api\UserProfileController::class, 'updateProfilePicture']); // POST is standard for file uploads
    Route::delete('/profile/picture', [Api\UserProfileController::class, 'removeProfilePicture']);


    // --- Password Management ---
    // PUT/PATCH: /api/profile/password -> Updates the user's password
    Route::put('/profile/password', [Api\UserProfileController::class, 'updatePassword']); // Use PUT or PATCH for password updates


    // Shopping Cart Management
    // Accessible at /api/cart (list user's cart items)
    Route::get('/cart', [Api\ShoppingCartController::class, 'index']);
    // Accessible at /api/cart/add (add item to cart) - Using POST on a custom route
    Route::post('/cart/add', [Api\ShoppingCartController::class, 'store']);
    // Accessible at /api/cart/{cartItem} (update item quantity) - Using PUT on a resource-like URL
    Route::put('/cart/{cartItem}', [Api\ShoppingCartController::class, 'update']);
    // Accessible at /api/cart/{cartItem} (remove item) - Using DELETE on a resource-like URL
    Route::delete('/cart/{cartItem}', [Api\ShoppingCartController::class, 'destroy']);
    // Accessible at /api/cart/clear (clear the entire cart) - Using POST on a custom route
    Route::post('/cart/clear', [Api\ShoppingCartController::class, 'clearCart']);


    // Product Orders (Authenticated User's own orders)
    // Accessible at /api/my-orders (list user's orders)
    Route::get('/my-orders', [Api\ProductOrderController::class, 'index']);
    // Accessible at /api/my-orders/{productOrder} (show a specific order)
    Route::get('/my-orders/{productOrder}', [Api\ProductOrderController::class, 'show']);
    // Accessible at /api/orders (place a new order) - Using POST on a resource-like URL
    Route::post('/orders', [Api\ProductOrderController::class, 'store']);


    // Hotel Bookings (Authenticated User's own bookings)
    // Accessible at /api/my-bookings (list user's bookings)
    Route::get('/my-bookings', [Api\HotelBookingController::class, 'index']);
    // Accessible at /api/my-bookings/{hotelBooking} (show a specific booking)
    Route::get('/my-bookings/{hotelBooking}', [Api\HotelBookingController::class, 'show']);
    // Accessible at /api/bookings (place a new booking) - Using POST on a resource-like URL
    Route::post('/bookings', [Api\HotelBookingController::class, 'store']);
    // Optional: Allow user to cancel booking (status update)
    // Accessible at /api/my-bookings/{hotelBooking}/cancel - Using POST on a custom route
    // Note: The destroy method in controller can be used for cancellation logic
    Route::post('/my-bookings/{hotelBooking}/cancel', [Api\HotelBookingController::class, 'destroy']); // Re-purposing destroy method for cancel action


    // Site Experiences (Authenticated User's own contributions)
    // Using apiResource for CRUD on user's *own* site experiences.
    // Accessible at /api/my-experiences, /api/my-experiences/{siteExperience}, etc.
    // The apiResource covers index, store, show, update, destroy.
    // The index and show methods in Api\SiteExperienceController are designed to fetch/show *only* the authenticated user's experiences.
    Route::apiResource('my-experiences', Api\SiteExperienceController::class);


    // Polymorphic Actions (Favorites, Ratings, Comments) - Authenticated User Actions
    // Note: These endpoints perform actions or fetch *the user's* specific related items.

    // Favorites
    // Accessible at /api/favorites/toggle - Using POST on a custom route
    Route::post('/favorites/toggle', [Api\FavoriteController::class, 'toggle']);
    // Accessible at /api/my-favorites - Using GET on a custom route
    Route::get('/my-favorites', [Api\FavoriteController::class, 'index']);
    // Optional: Check if authenticated user has favorited a specific item (e.g., GET /api/articles/1/is-favorited)
    // Accessible at /{targetType}/{targetId}/is-favorited - Using GET on a polymorphic route
    Route::get('/{targetType}/{targetId}/is-favorited', [Api\FavoriteController::class, 'isFavoritedForTarget']);


    // Ratings
    // Accessible at /api/ratings (store) - Using POST on a resource route
    // Accessible at /api/ratings/{rating} (update/delete) - Using PUT/DELETE on a resource route
    // Index/Show are typically done via the target endpoint (e.g., /articles/{article}/ratings)
    Route::apiResource('ratings', Api\RatingController::class)->only(['store', 'update', 'destroy']);


    // Comments
    // Accessible at /api/comments (store) - Using POST on a resource route
    // Accessible at /api/comments/{comment} (update/delete) - Using PUT/DELETE on a resource route
    // Index/Show are typically done via the target endpoint (e.g., /articles/{article}/comments)
    Route::apiResource('comments', Api\CommentController::class)->only(['store', 'update', 'destroy']);
    // Accessible at /api/comments/{comment}/replies (get replies for a specific comment) - Using GET on a custom route
    Route::get('/comments/{comment}/replies', [Api\CommentController::class, 'replies']);


    // --- Protected Routes for Specific User Roles (Admin/Vendor/Manager API) ---
    // These routes would be in separate controllers (e.g., Api\Admin\...)
    // and protected by additional middleware or policies (e.g., can:manage-users, can:update-all-products)
    // They are not included in this general API file for simplicity but would follow similar patterns
    // as the admin web panel controllers but returning JSON resources.

});

// Note: This file does NOT include Admin/Vendor/Manager specific API endpoints for managing ALL users,
// ALL products, ALL orders, etc. Those would typically be in separate controllers and route groups
// with explicit authorization middleware (e.g., auth:sanctum, can:isAdmin).

والمتحكم
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating; // Import Rating model
use App\Http\Requests\Api\StoreRatingRequest; // Import custom Store Request
use App\Http\Requests\Api\UpdateRatingRequest; // Import custom Update Request
use App\Http\Resources\RatingResource; // Import Rating Resource
use Illuminate\Support\Facades\Auth; // For authentication
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Auth\Access\AuthorizationException; // For authorization errors
// Use CommentController helper for mapping target type if needed for indexForTarget
use App\Http\Controllers\Api\CommentController;


class RatingController extends Controller
{
    /**
     * Display a listing of the resource (less common for ratings themselves).
     * You'd typically fetch ratings for a specific target.
     * Accessible at GET /api/ratings
     * (Might not be used, but included for resource completeness)
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // This endpoint is less useful for public API.
        // If needed, it might list *all* recent ratings across the app (for moderation preview?)
        // Or redirect to a "my ratings" endpoint if authenticated.
        // Simple default: Return recent ratings (might be slow if many)
        $ratings = Rating::with('user.profile')->orderBy('created_at', 'desc')->paginate(20);
        return RatingResource::collection($ratings);
    }

    /**
     * Store a new rating for a target item for the authenticated user.
     * Accessible at POST /api/ratings
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\StoreRatingRequest  $request // Use custom request for validation
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\RatingResource
     */
    public function store(StoreRatingRequest $request)
    {
        // Validation is handled by StoreRatingRequest (includes authorization for existing rating)
        // Authentication check handled by 'auth:sanctum' middleware

        $userId = Auth::id();
        $targetType = $request->target_type;
        $targetId = $request->target_id;

        // The StoreRatingRequest already checked if a rating by this user for this target exists.
        // If it exists, validation would fail with a unique constraint type error.
        // If it doesn't exist, proceed to create.

        try {
            $ratingData = $request->only(['target_type', 'target_id', 'rating_value', 'review_title', 'review_text']);
            $ratingData['user_id'] = $userId; // Assign the authenticated user as the author

            // Create the Rating
            $rating = Rating::create($ratingData);

            // Load relationships needed for the resource response
            $rating->load(['user.profile', 'target']); // Load user and the polymorphic target

            // Return the created rating using RatingResource
            return new RatingResource($rating);

        } catch (\Exception $e) {
            Log::error('Error creating rating: ' . $e->getMessage(), ['user_id' => $userId, 'target' => $targetType . ':' . $targetId, 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to create rating. Please try again.'], 500);
        }
    }

    /**
     * Display the specified resource. (Less common for ratings themselves - fetched via target)
     *
     * @param  \App\Models\Rating  $rating // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(Rating $rating)
    {
         // Load relationships needed for the resource response
         $rating->load(['user.profile', 'target']); // Load user and the polymorphic target

        // Return the single rating using RatingResource
        return new RatingResource($rating);
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /api/ratings/{rating}
     * Requires authentication and authorization (user owns the rating).
     *
     * @param  \App\Http\Requests\Api\UpdateRatingRequest  $request // Use custom request for validation
     * @param  \App\Models\Rating  $rating // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateRatingRequest $request, Rating $rating)
    {
         // Validation is handled by UpdateRatingRequest
         // Authentication check handled by middleware
         // Authorization (user owns rating) check handled by UpdateRatingRequest authorize() method

        try {
            // Only allow updating rating_value, review_title, review_text
            $rating->update($request->only(['rating_value', 'review_title', 'review_text']));

            // Load relationships needed for the resource response
             $rating->load(['user.profile', 'target']); // Reload relationships after update

            // Return the updated rating using RatingResource
            return new RatingResource($rating);

        } catch (\Exception $e) {
             Log::error('Error updating rating: ' . $e->getMessage(), ['rating_id' => $rating->id, 'user_id' => Auth::id(), 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to update rating. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /api/ratings/{rating}
     * Requires authentication and authorization (user owns the rating).
     *
     * @param  \App\Models\Rating  $rating // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Rating $rating)
    {
         // Authentication check handled by middleware
         // Authorization (user owns rating) check handled by UpdateRatingRequest authorize() method (or add here)
         // Let's add the authorization check here explicitly for clarity in Destroy
         if ($rating->user_id !== Auth::id()) {
              throw new AuthorizationException('You do not own this rating.');
         }


        try {
            $rating->delete();

            // Return a success response
            return response()->json(['message' => 'Rating deleted successfully.'], 200); // Using 200 with message

        } catch (\Exception $e) {
             Log::error('Error deleting rating: ' . $e->getMessage(), ['rating_id' => $rating->id, 'user_id' => Auth::id(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to delete rating. Please try again.'], 500);
        }
    }

     // --- Custom Method ---

     /**
      * Get ratings for a specific target (polymorphic).
      * Accessible at GET /api/{targetType}/{targetId}/ratings (Requires a route definition)
      * Can be public.
      *
      * @param  string  $targetType  The type of the target ('article', 'product', etc.)
      * @param  int     $targetId    The ID of the target
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
      */
     public function indexForTarget(string $targetType, int $targetId)
     {
         // Map the target type string to the actual model class using helper from CommentController
         $modelClass = (new CommentController())->mapTargetTypeToModel($targetType); // Reusing the helper

         // Check if the target type is valid and the target item exists
         if (!$modelClass || !$target = $modelClass::find($targetId)) {
             return response()->json(['message' => 'Invalid target or target not found.'], 404);
         }

         // Fetch ratings for this target
         // Load user relationship for each rating
         $ratings = $target->ratings() // Uses the morphMany relation on the target model (e.g., Product::ratings())
                           ->with('user.profile') // Load user
                           ->orderBy('created_at', 'desc') // Order by most recent
                           ->paginate(10); // Paginate results

          // Optional: Get average rating for this target
          // $averageRating = $target->ratings()->avg('rating_value');

         // Return the collection of ratings using RatingResource
         return RatingResource::collection($ratings);

         // // If returning average + list
         // return response()->json([
         //     'average_rating' => round($averageRating, 1), // Example rounding
         //     'ratings' => RatingResource::collection($ratings)
         // ]);
     }
}
والمتحكم
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment; // Import Comment model
use App\Models\User; // Import User model (for user relationship)
use App\Http\Requests\Api\StoreCommentRequest; // Import custom Store Request
use App\Http\Requests\Api\UpdateCommentRequest; // Import custom Update Request
use App\Http\Resources\CommentResource; // Import Comment Resource
use Illuminate\Support\Facades\Auth; // For authentication and user checks
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Auth\Access\AuthorizationException; // For authorization errors


class CommentController extends Controller
{
    /**
     * Display a listing of the resource (less common for comments themselves).
     * You'd typically fetch comments for a specific target.
     * Accessible at GET /api/comments
     * (Might not be used, but included for resource completeness)
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // This endpoint is less useful for public API.
        // If needed, it might list *all* recent comments across the app (for moderation preview?)
        // Or redirect to a "my comments" endpoint if authenticated.
        // For this design, let's assume it's not a primary public endpoint.
        // If accessed by Admin via API, they'd likely have a different controller.
        // Simple default: Return recent comments (might be slow if many)
         $comments = Comment::with('user.profile')->orderBy('created_at', 'desc')->paginate(20);
         return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /api/comments
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\StoreCommentRequest  $request // Use custom request for validation
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\CommentResource
     */
    public function store(StoreCommentRequest $request)
    {
        // Validation is handled by StoreCommentRequest
        // Authorization check (user is authenticated) is handled by 'auth:sanctum' middleware

        try {
            $commentData = $request->only(['target_type', 'target_id', 'content', 'parent_comment_id']);
            $commentData['user_id'] = Auth::id(); // Assign the authenticated user as the author

            // Create the Comment
            $comment = Comment::create($commentData);

            // Load relationships needed for the resource response
            $comment->load(['user.profile', 'parent']);

            // Return the created comment using CommentResource
            return new CommentResource($comment);

        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage(), ['user_id' => Auth::id(), 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to create comment. Please try again.'], 500);
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /api/comments/{comment}
     * (Might not be the primary way to view comments - usually fetched via target)
     *
     * @param  \App\Models\Comment  $comment // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(Comment $comment)
    {
         // Load relationships needed for the resource response
        $comment->load(['user.profile', 'parent.user.profile']); // Load parent and its user
        // Optional: Load replies for this specific comment
        // $comment->load(['user.profile', 'parent.user.profile', 'replies.user.profile']);


        // Return the single comment using CommentResource
        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /api/comments/{comment}
     * Requires authentication and authorization (user owns the comment).
     *
     * @param  \App\Http\Requests\Api\UpdateCommentRequest  $request // Use custom request for validation
     * @param  \App\Models\Comment  $comment // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
         // Validation is handled by UpdateCommentRequest
         // Authentication check handled by middleware

         // Authorization: Ensure the authenticated user owns this comment
         if ($comment->user_id !== Auth::id()) {
             // Throw Laravel's built-in authorization exception
             throw new AuthorizationException('You do not own this comment.');
             // Or return a JSON error response:
             // return response()->json(['message' => 'You are not authorized to update this comment.'], 403); // 403 Forbidden
         }

        try {
            // Only allow updating the content
            $comment->update($request->only(['content']));

            // Load relationships needed for the resource response
             $comment->load(['user.profile', 'parent']); // Reload relationships after update

            // Return the updated comment using CommentResource
            return new CommentResource($comment);

        } catch (\Exception $e) {
             Log::error('Error updating comment: ' . $e->getMessage(), ['comment_id' => $comment->id, 'user_id' => Auth::id(), 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to update comment. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /api/comments/{comment}
     * Requires authentication and authorization (user owns the comment).
     *
     * @param  \App\Models\Comment  $comment // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        // Authentication check handled by middleware

        // Authorization: Ensure the authenticated user owns this comment
        if ($comment->user_id !== Auth::id()) {
             throw new AuthorizationException('You do not own this comment.');
             // Or return a JSON error response:
             // return response()->json(['message' => 'You are not authorized to delete this comment.'], 403); // 403 Forbidden
        }

        try {
            // Deleting a parent comment should cascade delete replies based on migration's onDelete('cascade')
            $comment->delete();

            // Return a success response (e.g., 204 No Content or 200 with a message)
            return response()->json(['message' => 'Comment deleted successfully.'], 200); // Using 200 with message for clarity

        } catch (\Exception $e) {
             Log::error('Error deleting comment: ' . $e->getMessage(), ['comment_id' => $comment->id, 'user_id' => Auth::id(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to delete comment. Please try again.'], 500);
        }
    }

     // --- Custom Methods ---

     /**
      * Get comments for a specific target (polymorphic).
      * Accessible at GET /api/{targetType}/{targetId}/comments (Requires a route definition)
      * Can be public.
      *
      * @param  string  $targetType  The type of the target ('article', 'product', etc.)
      * @param  int     $targetId    The ID of the target
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
      */
     public function indexForTarget(string $targetType, int $targetId)
     {
         // Map the target type string to the actual model class
         $modelClass = $this->mapTargetTypeToModel($targetType);

         // Check if the target type is valid and the target item exists
         if (!$modelClass || !$target = $modelClass::find($targetId)) {
             return response()->json(['message' => 'Invalid target or target not found.'], 404);
         }

         // Fetch top-level comments for this target (comments with no parent_comment_id)
         // Load user relationship for each comment and recursively load replies
         $comments = $target->comments() // Uses the morphMany relation on the target model (e.g., Article::comments())
                            ->whereNull('parent_comment_id') // Fetch only top-level comments
                            ->with(['user.profile', 'replies.user.profile']) // Load user for top-level and replies, load replies recursively (be cautious with deep nesting)
                            ->orderBy('created_at', 'asc') // Order by oldest first
                            ->paginate(10); // Paginate results

         // Return the collection of comments using CommentResource
         return CommentResource::collection($comments);
     }

      /**
       * Get replies for a specific comment.
       * Accessible at GET /api/comments/{comment}/replies (Requires a route definition)
       * Can be public.
       *
       * @param  \App\Models\Comment  $comment // Route Model Binding for the parent comment
       * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
       */
      public function replies(Comment $comment)
      {
          // Fetch replies for this specific comment
          // Load user relationship for each reply and recursively load nested replies
           $replies = $comment->replies() // Uses the 'replies' hasMany relation on the Comment model
                             ->with(['user.profile', 'replies.user.profile']) // Load user and nested replies
                             ->orderBy('created_at', 'asc') // Order by oldest first
                             ->paginate(10); // Paginate results

           // Return the collection of replies using CommentResource
           return CommentResource::collection($replies);
      }


     /**
      * Helper method to map target_type string to Model class.
      * Add more mappings as needed for other polymorphic targets.
      *
      * @param string $targetType
      * @return string|null The full model class name or null if not found.
      */
     public function mapTargetTypeToModel(string $targetType): ?string
     {
         // Use a mapping array to be explicit and secure
         $map = [
             'article' => \App\Models\Article::class,
             'product' => \App\Models\Product::class,
             'touristsite' => \App\Models\TouristSite::class,
             'hotel' => \App\Models\Hotel::class,
             'siteexperience' => \App\Models\SiteExperience::class,
             // Add other polymorphic targets here
         ];

         // Return the model class if found in the map
         return $map[strtolower($targetType)] ?? null;
     }
}

بحيث نعتمد مصطلح واحد وهو
tourist-sites 
products
articles
hotels
site-experiences
tourist-activities
كأسماء من الممكن ان تكون في الروابط
مثلاً
لا يحب ان يكون هناك رابط
 GET /TouristSite/11/comments 
GET /TouristSite/11/ratings 

كل كتابة الروابط يجب ان تكون
بالشكل
tourist-sites 


