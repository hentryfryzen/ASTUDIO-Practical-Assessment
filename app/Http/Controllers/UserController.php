<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            // Fetch all users
            $users = User::all();
            return ApiResponse::success('Users retrieved successfully.', compact('users'));
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to retrieve users: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return ApiResponse::success('User retrieved successfully.', compact('user'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::error('User not found.', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Failed to retrieve user details: ' . $e->getMessage(), 500);
        }
    }
    
    

    public function update(Request $request, User $user)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|required|string|min:6|confirmed',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::error('Validation error.', 422, $validator->errors()->toArray());
            }

            // Update the user
            $user->update($request->only(['first_name', 'last_name', 'email', 'password']));
            return ApiResponse::success('User updated successfully.', compact('user'));
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to update user: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            // Delete the user
            $user->delete();
            return ApiResponse::success('User deleted successfully.');
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to delete user: ' . $e->getMessage(), 500);
        }
    }
}