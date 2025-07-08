<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\User;
use App\Http\Requests\Api\ToggleFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ResolvesPolymorphicTargets; // Import the Trait

class FavoriteController extends Controller
{
    use ResolvesPolymorphicTargets; // Use the Trait

    /**
     * Display a listing of the authenticated user's favorites.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('target')->orderBy('added_at', 'desc')->paginate(15);
        return FavoriteResource::collection($favorites);
    }

    /**
     * Toggle the favorite status for a target item for the authenticated user.
     *
     * @param  \App\Http\Requests\Api\ToggleFavoriteRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(ToggleFavoriteRequest $request)
    {
        $targetType = $request->target_type;
        $targetId = $request->target_id;
        $userId = Auth::id();

        // Optional: Verify the target item actually exists using the model mapping
        // Although Rule::exists in Request should cover this, an explicit check can be useful for debugging or stricter logic.
        $modelClass = $this->mapTargetTypeToModel($targetType);
        if (!$modelClass || !($modelClass::find($targetId))) {
             // This case should ideally be caught by ToggleFavoriteRequest validation,
             // but it's a fallback for robustness.
            return response()->json(['message' => 'The specified item does not exist.'], 404);
        }

        $existingFavorite = Favorite::where('user_id', $userId)
                                   ->where('target_type', $targetType)
                                   ->where('target_id', $targetId)
                                   ->first();

        if ($existingFavorite) {
            try {
                $existingFavorite->delete();
                return response()->json(['message' => 'Item removed from favorites.', 'is_favorited' => false], 200);
            } catch (\Exception $e) {
                 Log::error('FavoriteController: Error removing favorite for user ' . $userId . ' target ' . $targetType . ':' . $targetId . ': ' . $e->getMessage(), ['exception' => $e]);
                 return response()->json(['message' => 'Failed to remove item from favorites. Internal server error.'], 500); // 500 Internal Server Error
            }
        } else {
            try {
               $newFavorite = Favorite::create([
                    'user_id' => $userId,
                    'target_type' => $targetType,
                    'target_id' => $targetId,
                    'added_at' => now(), // Pass the current time
                ]);

                return response()->json(['message' => 'Item added to favorites.', 'is_favorited' => true], 201);

            } catch (\Exception $e) {
                 Log::error('FavoriteController: Error adding favorite for user ' . $userId . ' target ' . $targetType . ':' . $targetId . ': ' . $e->getMessage(), ['exception' => $e]);
                 return response()->json(['message' => 'Failed to add item to favorites. Internal server error.'], 500); // 500 Internal Server Error
            }
        }
    }

    // Reuse the mapTargetTypeToModel in isFavoritedForTarget
    public function isFavoritedForTarget(string $targetType, int $targetId)
    {
        $userId = Auth::id();

        $modelClass = $this->mapTargetTypeToModel($targetType);
        if (!$modelClass || !$target = $modelClass::find($targetId)) {
            return response()->json(['message' => 'Invalid target type or target not found.'], 404);
        }

        $isFavorited = Favorite::where('user_id', $userId)
                                ->where('target_type', $targetType)
                                ->where('target_id', $targetId)
                                ->exists();

        return response()->json(['is_favorited' => $isFavorited]);
    }
}