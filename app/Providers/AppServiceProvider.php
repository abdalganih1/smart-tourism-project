<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate; // Import Gate Facade
use App\Models\User; // Import User model
use Illuminate\Database\Eloquent\Relations\Relation; // Import Relation facade

class AppServiceProvider extends ServiceProvider
{

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
Relation::morphMap([
            'TouristSite' => \App\Models\TouristSite::class,
            'Product' => \App\Models\Product::class,
            'Article' => \App\Models\Article::class,
            'Hotel' => \App\Models\Hotel::class,
            'SiteExperience' => \App\Models\SiteExperience::class,
            // Add any other models that will be targets of polymorphic relations
            // Key should be the exact string you pass in 'target_type'
            // Value should be the full class name of the model
        ]);

        $this->registerPolicies();

        // Define a gate for accessing the admin panel
        Gate::define('access-admin-panel', function (User $user) {
            return $user->user_type === 'Admin';
        });
                Gate::define('access-hotelmanager-panel', function (User $user) {
            return $user->isHotelBookingManager(); // Use the User model method
        });

        Gate::define('access-vendor-panel', function (User $user) {
            return $user->isVendor(); // Use the User model method
        });

        // You can define other gates as needed, potentially combining roles
        Gate::define('access-content-management', function (User $user) {
            return $user->isAdmin() || $user->isArticleWriter() || $user->isEmployee();
        });
         Gate::define('access-financial-data', function (User $user) {
            return $user->isAdmin() || $user->isEmployee(); // Example for who sees finances
        });
        // You can define more gates for specific roles/permissions if needed
        // Gate::define('isVendor', function (User $user) { return $user->user_type === 'Vendor'; });
        // Gate::define('isHotelBookingManager', function (User $user) { return $user->user_type === 'HotelBookingManager'; });
        // ...
    }
}
