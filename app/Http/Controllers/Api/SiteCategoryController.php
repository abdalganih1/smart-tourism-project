<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteCategory; // Import SiteCategory model
use App\Http\Resources\SiteCategoryResource; // Import SiteCategory Resource


class SiteCategoryController extends Controller
{
    /**
     * Display a listing of site categories.
     * Accessible at GET /api/site-categories
     * Can be public.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Fetch all site categories, ordered by name.
        $categories = SiteCategory::orderBy('name')->get(); // No pagination for simple list

        // Return the collection using SiteCategoryResource
        return SiteCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     * Note: Not for public API.
     */
    public function store(Request $request)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Display the specified resource.
     * Note: Show is less common for simple lookup tables in API.
     */
    public function show(string $id)
    {
         // Optional: Fetch a single category and its sites
         // $category = SiteCategory::with('touristSites')->findOrFail($id);
         // return new SiteCategoryResource($category);
         return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Update the specified resource in storage.
     * Note: Not for public API.
     */
    public function update(Request $request, string $id)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Remove the specified resource from storage.
     * Note: Not for public API.
     */
    public function destroy(string $id)
    {
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }
}