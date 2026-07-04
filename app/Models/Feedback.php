<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'feedback_text', 'student_id', 'parent_id', 'class_id', 'section_id', 'school_id', 'admin_id', 'session_id', 'title'
    ];
}
