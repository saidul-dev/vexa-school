<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmitCard extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'template', 'heading', 'title', 'exam_center', 'footer_text', 'left_logo', 'right_logo', 'sign', 'background_image', 'school_id', 'created_at', 'created_at'
    ];

}
