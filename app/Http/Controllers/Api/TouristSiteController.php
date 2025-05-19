<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TouristSite; // Import TouristSite model
use App\Models\SiteCategory; // Import SiteCategory model (for filter)
use App\Http\Resources\TouristSiteResource; // Import TouristSite Resource
use App\Http\Resources\CommentResource; // Import Comment Resource for fetching comments
use App\Http\Resources\RatingResource; // Import Rating Resource for fetching ratings
// We won't need API Store/Update requests for *this* public browse controller
// use App\Http\Requests\Api\StoreTouristSiteRequest;
// use App\Http\Requests\Api\UpdateTouristSiteRequest;
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // Useful for contextual data (e.g., is favorited)


class TouristSiteController extends Controller
{
    /**
     * Display a listing of tourist sites.
     * Accessible at GET /api/tourist-sites
     * Can be public.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Start building the query
        $query = TouristSite::query();

        // Optional: Add filtering logic based on request parameters
        // Example: Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'LIKE', "%{$request->city}%");
        }
        // Example: Filter by country
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        // Example: Filter by category_id
        if ($request->filled('category_id')) {
             $query->where('category_id', $request->category_id);
         }
        // Example: Search by name or description
         if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
         }
        // Example: Order by name
        $query->orderBy('name');

        // Paginate the results
        $touristSites = $query->paginate(15); // Adjust pagination size as needed

        // Return the collection using TouristSiteResource
        return TouristSiteResource::collection($touristSites);
    }

    /**
     * Store a newly created resource in storage.
     * Note: Not for public API. Sites are created via Admin/Employee panel.
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Display the specified tourist site.
     * Accessible at GET /api/tourist-sites/{touristSite}
     * Can be public.
     *
     * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(TouristSite $touristSite)
    {
        // Load relationships needed for the show view (e.g., category, addedBy)
        $touristSite->load(['category', 'addedBy:id,username']);

        // Optional: Load polymorphic relationships if you want to show comments/ratings/experiences here
        // $touristSite->load(['category', 'addedBy:id,username', 'comments.user.profile', 'ratings.user.profile', 'experiences.user.profile']);


        // Return the single tourist site using TouristSiteResource
        return new TouristSiteResource($touristSite);
    }

    /**
     * Update the specified resource in storage.
     * Note: Not for public API.
     */
    public function update(Request $request, TouristSite $touristSite)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Remove the specified resource from storage.
     * Note: Not for public API.
     */
    public function destroy(TouristSite $touristSite)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

     // --- Optional Methods for Polymorphic Relationships (if fetching via site endpoint) ---
     // (Similar methods to ProductController/RatingController for fetching comments, ratings, isFavorited for this site)

     /**
      * Get comments for a specific tourist site.
      * Accessible at GET /api/tourist-sites/{touristSite}/comments (Requires a route definition)
      * Can be public.
      *
      * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
      * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
      */
     public function comments(TouristSite $touristSite)
     {
         // Fetch comments for this site, ordered
         // Load the user relationship for each comment and recursively load replies
         $comments = $touristSite->comments() // Uses the morphMany relation on the TouristSite model
                            ->whereNull('parent_comment_id') // Fetch only top-level comments
                            ->with(['user.profile', 'replies.user.profile']) // Load user for top-level and replies, load replies recursively
                            ->orderBy('created_at', 'asc') // Order by oldest first
                            ->paginate(10); // Paginate results

         // Return the collection of comments using CommentResource
         return CommentResource::collection($comments);
     }

      /**
       * Get ratings for a specific tourist site.
       * Accessible at GET /api/tourist-sites/{touristSite}/ratings (Requires a route definition)
       * Can be public.
       *
       * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
       * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
       */
     public function ratings(TouristSite $touristSite)
     {
          // Fetch ratings for this site, ordered
          // Load the user relationship for each rating
          $ratings = $touristSite->ratings() // Uses the morphMany relation on the TouristSite model
                             ->with('user.profile') // Load user
                             ->orderBy('created_at', 'desc') // Order by most recent
                             ->paginate(10); // Paginate results

          // Optional: Get average rating for this target
          // $averageRating = $touristSite->ratings()->avg('rating_value');

          // Return the collection of ratings using RatingResource
           return RatingResource::collection($ratings);

          // // If returning average + list
          // return response()->json([
          //     'average_rating' => round($averageRating, 1), // Example rounding
          //     'ratings' => RatingResource::collection($ratings)
          // ]);
     }

      /**
       * Get Site Experiences for a specific tourist site.
       * Accessible at GET /api/tourist-sites/{touristSite}/experiences (Requires a route definition)
       * Can be public.
       *
       * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
       * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
       */
       public function experiences(TouristSite $touristSite)
       {
            // Fetch experiences for this site, ordered
            // Load the user relationship for each experience
             $experiences = $touristSite->experiences() // Uses the hasMany relation on the TouristSite model
                               ->with('user.profile') // Load user
                               ->orderBy('visit_date', 'desc') // Order by visit date
                               ->paginate(10); // Paginate results

             // You'll need a SiteExperienceResource to format experiences
             return SiteExperienceResource::collection($experiences);
       }


      /**
       * Check if the authenticated user has favorited this tourist site.
       * Accessible at GET /api/tourist-sites/{touristSite}/is-favorited (Requires authentication)
       * (Requires a route definition in api.php)
       *
       * @param  \App\Models\TouristSite  $touristSite // Route Model Binding
       * @return \Illuminate\Http\JsonResponse
       */
       public function isFavorited(TouristSite $touristSite)
       {
           // This method requires authentication
           if (!Auth::check()) {
               return response()->json(['is_favorited' => false, 'message' => 'Authentication required to check favorite status.'], 401);
           }

           // Check if the authenticated user's favorites collection contains this site
            $isFavorited = Auth::user()->favorites()
                                ->where('target_type', (new TouristSite())->getMorphClass()) // Get the morph map name for TouristSite
                                ->where('target_id', $touristSite->id)
                                ->exists();

           return response()->json(['is_favorited' => $isFavorited]);
       }

}