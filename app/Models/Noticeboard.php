<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticeboard extends Model
{
    use HasFactory;

    protected $table = 'noticeboard';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notice_title', 'notice', 'start_date', 'start_time', 'end_date', 'end_time', 'status', 'show_on_website', 'image', 'school_id','session_id'
    ];
}
