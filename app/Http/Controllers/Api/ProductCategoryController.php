<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory; // Import ProductCategory model
use App\Http\Resources\ProductCategoryResource; // Import ProductCategory Resource
// No API Requests/Resources needed for this simple public controller


class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     * Accessible at GET /api/product-categories
     * Can be public.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Fetch all product categories, ordered by name.
        // Load parent relationship if needed for hierarchical display on frontend (optional)
        $categories = ProductCategory::with('parent')->orderBy('name')->get(); // No pagination for simple list

        // Return the collection using ProductCategoryResource
        return ProductCategoryResource::collection($categories);
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
     * Note: Show is less common for simple lookup tables in API. Usually filtered lists are sufficient.
     * If needed, you might return a category with its nested products.
     */
    public function show(string $id)
    {
         // Optional: Fetch a single category and its products
         // $category = ProductCategory::with('products')->findOrFail($id);
         // return new ProductCategoryResource($category);
         return response()->json(['message' => 'Method Not Allowed'], 405); // Or 404 if you prefer not to expose this route
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