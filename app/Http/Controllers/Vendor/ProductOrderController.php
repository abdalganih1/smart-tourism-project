<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse; // Although not used by 'only', keep for method signature
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ** استيراد Trait AuthorizesRequests **

class ProductOrderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of orders containing this vendor's products.
     */
    public function index(): View
    {
        $vendor = Auth::user();

        // Get IDs of products belonging to this vendor
        $vendorProductIds = $vendor->products->pluck('id');

        // Find all order item IDs associated with these products
        $vendorOrderItemIds = ProductOrderItem::whereIn('product_id', $vendorProductIds)->pluck('order_id')->unique();

        // Fetch the distinct orders that contain these items, with pagination
        $orders = ProductOrder::whereIn('id', $vendorOrderItemIds)
                               ->with('items.product', 'user') // Eager load order items, their products, and the user who ordered
                               ->latest() // Order by newest first
                               ->paginate(10);

        // Optional: Filter the items within each order to only show vendor's products if the view requires it

        return view('vendor.product_orders.index', compact('orders'));
    }

    /**
     * Display the specified order (if it contains vendor's products).
     */
    public function show(ProductOrder $productOrder): View
    {
        // Use the Gate to authorize viewing this specific order for this vendor
        $this->authorize('view-vendor-order', $productOrder);

        // The gate should load the 'items' relationship, but load others if needed
         $productOrder->load('user', 'items.product');

         // Optional: Filter order items in the view to only show the vendor's products
         // $vendor = Auth::user();
         // $vendorProductIds = $vendor->products->pluck('id');
         // $vendorItemsInOrder = $productOrder->items->filter(function ($item) use ($vendorProductIds) {
         //     return $vendorProductIds->contains($item->product_id);
         // });


        return view('vendor.product_orders.show', compact('productOrder')); // You might pass $vendorItemsInOrder instead/additionally
    }

    /**
     * Show the form for creating a new resource. (Not used by 'only' routes)
     */
    public function create()
    {
        abort(404); // Or throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }

    /**
     * Store a newly created resource in storage. (Not used by 'only' routes)
     */
    public function store(Request $request)
    {
         abort(404);
    }

    /**
     * Show the form for editing the specified resource. (Not used by 'only' routes)
     */
    public function edit(string $id)
    {
         abort(404);
    }

    /**
     * Update the specified resource in storage. (Not used by 'only' routes)
     * Note: Vendor might be allowed to update order status (e.g., Shipped).
     * If so, you would add a specific route/method for that action and authorize it.
     */
    public function update(Request $request, string $id)
    {
         abort(404);
    }

    /**
     * Remove the specified resource from storage. (Not used by 'only' routes)
     */
    public function destroy(string $id)
    {
         abort(404);
    }
}