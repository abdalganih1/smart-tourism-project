<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductCategory; // To get categories for forms
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ** استيراد Trait AuthorizesRequests **
use Storage;
use Str;
class ProductController extends Controller
{
    use AuthorizesRequests; // ** استخدام Trait AuthorizesRequests داخل الكلاس **


    /**
     * Display a listing of the vendor's products.
     */
    public function index(): View
    {
        // Authorize viewing any products managed by vendor or admin
        // This will check the 'viewAny' ability/gate on the Product model/class.
        // Your ProductPolicy::viewAny() should return true for Vendors and Admins.
        $this->authorize('viewAny', Product::class); // Check policy/gate for the class

        // Get the authenticated vendor user
        $vendor = Auth::user();

        // Fetch only products belonging to this vendor, with pagination
        // The authorization above ensures *only* vendors/admins reach here.
        // For a vendor, we still filter by their ownership for their specific panel list.
        if ($vendor->isVendor()) {
             $products = $vendor->products()->paginate(10); // Filter by vendor's products
        } else {
            // If an Admin is somehow accessing this panel (maybe via a shared admin route),
            // they might see all products. Adjust this logic if only vendors should ever see this list.
            // Based on your web.php routing and gates, only Vendors should access '/vendor/*', so filtering by $vendor->products() is correct.
            $products = $vendor->products()->paginate(10); // Should still work as Admin might have 0 products relation
            // Alternative for Admin view (if they could access this method): $products = Product::paginate(10);
        }


        return view('vendor.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        // Authorize creating a product
        $this->authorize('create', Product::class); // Policy/Gate will check if user is Admin or Vendor

        // Get product categories to populate a dropdown in the form
        $categories = ProductCategory::all();

        return view('vendor.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        // Authorize storing a product (validation handles general ability, policy adds context)
        // $this->authorize('create', Product::class); // Already checked in ->authorize() in create, but good practice here too

        $vendor = Auth::user(); // Get the authenticated vendor

        // Create the product, ensuring it's linked to the authenticated vendor
        // $request->validated() already includes the necessary data based on Request rules
        $product = $vendor->products()->create($request->validated()); // Uses the 'products' relationship to set seller_user_id

        // Handle image upload if included in the request and validated
        if ($request->hasFile('main_image')) {
             // Ensure the directory exists if needed
             // Storage::disk('public')->makeDirectory('products'); // Not usually needed, store handles this

            // Store the file and get its path
            $imagePath = $request->file('main_image')->store('products', 'public'); // 'products' is the folder inside storage/app/public

            // Update the product record with the public URL
            $product->main_image_url = '/storage/' . $imagePath; // Prepend '/storage/' to get the public URL
            $product->save(); // Save the product model to update the URL
        }


        return redirect()->route('vendor.products.index')
                         ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        // Authorize viewing this specific product (Policy checks ownership for Vendor)
        $this->authorize('view', $product); // Policy will check if user is Admin OR (Vendor AND owner)

        // Eager load relationships if needed for the show view (e.g., category, ratings, comments)
        $product->load('category'); // Example: Load category relation for display

        return view('vendor.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        // Authorize editing this specific product (Policy checks ownership for Vendor)
        $this->authorize('update', $product); // Policy will check if user is Admin OR (Vendor AND owner)

         // Get product categories for the dropdown
        $categories = ProductCategory::all();

        return view('vendor.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        // Authorize updating this specific product
        $this->authorize('update', $product); // Policy will check if user is Admin OR (Vendor AND owner)

        // Validation is handled by UpdateProductRequest ($request->validated())

        // Get the validated data, excluding the image file if not updated in validated() rules
        $validatedData = $request->validated();

        // Update the product attributes
        // Exclude image file itself from validated data before updating model attributes
        $product->update(array_diff_key($validatedData, array_flip(['main_image'])));


        // Handle image update/deletion
        if ($request->hasFile('main_image')) {
            // Delete the old image if it exists
            if ($product->main_image_url) {
                 // Remove '/storage/' prefix to get the path relative to storage/app/public
                $oldImagePath = Str::after($product->main_image_url, '/storage/');
                Storage::disk('public')->delete($oldImagePath);
            }

            // Store the new image
             $imagePath = $request->file('main_image')->store('products', 'public');
             $product->main_image_url = '/storage/' . $imagePath;
             $product->save(); // Save again to update the image URL
        }
         // If you had a checkbox for 'remove_main_image' and it was checked:
         // if ($request->boolean('remove_main_image') && $product->main_image_url) {
         //     $oldImagePath = Str::after($product->main_image_url, '/storage/');
         //     Storage::disk('public')->delete($oldImagePath);
         //     $product->main_image_url = null;
         //     $product->save();
         // }


        return redirect()->route('vendor.products.index')
                         ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Authorize deleting this specific product
        $this->authorize('delete', $product); // Policy will check if user is Admin OR (Vendor AND owner)

        // Optional: Handle deletion of associated files (images) before deleting the record
        if ($product->main_image_url) {
             $oldImagePath = Str::after($product->main_image_url, '/storage/');
            Storage::disk('public')->delete($oldImagePath);
        }

        // Delete the product record
        $product->delete();

        return redirect()->route('vendor.products.index')
                         ->with('success', 'Product deleted successfully.');
    }
}