<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProjectController extends Controller
{
    // Step 1: Create Project
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = new Project($validated);
        $project->user_id = $request->user()->id; // assign to logged-in user
        $project->save();

        return response()->json($project, 201);
    }

    //filter project by loged in user
    public function index(Request $request)
    {
    // Fetch projects belonging to the authenticated user
    $projects = Project::where('user_id', $request->user()->id)->with(['tasks', 'members'])->get();
    return response()->json($projects);
    }

    //find project be id only owned by loged in user
    public function show(Request $request, $id)
   {
    // Find project by ID, but only if it belongs to the authenticated user
    $project = Project::where('user_id', $request->user()->id)->with(['tasks', 'members'])->findOrFail($id);

    return response()->json($project);
    }

    public function update(Request $request, $id)
{
    // Validate input
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    // Find project belonging to authenticated user
    $project = Project::where('user_id', $request->user()->id)->findOrFail($id);

    // Update with validated data
    $project->update($validated);

    return response()->json([
        'message' => 'Project updated successfully',
        'project' => $project
    ]);
    }

    public function destroy(Request $request, $id)
    {
        // Find project belonging to authenticated user
        $project = Project::where('user_id', $request->user()->id)->findOrFail($id);

        // Delete the project
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }
}