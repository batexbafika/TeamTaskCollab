<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAssignment;

class TaskAssignmentController extends Controller
{
    // List all assignments for a task
    public function index($taskID)
    {
        $task = Task::with('assignments.user')->findOrFail($taskID);

        $this->authorize('view', $task);

        return response()->json([
            'taskID'      => $taskID,
            'assignments' => $task->assignments
        ]);
    }

    // Show a single assignment
    public function show($taskID, $assignmentID)
    {
        $assignment = TaskAssignment::where('taskID', $taskID)
            ->where('assignmentID', $assignmentID)
            ->with(['user', 'task'])
            ->firstOrFail();

        $this->authorize('view', $assignment);

        return response()->json([
            'assignment' => $assignment
        ]);
    }

    // Assign a user to a task
    public function store(Request $request, $taskID)
    {
        $task = Task::findOrFail($taskID);

        $this->authorize('create', [TaskAssignment::class, $task->project]);

        $validated = $request->validate([
            'userID' => 'required|exists:users,userID',
        ]);

        $assignment = TaskAssignment::create([
            'taskID' => $taskID,
            'userID' => $validated['userID'],
        ]);

        return response()->json([
            'message'    => 'User assigned to task successfully',
            'assignment' => $assignment
        ]);
    }

    // Remove assignment
    public function destroy($taskID, $assignmentID)
    {
        $assignment = TaskAssignment::where('taskID', $taskID)
            ->where('assignmentID', $assignmentID)
            ->firstOrFail();

        $this->authorize('delete', $assignment);

        $assignment->delete();

        return response()->json([
            'message' => "Task assignment deleted successfully"
        ]);
    }
}
