<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    //
    use HasFactory;

        protected $fillable = [
        'projectID',
        'userID',
        'role',
        'joinedAt',
     ];
     
    //cast to carbon objects for easier date handling
     protected $casts = [
    'joinedAt' => 'datetime',
    'role' => 'string', // could later be an Enum
     ];
    

    //relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectID');
    }
}