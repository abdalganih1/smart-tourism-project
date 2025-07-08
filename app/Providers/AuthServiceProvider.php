<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Product;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelBooking;
use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use Illuminate\Support\Facades\Log; // تأكد من استيراد Log إذا كنت تستخدمه للتسجيل داخل Gates

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // ... (Policies registrations) ...
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // --- Gates for Panel Access ---
        Gate::define('access-admin-panel', function (User $user) { return $user->isAdmin(); });
        Gate::define('access-hotelmanager-panel', function (User $user) { return $user->isHotelBookingManager(); });
        Gate::define('access-vendor-panel', function (User $user) { return $user->isVendor(); });
        // ... other panel access gates ...

        // --- Gates for Resource Management (Ownership/Management Checks) ---

        Gate::define('manage-product', function (User $user, Product $product) {
            return $user->isVendor() && (int) $user->id === (int) $product->seller_user_id;
        });

        Gate::define('view-vendor-order', function (User $user, ProductOrder $order) {
            if (!$user->isVendor()) {
                return false;
            }
            $order->load('items');
            $vendorProductIds = $user->products->pluck('id');
            foreach ($order->items as $item) {
                if ($vendorProductIds->contains($item->product_id)) {
                    return true;
                }
            }
            return false;
        });

        Gate::define('manage-hotel', function (User $user, Hotel $hotel) {
             // تحقق أن managed_by_user_id مُحمّل كخاصية على نموذج Hotel
            return $user->isHotelBookingManager() && $hotel->managed_by_user_id !== null && (int) $user->id === (int) $hotel->managed_by_user_id;
        });

        // Hotel Manager can manage rooms within hotels assigned to them
        // ** تعريف واحد وصحيح لـ manage-hotel-room Gate **
        Gate::define('manage-hotel-room', function (User $user, HotelRoom $room) {
            // التحقق من أن المستخدم مدير فندق
            if (!$user->isHotelBookingManager()) {
                return false;
            }
            // ** التأكد من تحميل علاقة الفندق على الغرفة **
             if (!$room->relationLoaded('hotel')) {
                 $room->load('hotel');
             }

            // التحقق من أن الغرفة مرتبطة بفندق وأن الفندق مرتبط بالمدير الحالي
            // $room->hotel يجب أن لا يكون null
            return $room->hotel !== null && $room->hotel->managed_by_user_id !== null && (int) $user->id === (int) $room->hotel->managed_by_user_id;
        });

        // Hotel Manager can manage bookings for rooms within hotels assigned to them
        // ** تعريف واحد وصحيح لـ manage-hotel-booking Gate **
        Gate::define('manage-hotel-booking', function (User $user, HotelBooking $booking) {
            // التحقق من أن المستخدم مدير فندق
             if (!$user->isHotelBookingManager()) {
                 return false;
             }
            // ** التأكد من تحميل علاقة الغرفة والفندق **
             if (!$booking->relationLoaded('room') || ($booking->room && !$booking->room->relationLoaded('hotel'))) {
                  $booking->load('room.hotel');
             }

            // التحقق من أن الحجز مرتبط بغرفة، والغرفة مرتبطة بفندق، والفندق مرتبط بالمدير الحالي
            return $booking->room !== null && $booking->room->hotel !== null && (int) $user->id === (int) $booking->room->hotel->managed_by_user_id;
        });
        
    }
}