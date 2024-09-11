<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $projectsAsCreator = $user->projects();

        $projectsAsMember = Project::whereHas('members', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        $projects = $projectsAsCreator->union($projectsAsMember)->paginate(10);

        $projectCollection = new ProjectCollection($projects);

        return response()->json($projectCollection, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json(new ProjectResource($project), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);
        $project = new ProjectResource($project);
        $project->load(['tasks', 'members']);

        return response()->json($project, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);
        $project->update($request->validated());

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => new ProjectResource($project),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        $project->delete();
        return response()->json(null, 204);
    }
}
