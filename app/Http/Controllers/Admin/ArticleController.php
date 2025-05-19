<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article; // Import Article model
use App\Models\User; // Import User model (for authors)
use App\Http\Requests\Admin\StoreArticleRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateArticleRequest; // Import custom Update Request
use Illuminate\Support\Facades\Storage; // For file storage
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // To get authenticated user for default author or authorization


class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/articles
     */
    public function index()
    {
        // Fetch articles with their author relationship, paginated
        $articles = Article::with(['author:id,username']) // Load author's basic info
                           ->orderBy('created_at', 'desc') // Or orderBy('published_at', 'desc')
                           ->paginate(10); // Paginate results

        // Return the view and pass the data
        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/articles/create
     */
    public function create()
    {
        // Fetch users who can author articles (Admin, ArticleWriter). Pass only ID and username.
        $authors = User::whereIn('user_type', ['Admin', 'ArticleWriter'])->select('id', 'username')->get();

        // Define allowed article statuses
        $statuses = ['Draft', 'Published', 'Archived']; // ** هذا التعريف يجب أن يكون موجوداً **

        // Return the view and pass data
        return view('admin.articles.create', compact('authors', 'statuses')); // ** ويتم تمرير statuses هنا **
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/articles
     *
     * @param  \App\Http\Requests\Admin\StoreArticleRequest  $request // Use custom request for validation
     */
    public function store(StoreArticleRequest $request)
    {
        // Validation is handled by StoreArticleRequest

        DB::beginTransaction();

        try {
            $articleData = $request->only([
                'author_user_id', 'title', 'content', 'excerpt',
                 'tags', 'status', 'published_at'
            ]);

            // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('articles', 'public'); // Store in 'storage/app/public/articles'
                $articleData['main_image_url'] = '/storage/' . $imagePath; // Save public path
            } else {
                $articleData['main_image_url'] = null; // Ensure null if no file uploaded
            }

            // Handle video URL (it's a string in the request, no upload needed unless it's a video file upload)
            // Assuming video_url is an external link, just store it directly
             $articleData['video_url'] = $request->video_url;


            // Set published_at if status is 'Published' and published_at is not provided
            if ($request->status === 'Published' && empty($articleData['published_at'])) {
                 $articleData['published_at'] = now(); // Use current timestamp if published now
            } else if ($request->status !== 'Published') {
                 $articleData['published_at'] = null; // Ensure null if not published
            }
            // Else, use the provided published_at


            // Create the Article
            $article = Article::create($articleData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.articles.index')->with('success', 'Article created successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // Clean up uploaded file if transaction failed and file was stored
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error creating article: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating article: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/articles/{article}
     *
     * @param  \App\Models\Article  $article // Route Model Binding
     */
    public function show(Article $article)
    {
        // Load relationships for the detailed view (e.g., author with profile)
        $article->load(['author.profile']);

        // Return the view and pass the data
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/articles/{article}/edit
     *
     * @param  \App\Models\Article  $article // Route Model Binding
     */
    public function edit(Article $article)
    {
         // Load relationships for the edit form (e.g., author with profile)
        $article->load(['author.profile']);

        // Fetch necessary data for the form (same as create)
        $authors = User::whereIn('user_type', ['Admin', 'ArticleWriter'])->select('id', 'username')->get();

        // Define allowed article statuses
        $statuses = ['Draft', 'Published', 'Archived'];


        // Return the view and pass data
        return view('admin.articles.edit', compact('article', 'authors', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/articles/{article}
     *
     * @param  \App\Http\Requests\Admin\UpdateArticleRequest  $request // Use custom request for validation
     * @param  \App\Models\Article  $article // Route Model Binding
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        // Validation is handled by UpdateArticleRequest

        DB::beginTransaction();
        $oldImagePath = $article->main_image_url; // Store old image path for potential deletion
        $oldVideoUrl = $article->video_url; // Store old video URL for potential logic (e.g., deleting file if locally stored)


        try {
            $articleData = $request->only([
                'author_user_id', 'title', 'content', 'excerpt',
                 'tags', 'status', 'published_at'
            ]);

             // Handle file upload for main_image if present
            if ($request->hasFile('main_image')) {
                // Upload the new image
                $imagePath = $request->file('main_image')->store('articles', 'public');
                $articleData['main_image_url'] = '/storage/' . $imagePath; // Save public path

                 // Delete the old image file if it existed
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            } else if ($request->boolean('remove_main_image')) { // Handle checkbox to remove image
                 $articleData['main_image_url'] = null;
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                     Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            }
             // If no new image and not removing, keep the old URL (not included in $articleData initially)
            // This logic is implicitly handled by not setting main_image_url unless needed.


            // Handle video URL update
             $articleData['video_url'] = $request->video_url;
             // Optional: Handle 'remove_video' checkbox if implemented
             // if ($request->boolean('remove_video')) {
             //     $articleData['video_url'] = null;
             //     // Add logic here to delete video file if you were storing locally
             // }


            // Handle published_at based on status and provided value
            if ($request->status === 'Published' && empty($articleData['published_at'])) {
                 // If becoming published but no date set, use now()
                 $articleData['published_at'] = now();
            } else if ($request->status !== 'Published') {
                 // If changing FROM Published, or to Draft/Archived, clear published_at
                 $articleData['published_at'] = null;
            }
            // Else (status is Published and published_at is provided), keep the provided value


            // Update the Article
            $article->update($articleData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // If a new file was uploaded, clean it up on error
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error updating article: ' . $e->getMessage(), ['article_id' => $article->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating article: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/articles/{article}
     *
     * @param  \App\Models\Article  $article // Route Model Binding
     */
    public function destroy(Article $article)
    {
         DB::beginTransaction();
         $oldImagePath = $article->main_image_url; // Store old path to delete
         // $oldVideoUrl = $article->video_url; // Store old video URL for potential logic


         try {
             // Optional: Check for related polymorphic data (Comments, Ratings, Favorites)
             // If your migrations have 'restrict' on foreign keys (unlikely for polymorphic), deletion will fail.
             // If you need to prevent deletion if comments/ratings exist, check here:
             // if ($article->comments()->count() > 0 || $article->ratings()->count() > 0 || $article->favorites()->count() > 0) {
             //      return redirect()->route('admin.articles.index')->with('error', 'Cannot delete article with associated comments, ratings, or favorites.');
             // }


             $article->delete(); // This attempts deletion. If using soft deletes, it marks as deleted.

             DB::commit(); // Commit transaction

             // Delete the image file after successful DB deletion
             if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                  Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
             }
              // Delete local video file if applicable (based on schema and implementation)
             // If storing videos locally:
             // if ($oldVideoUrl && str_starts_with($oldVideoUrl, '/storage/') && Storage::disk('public')->exists(str_replace('/storage/', '', $oldVideoUrl))) {
             //     Storage::disk('public')->delete(str_replace('/storage/', '', $oldVideoUrl));
             // }


             return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully!');

         } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             Log::error('Error deleting article: ' . $e->getMessage(), ['article_id' => $article->id, 'exception' => $e]);
              // Catch specific Integrity Constraint Violation exceptions if needed (less likely for polymorphic)
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.articles.index')->with('error', 'Article cannot be deleted due to associated data.');
              }
             return redirect()->route('admin.articles.index')->with('error', 'Error deleting article: ' . $e->getMessage());
         }
    }
}