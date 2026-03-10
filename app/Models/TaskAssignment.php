<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    use HasFactory;
    //fillable fields

    protected $table = 'task_assignments';
    protected $primaryKey = 'assignmentID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'taskID',
        'userID',
    ];



    //relationships
   public function task()
   {
    return $this->belongsTo(Task::class, 'taskID', 'taskID');
   }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
    
}
