<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite; // Import Favorite model
use App\Models\User; // Import User model (for user relationship)
use App\Http\Requests\Api\ToggleFavoriteRequest; // Import custom Toggle Request
use App\Http\Resources\FavoriteResource; // Import Favorite Resource
use Illuminate\Support\Facades\Auth; // For authentication
use Illuminate\Support\Facades\Log; // For logging


class FavoriteController extends Controller
{
    /**
     * Display a listing of the authenticated user's favorites.
     * Accessible at GET /api/my-favorites (Custom route name suggested)
     * Requires authentication.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Authentication check handled by 'auth:sanctum' middleware

        // Fetch the authenticated user's favorites
        // Eager load the 'target' polymorphic relationship to get the actual items
        $favorites = Auth::user()->favorites()->with('target')->orderBy('added_at', 'desc')->paginate(15); // Adjust pagination/ordering

        // Return the collection of favorites using FavoriteResource
        return FavoriteResource::collection($favorites);
    }

    /**
     * Toggle the favorite status for a target item for the authenticated user.
     * Accessible at POST /api/favorites/toggle (Custom route name suggested)
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\ToggleFavoriteRequest  $request // Use custom request for validation
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(ToggleFavoriteRequest $request)
    {
        // Validation is handled by ToggleFavoriteRequest
        // Authentication check handled by 'auth:sanctum' middleware

        $targetType = $request->target_type;
        $targetId = $request->target_id;
        $userId = Auth::id();

        // Find if the favorite already exists
        $existingFavorite = Favorite::where('user_id', $userId)
                                   ->where('target_type', $targetType)
                                   ->where('target_id', $targetId)
                                   ->first();

        if ($existingFavorite) {
            // If favorite exists, remove it
            try {
                $existingFavorite->delete();
                return response()->json(['message' => 'Item removed from favorites.', 'is_favorited' => false], 200); // 200 OK
            } catch (\Exception $e) {
                 Log::error('Error removing favorite: ' . $e->getMessage(), ['user_id' => $userId, 'target' => $targetType . ':' . $targetId, 'exception' => $e]);
                 return response()->json(['message' => 'Failed to remove item from favorites. Please try again.'], 500);
            }
        } else {
            // If favorite does not exist, create it
            try {
                $newFavorite = Favorite::create([
                    'user_id' => $userId,
                    'target_type' => $targetType,
                    'target_id' => $targetId,
                    // added_at is set by database default timestamp
                ]);

                 // Optional: Load target for response, or just return success message
                 // $newFavorite->load('target');
                 // return new FavoriteResource($newFavorite);

                return response()->json(['message' => 'Item added to favorites.', 'is_favorited' => true], 201); // 201 Created

            } catch (\Exception $e) {
                 Log::error('Error adding favorite: ' . $e->getMessage(), ['user_id' => $userId, 'target' => $targetType . ':' . $targetId, 'exception' => $e]);
                 return response()->json(['message' => 'Failed to add item to favorites. Please try again.'], 500);
            }
        }
    }

     /**
      * Display the specified resource. (Not used for Favorites - see index or isFavorited)
      */
     // public function show(Favorite $favorite) { ... }

     /**
      * Update the specified resource in storage. (Not used for Favorites)
      */
     // public function update(Request $request, Favorite $favorite) { ... }

     /**
      * Remove the specified resource from storage. (Not used for Favorites - see toggle)
      */
     // public function destroy(Favorite $favorite) { ... }

      // --- Custom Method (Optional) ---

      /**
       * Check if the authenticated user has favorited a specific item.
       * Accessible at GET /api/{targetType}/{targetId}/is-favorited (Custom route suggested)
       * Requires authentication.
       *
       * @param  string  $targetType  The type of the target ('article', 'product', etc.)
       * @param  int     $targetId    The ID of the target
       * @return \Illuminate\Http\JsonResponse
       */
       public function isFavoritedForTarget(string $targetType, int $targetId)
       {
           // Authentication check handled by middleware
           // Authorization: Ensure the authenticated user is checked against their own favorites
           $userId = Auth::id();

            // Map the target type string to the actual model class
            $modelClass = (new CommentController())->mapTargetTypeToModel($targetType); // Reuse mapping helper from CommentController

            // Check if the target type is valid and the target item exists
            if (!$modelClass || !$target = $modelClass::find($targetId)) {
                return response()->json(['message' => 'Invalid target or target not found.'], 404);
            }


           $isFavorited = Favorite::where('user_id', $userId)
                                    ->where('target_type', $targetType) // Use string type directly from request
                                    ->where('target_id', $targetId)
                                    ->exists();

           return response()->json(['is_favorited' => $isFavorited]);
       }
}