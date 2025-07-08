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
use App\Traits\ResolvesPolymorphicTargets; // Import the Trait


class RatingController extends Controller
{
    use ResolvesPolymorphicTargets; // Use the Trait

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
     * @return \App\Http\Resources\RatingResource|\Illuminate\Http\JsonResponse
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
     * @return \App\Http\Resources\RatingResource|\Illuminate\Http\JsonResponse
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
         $modelClass = $this->mapTargetTypeToModel($targetType); // Reusing the helper

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