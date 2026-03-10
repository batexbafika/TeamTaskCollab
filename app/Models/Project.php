<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'projectID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'description',
        'createdBy',  
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_members', 'projectID', 'userID');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'projectID');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'projectID');
    }
}
