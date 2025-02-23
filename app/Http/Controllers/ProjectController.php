<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\{User, Project, Timesheet, Attribute, AttributeValue};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Validator};
use Exception;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);

        $projects = Project::with('attributeValues.attribute')
            ->when($filters, fn($query) => $this->applyFilters($query, $filters))
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
        $query->where($field, strtoupper($operator) === 'LIKE' ? 'LIKE' : $operator, strtoupper($operator) === 'LIKE' ? "%$value%" : $value);
    }

    private function applyEavFilter($query, string $attributeName, string $operator, $value): void
    {
        $query->whereHas('attributeValues', function ($attrValueQuery) use ($attributeName, $operator, $value) {
            $attrValueQuery->whereHas('attribute', function ($attributeQuery) use ($attributeName) {
                $attributeQuery->where('name', $attributeName);
            })->whereRaw('CAST(value AS SIGNED) ' . $operator . ' ?', [$value]);
        });
    }
    
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'attributes' => 'array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $project = Project::create($validated);

            $this->syncAttributes($project, $validated['attributes'] ?? []);

            return ApiResponse::success('Project created', [$project->load('attributeValues.attribute')], 201);
        }, 5);
    }

    public function show(Project $project)
    {
        return ApiResponse::success('Project details', [$project->load(['users', 'attributeValues.attribute'])]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'status' => 'sometimes|string',
            'attributes' => 'array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required|string',
        ]);

        return DB::transaction(function () use ($project, $validated) {
            $project->update($validated);

            $this->syncAttributes($project, $validated['attributes'] ?? []);

            return ApiResponse::success('Project updated', [$project->load('attributeValues.attribute')]);
        }, 5);
    }

    public function destroy(Project $project)
    {
        return DB::transaction(function () use ($project) {
            $project->attributeValues()->delete();
            $project->delete();

            return ApiResponse::success('Project deleted successfully');
        }, 5);
    }

    public function setAttributes(Request $request, Project $project)
    {
        $validated = $request->validate([
            'attributes' => 'required|array',
            'attributes.*.attribute_id' => 'required|exists:attributes,id',
            'attributes.*.value' => 'required|string',
        ]);

        $this->syncAttributes($project, $validated['attributes']);

        return ApiResponse::success('Attributes updated.');
    }

    private function syncAttributes(Project $project, array $attributes): void
    {
        foreach ($attributes as $attr) {
            AttributeValue::updateOrCreate([
                'entity_id' => $project->id,
                'entity_type' => Project::class,
                'attribute_id' => $attr['attribute_id'],
            ], ['value' => $attr['value']]);
        }
    }

    public function filter(Request $request)
    {
        $filters = $request->query('filters', []);

        $projects = Project::with('attributeValues.attribute')
            ->when($filters, fn($query) => $this->applyFilters($query, $filters))
            ->get();

        return ApiResponse::success('Filtered projects', compact('projects'));
    }
}
