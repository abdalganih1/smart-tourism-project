<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response; // Recommended to import Response for clearer authorization results

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
         // Only Admins and Vendors can view product listings in their respective panels
        return $user->isAdmin() || $user->isVendor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        // Admins can view any product
        if ($user->isAdmin()) {
            return true;
        }

        // Vendors can view their own products
        if ($user->isVendor() && $user->id === $product->seller_user_id) {
            return true;
        }

        // Tourists and others can view published products (public API)
        // This policy is for the Admin/Vendor panels, so this part might be different
        // For Admin/Vendor panels, maybe only allow viewing if they can manage it?
        // Let's restrict view in Admin/Vendor panels to those who can manage.
         return false; // Deny by default
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         // Only Admins and Vendors can create products via their panels
        return $user->isAdmin() || $user->isVendor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
         // Admins can update any product
        if ($user->isAdmin()) {
            return true;
        }

        // Vendors can update their own products
        if ($user->isVendor() && $user->id === $product->seller_user_id) {
            return true;
        }

        return false; // Deny if not admin or owner vendor
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // Admins can delete any product
        if ($user->isAdmin()) {
            return true;
        }

        // Vendors can delete their own products
        if ($user->isVendor() && $user->id === $product->seller_user_id) {
            return true;
        }

        return false; // Deny
    }

    // Add methods for restore and forceDelete if using soft deletes

    /**
     * Determine whether the user can bypass policy checks (e.g., for Super Admins).
     * Optional method, if present, it runs before all other policy methods.
     */
    // public function before(User $user, string $ability)
    // {
    //      if ($user->user_type === 'SuperAdmin') { // If you had a SuperAdmin type
    //          return true; // SuperAdmins can do anything
    //      }
    // }
}