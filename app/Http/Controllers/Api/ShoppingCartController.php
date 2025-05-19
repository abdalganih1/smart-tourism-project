<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingCartItem; // Import ShoppingCartItem model
use App\Models\Product; // Import Product model (needed for add/update logic)
use App\Http\Requests\Api\AddToCartRequest; // Import custom Add Request
use App\Http\Requests\Api\UpdateCartItemRequest; // Import custom Update Request
use App\Http\Resources\ShoppingCartItemResource; // Import ShoppingCartItem Resource
use Illuminate\Support\Facades\Auth; // For authentication
use Illuminate\Support\Facades\Log; // For logging


class ShoppingCartController extends Controller
{
    /**
     * Display a listing of the authenticated user's shopping cart items.
     * Accessible at GET /api/cart
     * Requires authentication.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Authentication check handled by 'auth:sanctum' middleware

        // Fetch the authenticated user's shopping cart items
        // Eager load the product relationship for each item
        $cartItems = Auth::user()->shoppingCartItems()->with('product')->get(); // Usually no pagination for cart

        // Return the collection using ShoppingCartItemResource
        return ShoppingCartItemResource::collection($cartItems);
    }

    /**
     * Add an item to the authenticated user's shopping cart.
     * Accessible at POST /api/cart/add (Custom route name suggested)
     * Requires authentication.
     *
     * @param  \App\Http\Requests\Api\AddToCartRequest  $request // Use custom request for validation
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\ShoppingCartItemResource
     */
    public function store(AddToCartRequest $request) // Using 'store' method name for resource convention on a custom route
    {
        // Validation is handled by AddToCartRequest (includes product existence check)
        // Authentication check handled by 'auth:sanctum' middleware

        $userId = Auth::id();
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // --- Business Logic ---
        // Find if the item is already in the cart
        $existingCartItem = ShoppingCartItem::where('user_id', $userId)
                                           ->where('product_id', $productId)
                                           ->first();

        try {
            if ($existingCartItem) {
                // If item exists, update the quantity
                $existingCartItem->increment('quantity', $quantity);
                 // Reload the item after update to get the new quantity
                 $existingCartItem->refresh()->load('product');
                 $message = 'Product quantity updated in cart.';
            } else {
                // If item does not exist, create a new cart item
                $newCartItem = ShoppingCartItem::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    // added_at is set by database default timestamp
                ]);
                 // Load product relationship for the new item
                 $newCartItem->load('product');
                 $existingCartItem = $newCartItem; // Use the new item for the response
                 $message = 'Product added to cart.';
            }

            // Return the updated/created cart item using ShoppingCartItemResource
            return response()->json([
                'message' => $message,
                'cart_item' => new ShoppingCartItemResource($existingCartItem),
            ], $existingCartItem ? 200 : 201); // 200 OK for update, 201 Created for new

        } catch (\Exception $e) {
            Log::error('Error adding/updating cart item: ' . $e->getMessage(), ['user_id' => $userId, 'product_id' => $productId, 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to add product to cart. Please try again.'], 500);
        }
    }

    /**
     * Display the specified resource (less common for cart items).
     * Accessible at GET /api/cart/{cartItem}
     * Requires authentication and authorization (user owns the cart item).
     *
     * @param  \App\Models\ShoppingCartItem  $cartItem // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function show(ShoppingCartItem $cartItem)
    {
         // Authentication check handled by middleware

         // Authorization: Ensure the authenticated user owns this cart item
         if ($cartItem->user_id !== Auth::id()) {
              throw new AuthorizationException('You do not own this cart item.');
              // Or return a JSON error response:
              // return response()->json(['message' => 'You are not authorized to view this cart item.'], 403); // 403 Forbidden
         }

         // Load relationships needed for the resource response
        $cartItem->load('product');

        // Return the single cart item using ShoppingCartItemResource
        return new ShoppingCartItemResource($cartItem);
    }


    /**
     * Update the specified resource in storage (Update quantity).
     * Accessible at PUT/PATCH /api/cart/{cartItem}
     * Requires authentication and authorization (user owns the cart item).
     *
     * @param  \App\Http\Requests\Api\UpdateCartItemRequest  $request // Use custom request for validation
     * @param  \App\Models\ShoppingCartItem  $cartItem // Route Model Binding
     * @return \App\Http\Resources\Json\JsonResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateCartItemRequest $request, ShoppingCartItem $cartItem)
    {
         // Validation is handled by UpdateCartItemRequest (includes quantity check >= 1)
         // Authentication check handled by middleware
         // Authorization (user owns cart item) check handled by UpdateCartItemRequest authorize() method

        try {
             // Update the quantity
            $cartItem->update($request->only('quantity'));

             // Load product relationship after update
             $cartItem->load('product');

            // Return the updated cart item using ShoppingCartItemResource
            return new ShoppingCartItemResource($cartItem);

        } catch (\Exception $e) {
             Log::error('Error updating cart item quantity: ' . $e->getMessage(), ['cart_item_id' => $cartItem->id, 'user_id' => Auth::id(), 'request_data' => $request->all(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to update cart item quantity. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /api/cart/{cartItem}
     * Requires authentication and authorization (user owns the cart item).
     *
     * @param  \App\Models\ShoppingCartItem  $cartItem // Route Model Binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ShoppingCartItem $cartItem)
    {
        // Authentication check handled by middleware
        // Authorization (user owns cart item) check handled implicitly by route model binding and can be explicitly checked here
        if ($cartItem->user_id !== Auth::id()) {
             throw new AuthorizationException('You do not own this cart item.');
        }

        try {
            $cartItem->delete();

            // Return a success response
            return response()->json(['message' => 'Item removed from cart.'], 200); // Using 200 with message

        } catch (\Exception $e) {
             Log::error('Error removing cart item: ' . $e->getMessage(), ['cart_item_id' => $cartItem->id, 'user_id' => Auth::id(), 'exception' => $e]);
            // Return a generic error response
            return response()->json(['message' => 'Failed to remove item from cart. Please try again.'], 500);
        }
    }

    /**
     * Clear the authenticated user's entire shopping cart.
     * Accessible at POST /api/cart/clear (Custom route suggested)
     * Requires authentication.
     *
     * @return \Illuminate\Http\JsonResponse
     */
     public function clearCart()
     {
         // Authentication check handled by 'auth:sanctum' middleware

         try {
             Auth::user()->shoppingCartItems()->delete();

             return response()->json(['message' => 'Shopping cart cleared.'], 200);

         } catch (\Exception $e) {
             Log::error('Error clearing shopping cart: ' . $e->getMessage(), ['user_id' => Auth::id(), 'exception' => $e]);
             return response()->json(['message' => 'Failed to clear shopping cart. Please try again.'], 500);
         }
     }
}