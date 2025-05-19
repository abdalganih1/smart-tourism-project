<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Import the User model
use App\Models\UserProfile; // Import UserProfile model
use App\Models\UserPhoneNumber; // Import UserPhoneNumber model
use App\Http\Requests\Admin\StoreUserRequest; // Import custom Store Request
use App\Http\Requests\Admin\UpdateUserRequest; // Import custom Update Request
use Illuminate\Support\Facades\Hash; // To hash passwords
use Illuminate\Support\Facades\DB; // For database transactions
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Auth; // To check the authenticated user


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible at GET /admin/users
     */
    public function index()
    {
        // Fetch all users with their profile and phone numbers relationships, paginated
        $users = User::with(['profile', 'phoneNumbers'])
                     ->orderBy('created_at', 'desc') // Order by creation date
                     ->paginate(15); // Paginate results

        // Return the view and pass the users data
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     * Accessible at GET /admin/users/create
     */
    public function create()
    {
        // Return the view for creating a user
        // We might pass data like available user types if they are not hardcoded
        $userTypes = ['Tourist', 'Vendor', 'HotelBookingManager', 'ArticleWriter', 'Employee', 'Admin']; // Example list of user types
        return view('admin.users.create', compact('userTypes'));
    }

    /**
     * Store a newly created resource in storage.
     * Accessible at POST /admin/users
     *
     * @param  \App\Http\Requests\Admin\StoreUserRequest  $request // Use custom request for validation
     */
    public function store(StoreUserRequest $request)
    {
        // Validation is handled by StoreUserRequest

        // Use a database transaction for atomic creation of User and Profile/Phone
        DB::beginTransaction();

        try {
            // Create the User
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password
                'user_type' => $request->user_type ?? 'Tourist', // Default if not provided
                'is_active' => $request->is_active ?? true, // Default if not provided
            ]);

            // Create the associated User Profile (assuming profile data is in the request)
            UserProfile::create([
                'user_id' => $user->id, // Link to the created user
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                // passport_image_url and profile_picture_url likely need separate file upload logic
                'bio' => $request->bio,
            ]);

            // Add phone numbers if provided (assuming they come as an array in the request)
            if ($request->has('phone_numbers') && is_array($request->phone_numbers)) {
                 foreach ($request->phone_numbers as $phoneNumberData) {
                     // Basic validation for phone number data structure
                     if (isset($phoneNumberData['phone_number'])) {
                          $user->phoneNumbers()->create([
                              'phone_number' => $phoneNumberData['phone_number'],
                              'is_primary' => $phoneNumberData['is_primary'] ?? false,
                              'description' => $phoneNumberData['description'] ?? null,
                          ]);
                     }
                 }
            }


            DB::commit(); // Commit the transaction

            // Redirect with a success message
            return redirect()->route('admin.users.index')->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction on error
            Log::error('Error creating user: ' . $e->getMessage(), ['exception' => $e]);
            // Redirect back with an error message
            return redirect()->back()->withInput()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Accessible at GET /admin/users/{user}
     *
     * @param  \App\Models\User  $user // Route Model Binding
     */
    public function show(User $user)
    {
        // Load relationships for the detailed view
        $user->load(['profile', 'phoneNumbers']);

        // Return the view and pass the user data
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     * Accessible at GET /admin/users/{user}/edit
     *
     * @param  \App\Models\User  $user // Route Model Binding
     */
    public function edit(User $user)
    {
        // Load relationships for the edit form
        $user->load(['profile', 'phoneNumbers']);

        $userTypes = ['Tourist', 'Vendor', 'HotelBookingManager', 'ArticleWriter', 'Employee', 'Admin']; // Example list of user types

        // Return the view and pass the user data and user types
        return view('admin.users.edit', compact('user', 'userTypes'));
    }

    /**
     * Update the specified resource in storage.
     * Accessible at PUT/PATCH /admin/users/{user}
     *
     * @param  \App\Http\Requests\Admin\UpdateUserRequest  $request // Use custom request for validation
     * @param  \App\Models\User  $user // Route Model Binding
     */
    public function update(UpdateUserRequest $request, User $user)
    {
         // Validation is handled by UpdateUserRequest

        // Use a database transaction
        DB::beginTransaction();

        try {
            // Prepare user data for update (excluding password if not provided)
            $userData = $request->only(['username', 'email', 'user_type', 'is_active']);
            
            Log::info('User update request data (before password check):', $userData);
            Log::info('Request has password field:', ['filled' => $request->filled('password'), 'value' => $request->input('password')]);

            // Handle password update only if a new password is provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
                Log::info('Password field was filled. Adding hashed password to update data.');
            } else {
                Log::info('Password field was NOT filled. Skipping password update.');
            }

            // Update the User
            $user->update($userData);

            // Update or Create User Profile (using updateOrCreate for simplicity)
            // Assumes profile data is in the request
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id], // Conditions to find the profile (should always match for 1-to-1)
                $request->only(['first_name', 'last_name', 'father_name', 'mother_name', 'bio'])
                // File uploads (passport/profile picture) need separate logic
            );

             // Handle Phone Numbers (more complex: add new, update existing, delete removed)
             // This is a basic example - needs refinement based on how you manage phone numbers in the form
             // A common approach: Clear existing phones and re-add, or send list of existing+new and sync
             // For simplicity here, let's just update the primary and add others (needs adaptation)

             // Example: Update primary phone number if exists and provided
             if ($request->has('primary_phone_number')) {
                  $primaryPhone = $user->phoneNumbers()->where('is_primary', true)->first();
                  if ($primaryPhone) {
                      $primaryPhone->update(['phone_number' => $request->primary_phone_number]);
                  } else {
                      // Create if no primary existed
                       $user->phoneNumbers()->create([
                           'phone_number' => $request->primary_phone_number,
                           'is_primary' => true,
                           'description' => 'Primary', // Default description
                       ]);
                  }
             }

             // A more robust phone number update strategy might involve
             // sending an array of phone numbers with IDs and status (add/update/delete)
             // and iterating through it here. This is just a placeholder.


            DB::commit(); // Commit transaction

            // Redirect with success message
            return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            Log::error('Error updating user: ' . $e->getMessage(), ['exception' => $e]);
            // Redirect back with error message
            return redirect()->back()->withInput()->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Accessible at DELETE /admin/users/{user}
     *
     * @param  \App\Models\User  $user // Route Model Binding
     */
    public function destroy(User $user)
    {
        // --- Authorization Check ---
        // Prevent deleting the currently logged-in user
        if (Auth::user()->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        // Optional: Prevent deleting the last admin user
        if ($user->user_type === 'Admin' && User::where('user_type', 'Admin')->count() <= 1) {
             return redirect()->route('admin.users.index')->with('error', 'Cannot delete the last Admin user.');
        }

        // Optional: Prevent deleting users with associated critical data (e.g., orders, products)
        // unless cascade delete is fully intended and verified.
        // Your migrations use 'restrict' or 'set null' for some relations, which helps prevent accidental deletion.
        // If a 'restrict' constraint fails, the delete() method will throw an exception.
        // You might catch specific exceptions or check for related records before deleting.
        // Example check (if migrations didn't handle it):
        // if ($user->productOrders()->count() > 0 || $user->products()->count() > 0) {
        //     return redirect()->route('admin.users.index')->with('error', 'Cannot delete user with associated orders or products.');
        // }


        // Use a transaction for deletion too if you have complex related data handling
        DB::beginTransaction();
        try {
             // The cascade deletes in migrations should handle profile and phone numbers
             $user->delete(); // This triggers deletion based on onDelete rules in migrations

             DB::commit(); // Commit transaction
             return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction
            Log::error('Error deleting user: ' . $e->getMessage(), ['exception' => $e]);
            // Catch specific Integrity Constraint Violation exceptions if you want more granular error messages
             if ($e instanceof \Illuminate\Database\QueryException && $e->getCode() === '23000') { // Example for integrity constraint violation
                  return redirect()->route('admin.users.index')->with('error', 'User cannot be deleted due to associated data (e.g., orders, products).');
             }
            return redirect()->route('admin.users.index')->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}