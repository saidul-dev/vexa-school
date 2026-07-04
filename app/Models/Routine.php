<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id', 'section_id', 'subject_id', 'starting_hour', 'ending_hour', 'starting_minute', 'ending_minute','day', 'teacher_id', 'room_id','school_id', 'session_id'
    ];
}
