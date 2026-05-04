<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    // Agar bisa mengisi data lewat Enrollment::create
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
    ];

    // Opsional: Hubungkan ke model Course agar bisa tahu nama kursus yang dibeli
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
