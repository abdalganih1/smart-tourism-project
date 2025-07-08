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
use App\Traits\ResolvesPolymorphicTargets; // Import the Trait


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