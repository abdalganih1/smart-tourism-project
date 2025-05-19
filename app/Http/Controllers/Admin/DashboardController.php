<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import models to fetch counts
use App\Models\Product;
use App\Models\ProductOrder;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch some data to display on the dashboard
        $userCount = User::count();
        $productCount = Product::count();
        $pendingOrdersCount = ProductOrder::where('order_status', 'Pending')->count();
        // Add counts for TouristSites, Hotels, Articles, etc. if needed

        // Pass the data to the view
        return view('admin.dashboard', compact('userCount', 'productCount', 'pendingOrdersCount'));
    }
}