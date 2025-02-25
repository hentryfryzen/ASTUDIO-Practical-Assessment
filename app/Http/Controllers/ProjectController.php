<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\{User, Project, Attribute, AttributeValue};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Validator};
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with('attributeValues.attribute')
            ->when($request->input('filters', []), fn($query) => $this->applyFilters($query, $request->input('filters')))
            ->get();
    
        return ApiResponse::success('Projects fetched', compact('projects'));
    }
    
    private function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $filter) {
            [$operator, $value] = is_array($filter) 
                ? [$filter['operator'] ?? '=', $filter['value'] ?? null] 
                : ['=', $filter];
    
            in_array($field, ['name', 'status'])
                ? $this->applyRegularFilter($query, $field, $operator, $value)
                : $this->applyEavFilter($query, $field, $operator, $value);
        }
    }
    
    private function applyRegularFilter($query, string $field, string $operator, $value): void
    {
        $operator = strtoupper($operator);
        $operator === 'LIKE'
            ? $query->where($field, 'LIKE', "%$value%")
            : $query->where($field, $operator, $value);
    }
    
    private function applyEavFilter($query, string $attributeName, string $operator, $value): void
    {
        $operator = strtoupper($operator);
    
        $query->whereHas('attributeValues', function ($attrValueQuery) use ($attributeName, $operator, $value) {
            $attrValueQuery->whereHas('attribute', fn($attributeQuery) => 
                $attributeQuery->where('name', $attributeName)
            );
    
            if ($operator === 'LIKE') {
                $attrValueQuery->where('value', 'LIKE', "%$value%");
            } elseif (in_array($operator, ['>', '<', '>=', '<='])) {
                // Check if value is date
                $isDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    
                $attrValueQuery->whereRaw(
                    $isDate ? "STR_TO_DATE(value, '%Y-%m-%d') $operator ?" : "CAST(value AS DECIMAL) $operator ?",
                    [$value]
                );
            } else {
                $attrValueQuery->where('value', $operator, $value);
            }
        });
    }
    

    public function show($id)
    {
        $project = Project::find($id);  // Returns null if the project is not found
    
        if (!$project) {
            return ApiResponse::error('Project not found', 404);
        }
        return ApiResponse::success('Project details', [$project->load([ 'attributeValues.attribute'])]);
    }
    

    
    public function destroy(Project $project)
    {
        return DB::transaction(function () use ($project) {
            $project->attributeValues()->delete();
            $project->delete();
            return ApiResponse::success('Project deleted successfully');
        }, 5);
    }

    public function setAttributes(Request $request, $projectId)
    {
        try {
            $validated = $request->validate([
                'attribute_id' => 'required|exists:attributes,id',
                'value' => 'required|string',
            ], [
                'attribute_id.required' => 'Attribute ID is required.',
                'attribute_id.exists' => 'The selected attribute does not exist.',
                'value.required' => 'Value is required.',
                'value.string' => 'Value must be a string.',
            ]);
    
            $project = Project::findOrFail($projectId);
            $attribute = Attribute::findOrFail($validated['attribute_id']);
    
            $attributeValue = AttributeValue::updateOrCreate(
                [
                    'attribute_id' => $attribute->id,
                    'entity_id' => $project->id,
                ],
                [
                    'value' => $validated['value'],
                ]
            );
    
            return ApiResponse::success('Attribute set successfully', [
                'attributeValue' => $attributeValue->load('attribute'),
                'project' => $project->load('attributeValues.attribute'),
            ]);
        } catch (ValidationException $e) {
            return ApiResponse::error('Invalid data provided.', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            return ApiResponse::error('An unexpected error occurred.', 500, ['error' => $e->getMessage()]);
        }
    }
    
    

    private function syncAttributes(Project $project, array $attributes): void
    {
        foreach ($attributes as $attr) {
            AttributeValue::updateOrCreate(
                [
                    'entity_id' => $project->id,
                    'attribute_id' => $attr['attribute_id'],
                ],
                [
                    'value' => $attr['value'],
                ]
            );
        }
    }    

    public function filter(Request $request)
    {
        $projects = Project::with('attributeValues.attribute')
            ->when($request->query('filters', []), fn($query) => $this->applyFilters($query, $request->query('filters')))
            ->get();

        return ApiResponse::success('Filtered projects', compact('projects'));
    }

    // Store Method
    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'name' => 'required|string|unique:projects,name',
                'status' => 'required|string',
                'users' => 'array',
                'users.*.id' => 'required|exists:users,id',
                'attributes' => 'array',
                'attributes.*.attribute_id' => 'required|exists:attributes,id',
                'attributes.*.value' => 'required|string',
            ]);

            // Start a transaction to ensure data integrity
            return DB::transaction(function () use ($validated) {
                // Create the project
                $project = Project::create([
                    'name' => $validated['name'],
                    'status' => $validated['status'],
                ]);

                // Attach users to the project (many-to-many relationship)
                if (!empty($validated['users'])) {
                    $userIds = array_column($validated['users'], 'id');
                    $project->users()->attach($userIds);
                }

                // If attributes are provided, sync them to the project
                if (!empty($validated['attributes'])) {
                    $this->syncAttributesStore($project, $validated['attributes']);
                }

                // Return success response
                return ApiResponse::success('Project created successfully', [$project->load('attributeValues.attribute')], 201);
            }, 5);

        } catch (QueryException $e) {
            // Handle duplicate entry error (unique constraint violation)
            if ($e->getCode() == 23000) {
                return ApiResponse::error('Duplicate project name detected. Please choose a unique name.', 400);
            }

            // Handle any other database-related errors
            return ApiResponse::error('An error occurred while creating the project. Please try again.', 500, ['error' => $e->getMessage()]);
        } catch (ValidationException $e) {
            // Handle validation errors
            return ApiResponse::error('Invalid data provided. Please check your input and try again.', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return ApiResponse::error('An unexpected error occurred. Please try again later.', 500, ['error' => $e->getMessage()]);
        }
    }

    // Update Method
    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'name' => 'required|string|unique:projects,name,' . $id,
                'status' => 'required|string',
                'users' => 'array',
                'users.*.id' => 'required|exists:users,id',
                'attributes' => 'array',
                'attributes.*.attribute_id' => 'required|exists:attributes,id',
                'attributes.*.value' => 'required|string',
            ]);

            // Start a transaction to ensure data integrity
            return DB::transaction(function () use ($validated, $id) {
                // Find the project by ID
                $project = Project::findOrFail($id);

                // Update the project details
                $project->update([
                    'name' => $validated['name'],
                    'status' => $validated['status'],
                ]);

                // Sync the users for the project (many-to-many relationship)
                if (!empty($validated['users'])) {
                    $userIds = array_column($validated['users'], 'id');
                    $project->users()->sync($userIds); // Use sync to update users
                }

                // Sync attributes for the project
                if (!empty($validated['attributes'])) {
                    $this->syncAttributesStore($project, $validated['attributes']);
                }

                // Return success response
                return ApiResponse::success('Project updated successfully', [$project->load('attributeValues.attribute')], 200);
            }, 5);

        } catch (QueryException $e) {
            // Handle database errors
            if ($e->getCode() == 23000) {
                return ApiResponse::error('Duplicate project name detected. Please choose a unique name.', 400);
            }
            return ApiResponse::error('An error occurred while updating the project. Please try again.', 500, ['error' => $e->getMessage()]);
        } catch (ValidationException $e) {
            // Handle validation errors
            return ApiResponse::error('Invalid data provided. Please check your input and try again.', 422, ['errors' => $e->errors()]);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return ApiResponse::error('An unexpected error occurred. Please try again later.', 500, ['error' => $e->getMessage()]);
        }
    }

    // Sync attributes when storing or updating project
    private function syncAttributesStore(Project $project, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!Attribute::find($attribute['attribute_id'])) {
                throw new Exception('Invalid attribute ID provided.');
            }

            // Sync attribute values
            AttributeValue::updateOrCreate(
                ['attribute_id' => $attribute['attribute_id'], 'entity_id' => $project->id],
                ['value' => $attribute['value']]
            );
        }
    }
    public function assignUserToProject(Request $request, $projectId)
    {
        try{
        $messages = [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists'   => 'The selected user does not exist.',
        ];
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ], $messages);
    
        $project = Project::findOrFail($projectId);
    
        $userId = $validated['user_id'];
    
        if ($project->users()->where('user_id', $userId)->exists()) {
            DB::rollBack();
            return ApiResponse::conflict('User is already assigned to this project.');
        }
        $project->users()->attach($userId);
     } catch (\Exception $e) {
        DB::rollBack();
        return ApiResponse::error('An error occurred while assigning the user.', $e->getMessage());
    }
}
    
    
}
