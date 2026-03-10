<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProjectController extends Controller
{
//create project only for loged in user 
   public function store(Request $request)
{
    $this->authorize('create', Project::class);

    $validated = $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $project = Project::create([
        'name'        => $validated['name'],
        'description' => $validated['description'],
        'createdBy'   => Auth::id(),
    ]);

    $project->users()->attach(Auth::id(), [
        'role'     => 'projectManager',
        'joinedAt' => now(),
    ]);

    return response()->json($project, 201);
}


    public function index()
{
    $this->authorize('viewAny', Project::class);

    $userId = Auth::id();

    $projects = Project::whereHas('members', function ($query) use ($userId) {
        $query->where('userID', $userId);
    })->with(['tasks', 'members'])->get();

    return response()->json($projects);
}



    //find project be id only owned by loged in user
    public function show($id)
    {
        $project = Project::with(['tasks', 'members'])->findOrFail($id);
        $this->authorize('view', $project);

        return response()->json($project);
    }


    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project
        ]);
    }


    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }

}