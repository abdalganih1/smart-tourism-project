<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory; // Import ProductCategory model
use App\Http\Requests\Admin\StoreProductCategoryRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateProductCategoryRequest; // Import custom Update Request
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)
use Illuminate\Support\Facades\DB; // For database transactions


class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/product-categories
     */
    public function index()
    {
        // Fetch product categories with their parent relationship, ordered by name
        // No pagination needed for a lookup table unless it's huge
        $categories = ProductCategory::with('parent')->orderBy('name')->get();

        // Return the view and pass the data
        return view('admin.product_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/product-categories/create
     */
    public function create()
    {
        // Fetch existing categories to list as potential parents
        $parentCategories = ProductCategory::orderBy('name')->get();

        // Return the view for creating a category and pass data
        return view('admin.product_categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/product-categories
     *
     * @param  \App\Http\Requests\Admin\StoreProductCategoryRequest  $request // Use custom request for validation
     */
    public function store(StoreProductCategoryRequest $request)
    {
        // Validation is handled by StoreProductCategoryRequest

        try {
            $categoryData = $request->only(['name', 'description', 'parent_category_id']);

             // Ensure parent_category_id is null if an empty value or '0' is submitted by select
             if (empty($categoryData['parent_category_id'])) {
                 $categoryData['parent_category_id'] = null;
             }


            // Create the ProductCategory
            ProductCategory::create($categoryData);

            return redirect()->route('admin.product-categories.index')->with('success', 'Product category created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating product category: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating product category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/product-categories/{productCategory}
     * Note: Show is less common for simple lookup tables. Can be omitted.
     *
     * @param  \App\Models\ProductCategory  $productCategory // Route Model Binding
     */
    public function show(ProductCategory $productCategory)
    {
        // Redirect to edit page as show page is often redundant for simple resources
        return redirect()->route('admin.product-categories.edit', $productCategory);

        // // Or return a show view if you create one
        // return view('admin.product_categories.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/product-categories/{productCategory}/edit
     *
     * @param  \App\Models\ProductCategory  $productCategory // Route Model Binding
     */
    public function edit(ProductCategory $productCategory)
    {
        // Fetch existing categories to list as potential parents (exclude the category being edited)
        $parentCategories = ProductCategory::where('id', '!=', $productCategory->id)->orderBy('name')->get();

        // Return the view for editing a category and pass the data
        return view('admin.product_categories.edit', compact('productCategory', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/product-categories/{productCategory}
     *
     * @param  \App\Http\Requests\Admin\UpdateProductCategoryRequest  $request // Use custom request for validation
     * @param  \App\Models\ProductCategory  $productCategory // Route Model Binding
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
         // Validation is handled by UpdateProductCategoryRequest

        try {
            $categoryData = $request->only(['name', 'description', 'parent_category_id']);

             // Ensure parent_category_id is null if an empty value or '0' is submitted by select
            if (empty($categoryData['parent_category_id'])) {
                $categoryData['parent_category_id'] = null;
            }

            // Update the ProductCategory
            $productCategory->update($categoryData);

            return redirect()->route('admin.product-categories.index')->with('success', 'Product category updated successfully!');

        } catch (\Exception $e) {
             Log::error('Error updating product category: ' . $e->getMessage(), ['category_id' => $productCategory->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating product category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/product-categories/{productCategory}
     *
     * @param  \App\Models\ProductCategory  $productCategory // Route Model Binding
     */
    public function destroy(ProductCategory $productCategory)
    {
         try {
             // Check if any products use this category (based on set null delete in migration)
             // Check if any child categories exist (based on set null delete in migration)
             // If products/children exist, deleting the category will set their FKs to null.
             // If you need to prevent deletion if products/children exist, check here before deleting.
             // Example check:
             // if ($productCategory->products()->count() > 0) {
             //      return redirect()->route('admin.product-categories.index')->with('error', 'Cannot delete category as it is used by existing products.');
             // }
             // if ($productCategory->children()->count() > 0) {
             //      return redirect()->route('admin.product-categories.index')->with('error', 'Cannot delete category as it has child categories.');
             // }


             $productCategory->delete(); // This attempts deletion

             return redirect()->route('admin.product-categories.index')->with('success', 'Product category deleted successfully!');

         } catch (\Exception $e) {
             Log::error('Error deleting product category: ' . $e->getMessage(), ['category_id' => $productCategory->id, 'exception' => $e]);
              // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.product-categories.index')->with('error', 'Category cannot be deleted due to associated data.');
              }
             return redirect()->route('admin.product-categories.index')->with('error', 'Error deleting product category: ' . $e->getMessage());
         }
    }
}