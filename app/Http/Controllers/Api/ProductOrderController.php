<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductOrder; // Import ProductOrder model
use App\Models\ProductOrderItem; // Import ProductOrderItem model
use App\Models\ShoppingCartItem; // Import ShoppingCartItem model (needed for ordering logic)
use App\Models\Product; // Import Product model (nested via items, also for stock checks)
use App\Http\Requests\Api\StoreProductOrderRequest; // Import custom Store Request
use App\Http\Resources\ProductOrderResource; // Import ProductOrder Resource
use Illuminate\Support\Facades\Auth; // For authentication
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Auth\Access\AuthorizationException; // For authorization errors
use Illuminate\Support\Facades\DB; // For database transactions


class ProductOrderController extends Controller
{
    /**
     * Display a listing of the authenticated user's product orders.
     * Accessible at GET /api/my-orders (Custom route name suggested)
     * Requires authentication.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Authentication check handled by 'auth:sanctum' middleware

        // Fetch the authenticated user's product orders
        // Eager load necessary relationships (items, nested product)
        $productOrders = Auth::user()->productOrders()
                                    ->with(['items.product:id,name,price']) // Load items and basic product info
                                    ->orderBy('order_date', 'desc') // Order by order date
                                    ->paginate(15); // Paginate results

        // Return the collection of orders using ProductOrderResource
        return ProductOrderResource::collection($productOrders);
    }

    /**
     * Store a new product order for the authenticated user (typically from cart).
     * Accessible at POST /api/orders (Custom route name suggested)
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\StoreProductOrderRequest  $request // Use custom request for validation
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\ProductOrderResource
     */
    public function store(StoreProductOrderRequest $request)
    {
        // Validation is handled by StoreProductOrderRequest (e.g., shipping address if provided)
        // Authentication check handled by 'auth:sanctum' middleware

        $userId = Auth::id();
        $user = Auth::user(); // Get the user instance

        // --- Business Logic / Inventory Check / Calculate Total ---
        // This typically involves retrieving items from the user's shopping cart.
        $cartItems = $user->shoppingCartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your shopping cart is empty.'], 400);
        }

        $totalAmount = 0;
        $orderItemsData = [];
        $productsToUpdateStock = [];

        DB::beginTransaction();

        try {
            // Check stock and prepare order items data
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                // Ensure product exists and is available
                if (!$product || !$product->is_available) {
                    DB::rollBack();
                     return response()->json(['message' => "Product '{$product->name}' is not available."], 400);
                }

                // Check stock quantity
                if ($product->stock_quantity < $cartItem->quantity) {
                    DB::rollBack();
                    return response()->json(['message' => "Not enough stock for product '{$product->name}'. Available: {$product->stock_quantity}"], 400);
                }

                // Calculate item total and add to order items data
                $itemPrice = $product->price; // Use current product price or price at time of adding to cart? Using current for simplicity.
                $itemSubtotal = $cartItem->quantity * $itemPrice;
                $totalAmount += $itemSubtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price_at_purchase' => $itemPrice, // Record the price at time of order
                ];

                // Prepare for stock update
                $productsToUpdateStock[] = ['product' => $product, 'quantity' => $cartItem->quantity];
            }

            // Create the Product Order
            $orderData = $request->only([
                 'shipping_address_line1', 'shipping_address_line2', 'shipping_city',
                 'shipping_postal_code', 'shipping_country'
            ]);
            $orderData['user_id'] = $userId;
            $orderData['total_amount'] = $totalAmount;
            $orderData['order_date'] = now(); // Set order date

            $productOrder = ProductOrder::create($orderData);

            // Create the Order Items associated with the order
            $productOrder->items()->createMany($orderItemsData);

            // Update product stock quantities
            foreach ($productsToUpdateStock as $item) {
                 $item['product']->decrement('stock_quantity', $item['quantity']);
            }

            // Clear the user's shopping cart after placing the order
            $user->shoppingCartItems()->delete();

            // Note: Payment processing integration would go here or in a separate step after order is confirmed.
            // The payment_transaction_id and payment_status would be updated after successful payment.


            DB::commit(); // Commit transaction

            // Load relationships for the resource response
            $productOrder->load(['user.profile', 'items.product']);

            // Return the created order using ProductOrderResource
            return new ProductOrderResource($productOrder);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            Log::error('Error creating product order: ' . $e->getMessage(), ['user_id' => $userId, 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to place order. Please try again.'], 500);
        }
    }

    /**
     * Display a specific product order for the authenticated user.
     * Accessible at GET /api/my-orders/{productOrder}
     * Requires authentication and authorization (user owns the order).
     *
     * @param  \App\Models\ProductOrder  $productOrder // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(ProductOrder $productOrder)
    {
         // Authentication check handled by middleware

         // Authorization: Ensure the authenticated user owns this order
         if ($productOrder->user_id !== Auth::id()) {
              throw new AuthorizationException('You do not own this order.');
              // Or return a JSON error response:
              // return response()->json(['message' => 'You are not authorized to view this order.'], 403); // 403 Forbidden
         }

         // Load relationships needed for the resource response
        $productOrder->load(['user.profile', 'items.product']); // Load user with profile, items with product

        // Return the single order using ProductOrderResource
        return new ProductOrderResource($productOrder);
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /api/my-orders/{productOrder}
     * Note: Updating an order (items, quantity, total) is complex and often not allowed via API.
     * Status updates (e.g., Cancel) might be a separate action handled by Admin/Vendor.
     *
     * @param  \Illuminate\Http\Request  $request // Using base Request
     * @param  \App\Models\ProductOrder  $productOrder // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ProductOrder $productOrder)
    {
        // Users typically cannot update orders via API.
        // Admins/Vendors would have separate endpoints for status updates etc.
        return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /api/my-orders/{productOrder}
     * Note: Deleting an order is usually not allowed after placement.
     * Cancellation is a status update ('CancelledByUser').
     *
     * @param  \App\Models\ProductOrder  $productOrder // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProductOrder $productOrder) // Using base Request
    {
         // Users typically cannot delete orders. They can cancel them (status update).
         return response()->json(['message' => 'Method Not Allowed'], 405);
    }

    // --- Custom Method for Cancellation (Optional) ---
    // If you want users to cancel their orders:
    // public function cancel(Request $request, ProductOrder $productOrder) { ... authorization and status update logic ... }
    // Accessible via a custom route like POST /api/my-orders/{productOrder}/cancel
}