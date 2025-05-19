<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\TouristSite; // Import TouristSite model
use App\Models\Hotel; // Import Hotel model
use App\Models\Article; // Import Article model
use App\Models\HotelBooking; // Import HotelBooking model
use App\Models\ProductCategory; // Import ProductCategory model
use App\Models\SiteCategory; // Import SiteCategory model


class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch counts for all relevant models to display on the dashboard
        $userCount = User::count();
        $productCount = Product::count();
        $pendingOrdersCount = ProductOrder::where('order_status', 'Pending')->count();
        $touristSiteCount = TouristSite::count(); // Get count for Tourist Sites
        $hotelCount = Hotel::count(); // Get count for Hotels
        $articleCount = Article::where('status', 'Published')->count(); // Get count for published Articles
        $hotelBookingCount = HotelBooking::count(); // Get count for Hotel Bookings
        $productCategoryCount = ProductCategory::count(); // Get count for Product Categories
        $siteCategoryCount = SiteCategory::count(); // Get count for Site Categories
        // Note: You might add counts for Room Types, Rooms, Site Experiences, etc.


        // Pass all counts to the view
        return view('admin.dashboard', compact(
            'userCount',
            'productCount',
            'pendingOrdersCount',
            'touristSiteCount', // Pass new counts
            'hotelCount',
            'articleCount',
            'hotelBookingCount',
            'productCategoryCount',
            'siteCategoryCount'
        ));
    }
}