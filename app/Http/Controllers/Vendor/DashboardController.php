<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product; // Import models
use App\Models\ProductOrder;
use App\Models\ProductOrderItem;

class DashboardController extends Controller
{
    /**
     * Display the vendor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get products listed by this user
        $userProducts = $user->products;

        // Fetch counts or data relevant to this vendor
        $productCount = $userProducts->count();

        // Get order items for this vendor's products
        $vendorOrderItems = ProductOrderItem::whereIn('product_id', $userProducts->pluck('id'))->get();

        // Count total orders containing this vendor's products (might overcount if multiple products in one order)
        // A better approach is to count distinct order_ids from vendorOrderItems
        $totalOrdersWithVendorProductsCount = $vendorOrderItems->pluck('order_id')->unique()->count();

        // Count pending orders containing this vendor's products
         $pendingOrdersWithVendorProductsCount = ProductOrder::whereIn('id', $vendorOrderItems->pluck('order_id')->unique())
             ->where('order_status', 'Pending')
             ->count();


        return view('vendor.dashboard', compact('userProducts', 'productCount', 'totalOrdersWithVendorProductsCount', 'pendingOrdersWithVendorProductsCount'));
    }
}