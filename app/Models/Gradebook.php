<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gradebook extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id', 'section_id', 'student_id', 'exam_category_id', 'marks', 'comment', 'school_id', 'session_id', 'timestamp'
    ];
}
