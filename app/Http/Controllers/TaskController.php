<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $tasksQuery = Auth::user()->tasks();

        $tasksQuery->completed($request->input('completed'))
            ->title($request->input('title'))
            ->orderByTitle($request->input('order', 'asc'))->get();

        $tasks = $tasksQuery->get();

        return response()->json(new TaskCollection($tasks));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Project $project = null): JsonResponse
    {
        $data = $request->validated();

        if ($project) {
            if ($project->creator_id === Auth::id() || $project->members()->where('user_id', Auth::id())->exists()) {
                $data['project_id'] = $project->id;
            } else {
                return response()->json([
                    'message' => 'You are not authorized to create tasks in this project',
                ], 403);
            }
        }

        $task = Task::create($data);

        return response()->json(new TaskResource($task), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);
        return response()->json(new TaskResource($task), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task, Project $project = null): JsonResponse
    {
        Gate::authorize('update', $task);
        $data = $request->validated();

        if ($project) {
            if ($project->creator_id === Auth::id() || $project->members()->where('user_id', Auth::id())->exists()) {
                $data['project_id'] = $project->id;
            } else {
                return response()->json([
                    'message' => 'You are not authorized to update this task',
                ], 403);
            }
        }

        $task->update($data);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => new TaskResource($task),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);
        $task->delete();

        return response()->json(null, 204);
    }
}
