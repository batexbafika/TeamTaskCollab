<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        // Any user can list their own projects
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        // Allow if user is the creator or a member
        return $user->userID === $project->createdBy
            || $project->members()->where('userID', $user->userID)->exists();
    }

    public function create(User $user): bool
    {
        // Any authenticated user can create a project
        return $user !== null;
    }

    public function update(User $user, Project $project): bool
    {
        // Only the project creator or manager can update
        return $user->userID === $project->createdBy
            || $project->members()->where('userID', $user->userID)->where('role', 'manager')->exists();
    }

    public function delete(User $user, Project $project): bool
    {
        // Only the project creator can delete
        return $user->userID === $project->createdBy;
    }

    public function manageMembers(User $user, Project $project): bool
    {
        // Only managers can add/remove members
        return $project->members()->where('userID', $user->userID)->where('role', 'manager')->exists();
    }
}
