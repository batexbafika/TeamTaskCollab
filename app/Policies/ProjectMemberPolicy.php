<?php
namespace App\Policies;

use App\Models\User;
use App\Models\ProjectMember;

class ProjectMemberPolicy
{
    public function viewAny(User $user, $project): bool
    {
        // Any member of the project can list members
        return $project->members()->where('userID', $user->userID)->exists();
    }

    public function create(User $user, $project): bool
    {
        // Only managers can add members
        return $project->members()
            ->where('userID', $user->userID)
            ->where('role', 'projectManager')
            ->exists();
    }

    public function update(User $user, ProjectMember $member): bool
    {
        // Only managers can change roles
        return $member->project->members()
            ->where('userID', $user->userID)
            ->where('role', 'projectManager')
            ->exists();
    }

    public function delete(User $user, ProjectMember $member): bool
    {
        // Only managers can remove members
        return $member->project->members()
            ->where('userID', $user->userID)
            ->where('role', 'projectManager')
            ->exists();
    }
}
