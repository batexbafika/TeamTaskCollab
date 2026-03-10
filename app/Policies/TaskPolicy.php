<?php
namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;

class TaskPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        // Any member of the project can list tasks
        return $project->members()->where('userID', $user->userID)->exists();
    }

    public function view(User $user, Task $task): bool
    {
        // Task creator OR any project member
        return $user->userID === $task->createdBy
            || $task->project->members()->where('userID', $user->userID)->exists();
    }

    public function create(User $user, Project $project): bool
    {
        // Only project members can create tasks
        return $project->members()->where('userID', $user->userID)->exists();
    }

    public function update(User $user, Task $task): bool
    {
        // Task creator OR project manager
        return $user->userID === $task->createdBy || $task->project->members()->where('userID', $user->userID)
                ->where('role', 'manager')
                ->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        // Same rule as update
        return $user->userID === $task->createdBy
            || $task->project->members()
                ->where('userID', $user->userID)
                ->where('role', 'manager')
                ->exists();
    }
}

