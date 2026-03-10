<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;

    protected $table = 'project_members'; // <-- important
    protected $primaryKey = 'membershipID';
    public $incrementing = true;
    protected $keyType = 'int';

    // Mass assignable fields
    protected $fillable = [
        'projectID', 
        'userID', 
        'role', 
        'joinedAt',
        'created_at',
        'updated_at',
        ];

    /**
     * The user who created the project.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    /**
     * All membership records for this project (pivot as full models).
     */
    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class, 'projectID');
    }

    /**
     * All users who belong to this project (via pivot).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_members', 'projectID', 'userID')->withPivot('role', 'joinedAt')->withTimestamps();
    }

    /**
     * All tasks under this project.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'projectID');
    }

        public function project()
    {
        return $this->belongsTo(Project::class, 'projectID');
    }

}
