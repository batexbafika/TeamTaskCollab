<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    //fillable fields
    protected $table = 'tasks';
    protected $primaryKey = 'taskID';
    public $incrementing = true;
    protected $keyType = 'int';

     protected $fillable = [
        'title',
        'description',
        'status',
        'createdBy',
        'projectID',
        'createdAt',
        'deadline',
    ];


    
//relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'projectID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    public function assignments()
    {
    return $this->hasMany(TaskAssignment::class, 'taskID', 'taskID');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'taskID', 'userID');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'taskID');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'projectID', 'projectID');
    }
}