<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use Exception;

class AuthController extends Controller {
    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',    
                'password' => 'required|string|min:6|confirmed',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::error('Validation error.', 422, $validator->errors()->toArray());
            }

            // Create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Return success response
            return ApiResponse::success('User registered.', ['user' => $user], 201);
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::error('Validation error.', 422, $validator->errors()->toArray());
            }

            // Extract credentials
            $credentials = $request->only('email', 'password');

            // Attempt authentication
            if (!Auth::attempt($credentials)) {
                return ApiResponse::error('Invalid credentials.', 401);
            }

            // Generate and return access token
            $token = $request->user()->createToken('API Token')->accessToken;
            return ApiResponse::success('Login successful.', ['token' => $token]);
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Login failed: ' . $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke the user's token
            $request->user()->token()->revoke();
            return ApiResponse::success('Logout successful.');
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Logout failed: ' . $e->getMessage(), 500);
        }
    }
}