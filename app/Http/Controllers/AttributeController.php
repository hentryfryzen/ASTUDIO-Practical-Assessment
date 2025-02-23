<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ApiResponse;
use Exception;

class AttributeController extends Controller
{
    public function index()
    {
        try {
            // Fetch all attributes
            $attributes = Attribute::all();
            return ApiResponse::success('Attributes fetched successfully.', compact('attributes'));
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to fetch attributes: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:attributes,name',
                'type' => 'required|in:text,date,number,select',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::error('Validation error.', 422, $validator->errors()->toArray());
            }

            // Create the attribute
            $attribute = Attribute::create($request->only('name', 'type'));
            return ApiResponse::success('Attribute created successfully.', compact('attribute'), 201);
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to create attribute: ' . $e->getMessage(), 500);
        }
    }

    public function show(Attribute $attribute)
    {
        try {
            // Return attribute details
            return ApiResponse::success('Attribute details fetched successfully.', compact('attribute'));
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to fetch attribute details: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Attribute $attribute)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|unique:attributes,name,' . $attribute->id,
                'type' => 'sometimes|required|in:text,date,number,select',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::error('Validation error.', 422, $validator->errors()->toArray());
            }

            // Update the attribute
            $attribute->update($request->only('name', 'type'));
            return ApiResponse::success('Attribute updated successfully.', compact('attribute'));
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to update attribute: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Attribute $attribute)
    {
        try {
            // Delete the attribute
            $attribute->delete();
            return ApiResponse::success('Attribute deleted successfully.');
        } catch (Exception $e) {
            // Handle any unexpected errors
            return ApiResponse::error('Failed to delete attribute: ' . $e->getMessage(), 500);
        }
    }


}