<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use  HasFactory, Notifiable, HasApiTokens;

    // Primary key is 'id' by default, no need to specify $primaryKey

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password', // Using password as per your schema
        'user_type',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Uncomment if using email verification
        'password' => 'hashed', // Note: Laravel's default Auth expects 'password' column.
                                    // If keeping 'password', you might need custom logic
                                    // for login using Hash::check() or a custom Auth provider.
                                    // For simplicity with built-in auth features (like password reset),
                                    // renaming the column to 'password' in the migration is often easier.
                                    // Assuming you handle password verification manually for API.
        'is_active' => 'boolean',
    ];

    // Define relationships here

    public function profile()
    {
        // One-to-One relation where User has one profile. Profile table has user_id FK.
        // Laravel expects FK named user_profile_id by default, but your schema has user_id.
        // No need to specify 'user_id' explicitly if the FK in user_profiles table is named 'user_id'.
        // It's standard 'user_id' -> belongsTo(User), User hasOne -> belongsTo(UserProfile).
        return $this->hasOne(UserProfile::class); // Laravel infers FK name 'user_id' on UserProfiles
    }

    public function phoneNumbers()
    {
        // One-to-Many relation where User has many phone numbers. PhoneNumbers table has user_id FK.
        return $this->hasMany(UserPhoneNumber::class); // Laravel infers FK name 'user_id' on UserPhoneNumbers
    }

    public function products()
    {
        // One-to-Many relation where User (as seller/vendor) has many products. Products table has seller_user_id FK.
        return $this->hasMany(Product::class, 'seller_user_id'); // Specify FK name if not user_id
    }

    public function shoppingCartItems()
    {
        // One-to-Many relation where User has many cart items. ShoppingCartItems table has user_id FK.
         return $this->hasMany(ShoppingCartItem::class);
    }

    public function productOrders()
    {
        // One-to-Many relation where User has many product orders. ProductOrders table has user_id FK.
         return $this->hasMany(ProductOrder::class);
    }

    public function touristSitesAdded()
    {
        // One-to-Many relation where User added many sites. TouristSites table has added_by_user_id FK.
         return $this->hasMany(TouristSite::class, 'added_by_user_id');
    }

    public function touristActivitiesOrganized()
    {
        // One-to-Many relation where User organized many activities. TouristActivities table has organizer_user_id FK.
         return $this->hasMany(TouristActivity::class, 'organizer_user_id');
    }

     public function hotelsManaged()
    {
        // One-to-Many relation where User manages many hotels. Hotels table has managed_by_user_id FK.
         return $this->hasMany(Hotel::class, 'managed_by_user_id');
    }

     public function hotelBookings()
    {
        // One-to-Many relation where User has many hotel bookings. HotelBookings table has user_id FK.
         return $this->hasMany(HotelBooking::class);
    }

     public function siteExperiences()
    {
        // One-to-Many relation where User wrote many experiences. SiteExperiences table has user_id FK.
         return $this->hasMany(SiteExperience::class);
    }

     public function articlesAuthored()
    {
        // One-to-Many relation where User authored many articles. Articles table has author_user_id FK.
         return $this->hasMany(Article::class, 'author_user_id');
    }

    // Polymorphic relationships where User is the source of the action (Many-to-One polymorphic)
    // e.g., User has many Favorites (where Favorite's user_id is this user's id)
    public function favorites()
    {
        return $this->hasMany(Favorite::class); // Favorite model has user_id FK
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class); // Rating model has user_id FK
    }

    public function comments()
    {
        return $this->hasMany(Comment::class); // Comment model has user_id FK
    }

    // Accessors or methods for role checking
    public function isAdmin()
    {
        return $this->user_type === 'Admin';
    }

     public function isVendor()
    {
        return $this->user_type === 'Vendor';
    }

    public function isTourist()
    {
        return $this->user_type === 'Tourist';
    }

     // ... methods for other user types
}