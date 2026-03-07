<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Task;

class TaskController extends Controller
{

//store a task in a project table (mass assign via eloquent)
   public function store(Request $request, $projectId)
{
    $validated = $request->validate([  //validate incoming request
        'description' => 'required|string',
        'status' => 'required|string',
        'createdBy' => 'required|integer',
        'deadline' => 'nullable|date',
    ]);

    $task = Task::create([
        'description' => $validated['description'],
        'status' => $validated['status'],
        'createdBy' => $validated['createdBy'],
        'projectID' => $projectId,
        'createdAt' => now(),
        'deadline' => $validated['deadline'] ?? null,
    ]);

    return response()->json([
        'message' => 'Task created successfully',
        'task' => $task
    ]);
}
//index to get all tasks in a project
public function index($projectId) //fetch is done by projectID
{
    $tasks = Task::where('projectID', $projectId)->get(); // projectID found in the request
    return response()->json($tasks); //returns all tasks under the provided project ID
}

//get individual tasks under a given project (task/{id})
public function show($projectId, $taskId) //both the project id and the task are params
{
     $task = Task::where('projectID', $projectId)->where('taskID', $taskId)->firstOrFail(); //in a project , find task with id ={id}
    return response()->json($task); //return such task.
}

//update a task
public function update(Request $request, $projectID, $taskID)
{
    $validated = $request->validate([  //validate incoming request
        'description' => 'sometimes|required|string',
        'status' => 'sometimes|required|string',
        'deadline' => 'sometimes|nullable|date',
    ]);

    $task = Task::where('projectID', $projectID)->where('taskID', $taskID)->firstOrFail(); //find the task to update

    $task->update($validated); //update the task with the validated data

    return response()->json([
        'message' => 'Task updated successfully',
        'task' => $task
    ]);
}

//delete a task
public function destroy($projectID, $taskID)
{
    $task = Task::where('projectID', $projectID)->where('taskID', $taskID)->firstOrFail();

    $task->delete(); //delete the task

    return response()->json([
        'message' => 'Task deleted successfully'
    ]);
}

}