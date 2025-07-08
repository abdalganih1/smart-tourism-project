<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    // Primary key is 'id' by default, which is standard and recommended.
    // No need to specify $primaryKey unless it's not 'id'.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password', // Assuming migration column name is 'password'
        'user_type',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', // Always hide password hashes from API responses
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Uncomment if you are using email verification
        'password' => 'hashed', // Automatically hash passwords when set
        'is_active' => 'boolean', // Cast 0/1 to false/true
    ];

    // --- Relationships ---

    /**
     * Get the profile associated with the user. (One-to-One)
     */
    public function profile()
    {
        // Explicitly defining the foreign key ('user_id' on the user_profiles table)
        // and the local key ('id' on the users table) resolves the 'Unknown column 'id' in where clause' error.
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    /**
     * Get the phone numbers for the user. (One-to-Many)
     */
    public function phoneNumbers()
    {
        // Laravel correctly infers the foreign key 'user_id' here.
        return $this->hasMany(UserPhoneNumber::class);
    }

    /**
     * Get the products listed by the user (if they are a vendor). (One-to-Many)
     */
    public function products()
    {
        // We must specify the foreign key 'seller_user_id' because it's not the conventional 'user_id'.
        return $this->hasMany(Product::class, 'seller_user_id');
    }

    /**
     * Get the shopping cart items for the user. (One-to-Many)
     */
    public function shoppingCartItems()
    {
        return $this->hasMany(ShoppingCartItem::class);
    }

    /**
     * Get the product orders for the user. (One-to-Many)
     */
    public function productOrders()
    {
        return $this->hasMany(ProductOrder::class);
    }

    /**
     * Get the tourist sites added by the user. (One-to-Many)
     */
    public function touristSitesAdded()
    {
        // Specify the foreign key 'added_by_user_id'
        return $this->hasMany(TouristSite::class, 'added_by_user_id');
    }

    /**
     * Get the tourist activities organized by the user. (One-to-Many)
     */
    public function touristActivitiesOrganized()
    {
        // Specify the foreign key 'organizer_user_id'
        return $this->hasMany(TouristActivity::class, 'organizer_user_id');
    }

    /**
     * Get the hotels managed by the user. (One-to-Many)
     */
     public function hotelsManaged()
    {
        // Specify the foreign key 'managed_by_user_id'
        return $this->hasMany(Hotel::class, 'managed_by_user_id');
    }

    /**
     * Get the hotel bookings made by the user. (One-to-Many)
     */
     public function hotelBookings()
    {
        return $this->hasMany(HotelBooking::class);
    }

    /**
     * Get the site experiences written by the user. (One-to-Many)
     */
     public function siteExperiences()
    {
        return $this->hasMany(SiteExperience::class);
    }

    /**
     * Get the articles authored by the user. (One-to-Many)
     */
     public function articlesAuthored()
    {
        // Specify the foreign key 'author_user_id'
        return $this->hasMany(Article::class, 'author_user_id');
    }

    // --- Polymorphic Relationships (where User is the source) ---

    /**
     * Get all of the user's favorites.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get all of the user's ratings.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get all of the user's comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    // --- Role & Permission Helpers ---

    /**
     * Check if the user is an Administrator.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'Admin';
    }

    /**
     * Check if the user is a Vendor.
     */
     public function isVendor(): bool
    {
        return $this->user_type === 'Vendor';
    }

    /**
     * Check if the user is a Tourist.
     */
    public function isTourist(): bool
    {
        return $this->user_type === 'Tourist';
    }

    /**
     * Check if the user is a Hotel Booking Manager.
     */
    public function isHotelBookingManager(): bool
    {
        return $this->user_type === 'HotelBookingManager';
    }

    /**
     * Check if the user is an Article Writer.
     */
     public function isArticleWriter(): bool
    {
        return $this->user_type === 'ArticleWriter';
    }

    /**
     * Check if the user is an Employee.
     */
     public function isEmployee(): bool
    {
        return $this->user_type === 'Employee';
    }

    /**
     * A generic method to check if the user has a specific role or one of several roles.
     * @param string|array $roles The role or roles to check against.
     * @return bool
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->user_type === $roles;
        }

        return in_array($this->user_type, $roles, true);
    }
}