<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Exception;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the timesheets.
     */
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);

        $query = Timesheet::with(['user', 'project.attributeValues.attribute']);

        foreach ($filters as $field => $filter) {
            $operator = $filter['operator'] ?? '=';
            $value = $filter['value'] ?? $filter;

            match ($field) {
                'task_name', 'date', 'hours' => $query->where($field, $operator, $value),
                'project' => $query->whereHas('project', fn($q) => $q->where('name', $operator, $value)),
                'user' => $query->whereHas('user', fn($q) => $q->where('first_name', $operator, $value)),
                default => null,
            };
        }
        return ApiResponse::success('Timesheets fetched', ['timesheets' => $query->get()]);
    }

    /**
     * Store a newly created timesheet.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id'    => 'required|exists:projects,id',
                'date'          => 'required|date',
                'hours'  => 'required|numeric|min:0',
                'task_name'   => 'nullable|string|max:255',
            ]); 

            if ($validator->fails()) {
                return ApiResponse::error('Validation failed.', 422, ['errors' => $validator->errors()->toArray()]);
            }

            $timesheet = Timesheet::create([
                'user_id'       => Auth::id(),
                'project_id'    => $request->project_id,
                'date'          => $request->date,
                'hours'         => $request->hours,
                'task_name'   => $request->task_name,
            ]);

            return ApiResponse::success('Timesheet created successfully.', ['timesheet' => $timesheet], 201);
        } catch (Exception $e) {
            return ApiResponse::error('Failed to create timesheet: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified timesheet.
     */
    public function show($id)
    {
        try {
            $timesheet = Timesheet::with('user', 'project')->find($id);

            if (!$timesheet) {
                return ApiResponse::error('Timesheet not found.', 404);
            }

            return ApiResponse::success('Timesheet retrieved successfully.', ['timesheet' => $timesheet]);
        } catch (Exception $e) {
            return ApiResponse::error('Failed to retrieve timesheet: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified timesheet.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id'    => 'sometimes|required|exists:projects,id',
                'date'          => 'sometimes|required|date',
                'hours'          => 'sometimes|required|numeric|min:0',
                'task_name'   => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error('Validation failed.', 422, ['errors' => $validator->errors()->toArray()]);
            }

            $timesheet = Timesheet::find($id);

            if (!$timesheet) {
                return ApiResponse::error('Timesheet not found.', 404);
            }

            $timesheet->update($request->only(['project_id', 'date', 'hours', 'task_name']));

            return ApiResponse::success('Timesheet updated successfully.', ['timesheet' => $timesheet]);
        } catch (Exception $e) {
            return ApiResponse::error('Failed to update timesheet: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified timesheet.
     */
    public function destroy($id)
    {
        try {
            $timesheet = Timesheet::find($id);

            if (!$timesheet) {
                return ApiResponse::error('Timesheet not found.', 404);
            }

            $timesheet->delete();

            return ApiResponse::success('Timesheet deleted successfully.', ['timesheet_id' => $id]);
        } catch (Exception $e) {
            return ApiResponse::error('Failed to delete timesheet: ' . $e->getMessage(), 500);
        }
    }
}
