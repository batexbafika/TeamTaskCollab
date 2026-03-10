<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
  
public function store(Request $request, $projectId)
{
    $project = Project::findOrFail($projectId);

    // Authorize directly against the project
   $this->authorize('create', [Task::class, $project]);


    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'required|string',
        'status'      => 'required|string',
        'deadline'    => 'nullable|date',
    ]);

    $task = Task::create([
        'projectID'   => $projectId,
        'title'       => $validated['title'],
        'description' => $validated['description'],
        'status'      => $validated['status'],
        'createdBy'   => Auth::id(),   // shortcut for current user ID
        'createdAt'   => now(),
        'deadline'    => $validated['deadline'] ?? null,
    ]);

    return response()->json($task, 201);
}


    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);
        $this->authorize('viewAny', [Task::class, $project]);

        $tasks = Task::where('projectID', $projectId)->get();
        return response()->json($tasks);
    }

    public function show($projectId, $taskId)
    {
        $task = Task::where('projectID', $projectId)->where('taskID', $taskId)->firstOrFail();
        $this->authorize('view', $task);

        return response()->json($task);
    }

    public function update(Request $request, $projectId, $taskId)
    {
        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status'      => 'sometimes|required|string',
            'deadline'    => 'sometimes|nullable|date',
        ]);

        $task = Task::where('projectID', $projectId)->where('taskID', $taskId)->firstOrFail();
        $this->authorize('update', $task);

        $task->update($validated);

        return response()->json([
            'message' => 'Task updated successfully',
            'task'    => $task
        ]);
    }

    public function destroy($projectId, $taskId)
    {
        $task = Task::where('projectID', $projectId)->where('taskID', $taskId)->firstOrFail();
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
