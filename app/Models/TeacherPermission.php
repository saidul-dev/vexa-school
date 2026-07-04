<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherPermission extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['id', 'class_id', 'section_id', 'school_id', 'teacher_id', 'marks', 'attendance', 'updated_at'];

}
