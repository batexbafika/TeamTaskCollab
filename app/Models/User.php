<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
        use HasApiTokens, HasFactory, Notifiable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
   

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
}