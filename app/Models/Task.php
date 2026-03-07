<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    //fillable fields
    protected $primaryKey = 'taskID'; //this tells eloquent to use taskID as the primary key instead of id
    public $incrementing = true;
    protected $keyType = 'int';

     protected $fillable = [
        'description',
        'status',
        'createdBy',
        'projectID',
        'createdAt',
        'deadline',
    ];

    //cast to carbon objects for easier date handling
    protected $casts = [
    'status' => 'string', 
    'createdAt' => 'datetime',
    'deadline' => 'datetime',
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
        return $this->hasMany(TaskAssignment::class, 'taskID');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'taskID', 'userID');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'taskID');
    }
}