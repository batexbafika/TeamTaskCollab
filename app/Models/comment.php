<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'message',
        'taskID',
        'createdBy',
        'createdAt',
    ];

    //cast to carbon objects for easier date handling
    protected $casts = [
    'createdAt' => 'datetime',
    ];


    //relationships

    public function task()
    {
        return $this->belongsTo(Task::class, 'taskID');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }


}
