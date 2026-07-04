<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'exam_category_id', 'exam_type', 'room_number', 'starting_time', 'ending_time', 'total_marks', 'status', 'class_id', 'subject_id', 'school_id', 'session_id'
    ];
}
