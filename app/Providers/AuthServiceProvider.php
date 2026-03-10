<?php

namespace App\Providers;

use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use App\Models\ProjectMember;
use App\Policies\ProjectMemberPolicy;
use App\Models\TaskAssignment;
use App\Policies\TaskAssignmentPolicy;
use App\Models\Comment;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Task::class => TaskPolicy::class, //Task class is protected by TaskPolicy
        Project::class => ProjectPolicy::class,
        ProjectMember::class => ProjectMemberPolicy::class,
        TaskAssignment::class => TaskAssignmentPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}