<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaskAssignment;
use App\Models\Project;

class TaskAssignmentPolicy
{
    // Any project member can view assignments
    public function view(User $user, $model)
    {
        // $model can be Task or TaskAssignment
        $project = $model instanceof TaskAssignment
            ? $model->task->project
            : $model->project;

        return $project->members()
            ->where('userID', $user->userID)
            ->exists();
    }

    // Only managers or administrators can assign users
    public function create(User $user, Project $project)
    {
        return $project->members()
            ->where('userID', $user->userID)
            ->whereIn('role', ['projectManager', 'administrator'])
            ->exists();
    }

    // Managers/admins can delete any assignment,
    // assigned user can delete their own assignment
    public function delete(User $user, TaskAssignment $assignment)
    {
        $isPrivileged = $assignment->task->project->members()
            ->where('userID', $user->userID)
            ->whereIn('role', ['projectManager', 'administrator'])
            ->exists();

        $isSelf = $user->userID === $assignment->userID; // Can delete own assignment

        return $isPrivileged || $isSelf;
    }
}
