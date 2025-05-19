<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // Import Product model
use App\Models\ProductCategory; // Import ProductCategory model
use App\Models\User; // Import User model (for sellers/vendors)
use App\Http\Requests\Admin\StoreProductRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateProductRequest; // Import custom Update Request
use Illuminate\Support\Facades\Storage; // For file storage
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // To check authenticated user role (optional, handled by middleware)


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/products
     */
    public function index()
    {
        // Fetch products with their seller and category relationships, paginated
        $products = Product::with(['seller.profile', 'category']) // Load seller's basic info and profile, and category
                           ->orderBy('created_at', 'desc')
                           ->paginate(10); // Paginate results

        // Return the view and pass the products data
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/products/create
     */
    public function create()
    {
        // Fetch necessary data for the form
        $categories = ProductCategory::orderBy('name')->get();
        // Fetch users with user_type = 'Vendor' or 'Admin' who can be sellers
        $sellers = User::whereIn('user_type', ['Vendor', 'Admin'])->get();

        // Return the view and pass data
        return view('admin.products.create', compact('categories', 'sellers'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/products
     *
     * @param  \App\Http\Requests\Admin\StoreProductRequest  $request // Use custom request for validation
     */
    public function store(StoreProductRequest $request)
    {
        // Validation is handled by StoreProductRequest

        DB::beginTransaction();

        try {
            $productData = $request->only([
                'seller_user_id', 'name', 'description', 'color',
                'stock_quantity', 'price', 'category_id', 'is_available'
            ]);

            // Handle file upload for main_image_url if present
            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('products', 'public'); // Store in 'storage/app/public/products'
                $productData['main_image_url'] = '/storage/' . $imagePath; // Save public path
            }

            // Create the Product
            $product = Product::create($productData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // Clean up uploaded file if transaction failed and file was stored
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error creating product: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/products/{product}
     *
     * @param  \App\Models\Product  $product // Route Model Binding
     */
    public function show(Product $product)
    {
        // Load relationships for the detailed view
        $product->load(['seller.profile', 'category']);

        // Return the view and pass the product data
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/products/{product}/edit
     *
     * @param  \App\Models\Product  $product // Route Model Binding
     */
    public function edit(Product $product)
    {
         // Load relationships for the edit form
        $product->load(['seller.profile', 'category']);

        // Fetch necessary data for the form (same as create)
        $categories = ProductCategory::orderBy('name')->get();
        $sellers = User::whereIn('user_type', ['Vendor', 'Admin'])->get();


        // Return the view and pass data
        return view('admin.products.edit', compact('product', 'categories', 'sellers'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/products/{product}
     *
     * @param  \App\Http\Requests\Admin\UpdateProductRequest  $request // Use custom request for validation
     * @param  \App\Models\Product  $product // Route Model Binding
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // Validation is handled by UpdateProductRequest

        DB::beginTransaction();
        $oldImagePath = $product->main_image_url; // Store old path to delete on success

        try {
            $productData = $request->only([
                'seller_user_id', 'name', 'description', 'color',
                'stock_quantity', 'price', 'category_id', 'is_available'
            ]);

             // Handle file upload for main_image_url if present
            if ($request->hasFile('main_image')) {
                // Upload the new image
                $imagePath = $request->file('main_image')->store('products', 'public');
                $productData['main_image_url'] = '/storage/' . $imagePath; // Save public path

                 // Delete the old image file if it existed
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                      Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            } else if ($request->boolean('remove_main_image')) { // Handle checkbox to remove image
                 $productData['main_image_url'] = null;
                 if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                     Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
                 }
            }


            // Update the Product
            $product->update($productData);

            DB::commit(); // Commit transaction

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            // If a new file was uploaded, clean it up on error
            if (isset($imagePath)) {
                 Storage::disk('public')->delete($imagePath);
            }
            Log::error('Error updating product: ' . $e->getMessage(), ['product_id' => $product->id, 'exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/products/{product}
     *
     * @param  \App\Models\Product  $product // Route Model Binding
     */
    public function destroy(Product $product)
    {
         DB::beginTransaction();
         $oldImagePath = $product->main_image_url; // Store old path to delete

         try {
             // Optional: Check for related data (ProductOrderItems, ShoppingCartItems)
             // If your migrations have 'restrict' on foreign keys like product_id
             // in product_order_items or shopping_cart_items, the delete will fail
             // and throw an exception if related records exist.
             // You can add checks here if needed for more specific error messages.
             // if ($product->orderItems()->count() > 0 || $product->shoppingCartItems()->count() > 0) {
             //      return redirect()->route('admin.products.index')->with('error', 'Cannot delete product with associated orders or cart items.');
             // }


             $product->delete(); // This triggers deletion. If using soft deletes, it marks as deleted.

             DB::commit(); // Commit transaction

             // Delete the image file after successful DB deletion
             if ($oldImagePath && Storage::disk('public')->exists(str_replace('/storage/', '', $oldImagePath))) {
                  Storage::disk('public')->delete(str_replace('/storage/', '', $oldImagePath));
             }


             return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');

         } catch (\Exception $e) {
             DB::rollBack(); // Rollback transaction
             Log::error('Error deleting product: ' . $e->getMessage(), ['product_id' => $product->id, 'exception' => $e]);
              // Catch specific Integrity Constraint Violation exceptions if needed
              if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') {
                   return redirect()->route('admin.products.index')->with('error', 'Product cannot be deleted due to associated orders or cart items.');
              }
             return redirect()->route('admin.products.index')->with('error', 'Error deleting product: ' . $e->getMessage());
         }
    }

     /**
      * Handle file upload (Helper method if needed, but storage handled inline).
      * This might be useful if you had multiple file uploads or complex logic.
      *
      * @param  \Illuminate\Http\UploadedFile|null  $file
      * @return string|null
      */
     protected function handleImageUpload(?\Illuminate\Http\UploadedFile $file): ?string
     {
         if ($file) {
             $path = $file->store('products', 'public');
             return '/storage/' . $path;
         }
         return null;
     }
}