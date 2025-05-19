<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteCategory; // Import SiteCategory model
use App\Http\Requests\Admin\StoreSiteCategoryRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateSiteCategoryRequest; // Import custom Update Request
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class SiteCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/site-categories
     */
    public function index()
    {
        // Fetch all site categories, ordered by name
        // No pagination needed for a lookup table unless it's huge
        $categories = SiteCategory::orderBy('name')->get();

        // Return the view and pass the data
        return view('admin.site_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/site-categories/create
     */
    public function create()
    {
        // Return the view for creating a site category
        return view('admin.site_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/site-categories
     *
     * @param  \App\Http\Requests\Admin\StoreSiteCategoryRequest  $request // Use custom request for validation
     */
    public function store(StoreSiteCategoryRequest $request)
    {
        // Validation is handled by StoreSiteCategoryRequest

        try {
            $categoryData = $request->only(['name', 'description']);

            // Create the SiteCategory
            SiteCategory::create($categoryData);

            return redirect()->route('admin.site-categories.index')->with('success', 'Tourist site category created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating tourist site category: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating tourist site category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/site-categories/{siteCategory}
     * Note: Show is less common for simple lookup tables. Can be omitted.
     *
     * @param  \App\Models\SiteCategory  $siteCategory // Route Model Binding
     */
    public function show(SiteCategory $siteCategory)
    {
         // Redirect to edit page as show page is often redundant for simple resources
         return redirect()->route('admin.site-categories.edit', $siteCategory);

        // // Or return a show view if you create one
        // return view('admin.site_categories.show', compact('siteCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/site-categories/{siteCategory}/edit
     *
     * @param  \App\Models\SiteCategory  $siteCategory // Route Model Binding
     */
    public function edit(SiteCategory $siteCategory)
    {
        // Return the view for editing a site category and pass the data
        return view('admin.site_categories.edit', compact('siteCategory'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/site-categories/{siteCategory}
     *
     * @param  \App\Http\Requests\Admin\UpdateSiteCategoryRequest  $request // Use custom request for validation
     * @param  \App\Models\SiteCategory  $siteCategory // Route Model Binding
     */
    public function update(UpdateSiteCategoryRequest $request, SiteCategory $siteCategory)
    {
         // Validation is handled by UpdateSiteCategoryRequest

        try {
            $categoryData = $request->only(['name', 'description']);

            // Update the SiteCategory
            $siteCategory->update($categoryData);

            return redirect()->route('admin.site-categories.index')->with('success', 'Tourist site category updated successfully!');

        } catch (\Exception $e) {
             Log::error('Error updating tourist site category: ' . $e->getMessage(), ['category_id' => $siteCategory->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating tourist site category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/site-categories/{siteCategory}
     *
     * @param  \App\Models\SiteCategory  $siteCategory // Route Model Binding
     */
    public function destroy(SiteCategory $siteCategory)
    {
         try {
             // Check if any tourist sites use this category (based on set null delete in migration)
             // If sites exist, deleting the category will set their FKs to null.
             // If you need to prevent deletion if sites exist, check here before deleting.
             // Example check:
             // if ($siteCategory->touristSites()->count() > 0) {
             //      return redirect()->route('admin.site-categories.index')->with('error', 'Cannot delete category as it is used by existing tourist sites.');
             // }


             $siteCategory->delete(); // This attempts deletion

             return redirect()->route('admin.site-categories.index')->with('success', 'Tourist site category deleted successfully!');

         } catch (\Exception $e) {
             Log::error('Error deleting tourist site category: ' . $e->getMessage(), ['category_id' => $siteCategory->id, 'exception' => $e]);
              // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.site-categories.index')->with('error', 'Category cannot be deleted due to associated tourist sites.');
              }
             return redirect()->route('admin.site-categories.index')->with('error', 'Error deleting tourist site category: ' . $e->getMessage());
         }
    }
}