<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article; // Import Article model
use App\Http\Resources\ArticleResource; // Import Article Resource
// We won't need API Store/Update requests for *this* public browse controller
// use App\Http\Requests\Api\StoreArticleRequest;
// use App\Http\Requests\Api\UpdateArticleRequest;
use Illuminate\Support\Facades\Auth; // Useful for contextual data (e.g., is favorited)
use Illuminate\Support\Facades\Log; // For logging errors

class ArticleController extends Controller
{
    /**
     * Display a listing of published articles.
     * Accessible at GET /api/articles
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Start building the query for published articles
        $query = Article::query()->where('status', 'Published');

        // Optional: Add filtering logic based on request parameters
        // Example: Filter by tags (assuming tags are comma-separated in DB)
        if ($request->filled('tag')) {
            $tag = $request->tag;
            $query->where('tags', 'LIKE', "%{$tag}%"); // Basic LIKE search for tags
        }
         // Example: Filter by author_id
         if ($request->filled('author_id')) {
              $query->where('author_user_id', $request->author_id);
         }
         // Example: Search by title or excerpt
          if ($request->filled('search')) {
             $searchTerm = $request->search;
             $query->where(function($q) use ($searchTerm) {
                 $q->where('title', 'LIKE', "%{$searchTerm}%")
                   ->orWhere('excerpt', 'LIKE', "%{$searchTerm}%");
             });
          }
        // Example: Order by published date
        $query->orderBy('published_at', 'desc');

        // Paginate the results
        $articles = $query->paginate(15); // Adjust pagination size as needed

        // Return the collection of articles using ArticleResource
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /api/articles
     * Note: This endpoint is typically not for public API users (tourists).
     * It would be for authenticated admin/authors, likely in a different controller/route group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) // Using base Request as it's not a public endpoint
    {
        // Prevent creation via this public endpoint
        // You could add a gate/policy check here too, but returning 405 is clearer
        // if the route is defined as public but creation is not intended.
         return response()->json(['message' => 'Method Not Allowed'], 405);

        // // If you allow authors to create via this API, you'd uncomment the following:
        // $this->authorize('create', Article::class); // Policy check
        // // Use the appropriate StoreArticleRequest for API
        // // $validatedData = $request->validated();
        // // ... creation logic ...
        // // return new ArticleResource($article);
    }

    /**
     * Display the specified published article.
     * Accessible at GET /api/articles/{article}
     *
     * @param  \App\Models\Article  $article // Route Model Binding
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\ArticleResource
     */
    public function show(Article $article)
    {
        // Check if the article is published before showing it to public users
        if ($article->status !== 'Published') {
            // Return 404 Not Found if the article is not published (for public access)
            // Admins might access drafts via admin panel routes/controllers
            return response()->json(['message' => 'Article not found or not published.'], 404);
        }

        // Load necessary relationships for the show view (e.g., author with profile)
        $article->load(['author.profile']);

        // Optional: Load polymorphic relationships if you want to show comments/ratings here
        // $article->load(['author.profile', 'comments.user.profile', 'ratings.user.profile']);

        // Return the single article using ArticleResource
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /api/articles/{article}
     * Note: This endpoint is typically not for public API users (tourists).
     *
     * @param  \Illuminate\Http\Request  $request // Using base Request as it's not a public endpoint
     * @param  \App\Models\Article  $article // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Article $article)
    {
        // Prevent updates via this public endpoint
        return response()->json(['message' => 'Method Not Allowed'], 405);

        // // If you allow authors/admins to update via this API, you'd uncomment the following:
        // $this->authorize('update', $article); // Policy check
        // // Use the appropriate UpdateArticleRequest for API
        // // $validatedData = $request->validated();
        // // ... update logic ...
        // // return new ArticleResource($article);
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /api/articles/{article}
     * Note: This endpoint is typically not for public API users (tourists).
     *
     * @param  \App\Models\Article  $article // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Article $article)
    {
        // Prevent deletion via this public endpoint
        return response()->json(['message' => 'Method Not Allowed'], 405);

        // // If you allow authors/admins to delete via this API, you'd uncomment the following:
        // $this->authorize('delete', $article); // Policy check
        // // ... deletion logic ...
        // // return response()->json(['message' => 'Article deleted']);
    }

     // --- Optional Methods for Polymorphic Relationships (if fetching via article endpoint) ---

     /**
      * Get comments for a specific article.
      * Accessible at GET /api/articles/{article}/comments
      * (Requires a route definition in api.php)
      *
      * @param  \App\Models\Article  $article // Route Model Binding
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
      */
     public function comments(Article $article)
     {
         // Ensure article is published before showing comments (optional, depending on rules)
         if ($article->status !== 'Published') {
             return response()->json(['message' => 'Article not found or not published.'], 404);
         }

         // Fetch comments for this article, ordered by creation date
         // Load the user relationship for each comment
         $comments = $article->comments()->with('user.profile')->orderBy('created_at')->paginate(15); // Adjust pagination/ordering


         // You'll need a CommentResource to format comments
         // return CommentResource::collection($comments);
         // Placeholder response if CommentResource is not yet created
         return response()->json($comments); // Simple JSON response for now
     }

     /**
      * Get ratings for a specific article.
      * Accessible at GET /api/articles/{article}/ratings
      * (Requires a route definition in api.php)
      *
      * @param  \App\Models\Article  $article // Route Model Binding
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
      */
     public function ratings(Article $article)
     {
          // Ensure article is published before showing ratings (optional)
          if ($article->status !== 'Published') {
              return response()->json(['message' => 'Article not found or not published.'], 404);
          }

         // Fetch ratings for this article, ordered
         // Load the user relationship for each rating
         $ratings = $article->ratings()->with('user.profile')->orderBy('created_at')->paginate(15); // Adjust pagination/ordering

         // Optional: Get average rating
         // $averageRating = $article->ratings()->avg('rating_value');

         // You'll need a RatingResource to format ratings
         // return RatingResource::collection($ratings);
         // Placeholder response if RatingResource is not yet created
          return response()->json($ratings); // Simple JSON response for now

         // // If returning average + list
         // return response()->json([
         //     'average_rating' => round($averageRating, 1), // Example rounding
         //     'ratings' => RatingResource::collection($ratings)
         // ]);
     }

      /**
       * Check if the authenticated user has favorited this article.
       * Accessible at GET /api/articles/{article}/is-favorited (Requires authentication)
       * (Requires a route definition in api.php)
       *
       * @param  \App\Models\Article  $article // Route Model Binding
       * @return \Illuminate\Http\JsonResponse
       */
      public function isFavorited(Article $article)
      {
          // This method requires authentication
          if (!Auth::check()) {
              return response()->json(['is_favorited' => false, 'message' => 'Authentication required to check favorite status.'], 401);
          }

           // Check if the authenticated user's favorites collection contains this article
           // Assumes user->favorites() relationship is defined and loads polymorphic relation
           // A simpler check: query the favorites table directly
           $isFavorited = Auth::user()->favorites()
                               ->where('target_type', (new Article())->getMorphClass()) // Get the morph map name for Article
                               ->where('target_id', $article->id)
                               ->exists();

           return response()->json(['is_favorited' => $isFavorited]);
      }
}