<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Import the User model
use App\Models\UserProfile; // Import UserProfile model (for registration)
use Illuminate\Support\Facades\Hash; // For manual password checking/hashing
use Illuminate\Support\Facades\Auth; // For Laravel's authentication attempt (optional, but good to have)
use Illuminate\Validation\ValidationException; // To throw validation errors
use App\Http\Requests\Auth\RegisterRequest; // Import your RegisterRequest
use App\Http\Requests\Auth\LoginRequest; // Import your LoginRequest
use App\Http\Resources\UserResource; // Optional: Use a UserResource for output

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        // Validation is handled by RegisterRequest

        // Create the user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password and save to 'password' column
            'user_type' => $request->user_type ?? 'Tourist', // Default to Tourist if not provided
            'is_active' => true, // Default to active
            // Ensure User model has these fields in $fillable
        ]);

        // Create the user profile (assuming it's mandatory after registration)
        // Get first_name and last_name from the registration request if provided, otherwise use placeholders
        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name ?? 'New',
            'last_name' => $request->last_name ?? 'User',
            // You might add other profile fields here if collected during registration
            // e.g., 'father_name' => $request->father_name,
        ]);


        // Create a Sanctum token for the new user
        $token = $user->createToken($request->device_name ?? 'api_token')->plainTextToken; // Use device name from request or default

        return response()->json([
            // Load the profile relationship before creating the resource
            'user' => new UserResource($user->load('profile')), // Use UserResource to format user data, include profile
            'token' => $token,
        ], 201); // 201 Created status
    }

    /**
     * Handle user login.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        // Validation is handled by LoginRequest

        // Attempt to find the user by username or email (depending on how you want to allow login)
        // 'login' is the field name in your LoginRequest, can be username or email
        $user = User::where('username', $request->login)
                    ->orWhere('email', $request->login)
                    ->first();

        // Check if user exists and if password is correct
        // Using Hash::check() to compare the plain password from request with the hashed password from DB ('password' column)
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Throw validation exception for consistent error response format
            throw ValidationException::withMessages([
                'login' => [__('auth.failed')], // Use Laravel's localization for error messages (add 'auth.failed' to lang files)
            ]);
        }

        // Check if user is active
         if (!$user->is_active) {
             throw ValidationException::withMessages([
                'login' => [__('Your account is inactive. Please contact support.')], // Custom message
            ]);
         }

        // Authentication successful, issue a new token
        // Optional: Revoke old tokens if you want only one active token per device
        // $user->tokens()->where('name', $request->device_name ?? 'api_token')->delete();

        $token = $user->createToken($request->device_name ?? 'api_token')->plainTextToken;

        return response()->json([
             // Load the profile relationship before creating the resource
            'user' => new UserResource($user->load('profile')), // Use UserResource, include profile
            'token' => $token,
        ]);
    }

    /**
     * Handle user logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the current user's token
        // The 'auth:sanctum' middleware makes $request->user() available
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // The '/user' endpoint is often a simple route closure in api.php:
    // Route::middleware('auth:sanctum')->get('/user', function (Request $request) { return new UserResource($request->user()->load('profile')); });
    // If you prefer a controller method for '/user' endpoint:
    // public function authenticatedUser(Request $request)
    // {
    //    return new UserResource($request->user()->load('profile'));
    // }
}