<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // Import Product model
use App\Http\Resources\ProductResource; // Import Product Resource
use App\Http\Resources\CommentResource; // Import Comment Resource for fetching comments
use App\Http\Resources\RatingResource; // Import Rating Resource for fetching ratings
// We won't need API Store/Update requests for *this* public browse controller
// use App\Http\Requests\Api\StoreProductRequest;
// use App\Http\Requests\Api\UpdateProductRequest;
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // Useful for contextual data (e.g., is favorited)


class ProductController extends Controller
{
    /**
     * Display a listing of products.
     * Accessible at GET /api/products
     * Can be public.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Start building the query for available products
        $query = Product::query()->where('is_available', true)->where('stock_quantity', '>', 0); // Filter for available and in stock (adjust based on logic)

        // Optional: Add filtering logic based on request parameters
        // Example: Filter by category_id
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
         // Example: Filter by seller_user_id
         if ($request->filled('seller_user_id')) {
              $query->where('seller_user_id', $request->seller_user_id);
          }
        // Example: Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        // Example: Search by name or description
         if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
         }
        // Example: Order results
        $query->orderBy('name');

        // Paginate the results
        $products = $query->paginate(15); // Adjust pagination size as needed

        // Return the collection of products using ProductResource
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     * Note: Not for public API.
     */
    public function store(Request $request) // Using base Request
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Display the specified product.
     * Accessible at GET /api/products/{product}
     * Can be public.
     *
     * @param  \App\Models\Product  $product // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        // Optional: Check if product is available/in stock for public view
        // if (!$product->is_available || $product->stock_quantity <= 0) {
        //      return response()->json(['message' => 'Product not available.'], 404);
        // }


        // Load relationships needed for the show view (e.g., seller with profile, category)
        $product->load(['seller.profile', 'category']);

        // Optional: Load polymorphic relationships if you want to show comments/ratings here
        // $product->load(['seller.profile', 'category', 'comments.user.profile', 'ratings.user.profile']);


        // Return the single product using ProductResource
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     * Note: Not for public API.
     */
    public function update(Request $request, Product $product) // Using base Request
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Remove the specified resource from storage.
     * Note: Not for public API.
     */
    public function destroy(Product $product) // Using base Request
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

     // --- Optional Methods for Polymorphic Relationships (if fetching via product endpoint) ---

     /**
      * Get comments for a specific product.
      * Accessible at GET /api/products/{product}/comments (Requires a route definition)
      * Can be public.
      *
      * @param  \App\Models\Product  $product // Route Model Binding
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
      */
     public function comments(Product $product)
     {
         // Fetch comments for this product, ordered
         // Load the user relationship for each comment and recursively load replies
         $comments = $product->comments() // Uses the morphMany relation on the Product model
                            ->whereNull('parent_comment_id') // Fetch only top-level comments
                            ->with(['user.profile', 'replies.user.profile']) // Load user for top-level and replies, load replies recursively (be cautious with deep nesting)
                            ->orderBy('created_at', 'asc') // Order by oldest first
                            ->paginate(10); // Paginate results

         // Return the collection of comments using CommentResource
         return CommentResource::collection($comments);
     }

      /**
       * Get ratings for a specific product.
       * Accessible at GET /api/products/{product}/ratings (Requires a route definition)
       * Can be public.
       *
       * @param  \App\Models\Product  $product // Route Model Binding
       * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
       */
     public function ratings(Product $product)
     {
          // Fetch ratings for this product, ordered
          // Load the user relationship for each rating
          $ratings = $product->ratings() // Uses the morphMany relation on the Product model
                             ->with('user.profile') // Load user
                             ->orderBy('created_at', 'desc') // Order by most recent
                             ->paginate(10); // Paginate results

          // Optional: Get average rating
          // $averageRating = $product->ratings()->avg('rating_value');

          // You'll need a RatingResource to format ratings
           return RatingResource::collection($ratings);

          // // If returning average + list
          // return response()->json([
          //     'average_rating' => round($averageRating, 1), // Example rounding
          //     'ratings' => RatingResource::collection($ratings)
          // ]);
     }

      /**
       * Check if the authenticated user has favorited this product.
       * Accessible at GET /api/products/{product}/is-favorited (Requires authentication)
       * (Requires a route definition in api.php)
       *
       * @param  \App\Models\Product  $product // Route Model Binding
       * @return \Illuminate\Http\JsonResponse
       */
       public function isFavorited(Product $product)
       {
           // This method requires authentication
           if (!Auth::check()) {
               return response()->json(['is_favorited' => false, 'message' => 'Authentication required to check favorite status.'], 401);
           }

           // Check if the authenticated user's favorites collection contains this product
            $isFavorited = Auth::user()->favorites()
                                ->where('target_type', (new Product())->getMorphClass()) // Get the morph map name for Product
                                ->where('target_id', $product->id)
                                ->exists();

           return response()->json(['is_favorited' => $isFavorited]);
       }
}