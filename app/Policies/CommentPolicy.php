<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use App\Models\Comment;

class CommentPolicy
{
    public function view(User $user, Comment $comment): bool
    {
        return $comment->task->project->members()
            ->where('userID', $user->userID)
            ->exists();
    }

    public function create(User $user, Task $task): bool
    {
        return $task->project->members()
            ->where('userID', $user->userID)
            ->exists();
    }

    public function delete(User $user, Comment $comment): bool
    {
        $isAuthor = $user->userID === $comment->createdBy;

        $isPrivileged = $comment->task->project->members()
            ->where('userID', $user->userID)
            ->whereIn('role', ['projectManager', 'administrator'])
            ->exists();

        return $isAuthor || $isPrivileged;
    }
}
