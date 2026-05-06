<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quizzes';

    protected $fillable = [
        'course_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'answer'
    ];

    // Relasi balik ke Course (Satu quiz punya satu kursus)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
