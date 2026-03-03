<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //fillable fields
     use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'createdBy',
        'createdAt',
    ];

    //cast to carbon objects for easier date handling
    protected $casts = [
    'createdAt' => 'datetime',
     ];

     //relationships

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'projectID');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'projectID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }
}
