<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductOrder; // Import ProductOrder model
use App\Models\User; // Import User model (for user relationship)
use App\Models\ProductOrderItem; // Import ProductOrderItem model (for items relationship)
use App\Models\Product; // Import Product model (nested relationship via items)
// No need for Store/Update requests if only index/show are used via resource route
// If adding update status functionality, you'd need a custom request.
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // For authorization check (optional, handled by middleware/policy)


class ProductOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/product-orders
     */
    public function index()
    {
        // Fetch product orders with their user relationship, paginated
        $productOrders = ProductOrder::with(['user:id,username']) // Load user's basic info
                                     ->orderBy('order_date', 'desc') // Order by order date
                                     ->paginate(15); // Paginate results

        // Return the view and pass the data
        return view('admin.product_orders.index', compact('productOrders'));
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/product-orders/{productOrder}
     *
     * @param  \App\Models\ProductOrder  $productOrder // Route Model Binding
     */
    public function show(ProductOrder $productOrder)
    {
        // Load relationships for the detailed view
        // Load user with profile, and order items with their product relationship
        $productOrder->load(['user.profile', 'items.product']);

        // Return the view and pass the data
        return view('admin.product_orders.show', compact('productOrder'));
    }

    // create, store, edit, update, destroy methods are not used
    // based on the Route::resource(...)->only(['index', 'show']) definition in web.php
    // If you need to add functionality like "Update Status", you'd add a custom method here
    // and define a custom route for it (e.g., POST /admin/product-orders/{productOrder}/update-status)
}