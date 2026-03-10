<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

   class Comment extends Model
{
    use HasFactory;
    protected $primaryKey = 'commentID';
    protected $fillable = [
        'message',
        'taskID',
        'createdBy',
    ];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class, 'taskID');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }
}
