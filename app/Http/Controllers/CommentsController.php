<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function index($taskID)
    {
        $task = Task::with('comments.author')->findOrFail($taskID);
        $this->authorize('view', $task);

        return response()->json([
            'taskID'   => $taskID,
            'comments' => $task->comments
        ]);
    }

    public function show($taskID, $commentID)
    {
        $comment = Comment::where('taskID', $taskID)
            ->where('id', $commentID)
            ->with('author')
            ->firstOrFail();

        $this->authorize('view', $comment);

        return response()->json(['comment' => $comment]);
    }

    public function store(Request $request, $taskID)
{
    $task = Task::findOrFail($taskID);

    $this->authorize('create', [Comment::class, $task]);

    $validated = $request->validate([
        'message' => 'required|string|max:1000',
    ]);

    $comment = Comment::create([
        'taskID'    => $taskID,
        'message'   => $validated['message'],
        'createdBy' => Auth::id(),
    ]);

    return response()->json([
        'message' => 'Comment added successfully',
        'comment' => $comment
    ]);
}

    public function destroy($taskID, $commentID)
    {
        $comment = Comment::where('taskID', $taskID)
            ->where('commentID', $commentID)
            ->firstOrFail();

        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
