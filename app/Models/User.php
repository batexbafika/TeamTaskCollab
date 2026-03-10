<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Comment;


//@property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects
class User extends Authenticatable
{
        use HasApiTokens, HasFactory, Notifiable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    protected $primaryKey = 'userID'; // important!
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'address',
        'password',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    //relationships
    public function projectMemberships()
    {
        return $this->hasMany(ProjectMember::class, 'userID');
    }

    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'createdBy');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'createdBy');
    }

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'userID');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments', 'userID', 'taskID');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'createdBy');
    }
  
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members', 'userID', 'projectID')
                    ->withPivot('role', 'joinedAt'); // optional: include extra pivot fields
    }


}