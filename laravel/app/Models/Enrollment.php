<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'price_bought',
        'status',
    ];

    /**
     * Relasi ke Model User (Pemilik pesanan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Model Course (Kursus yang dibeli)
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function calculateProgress()
    {
        if (!$this->course) {
        return 0;
        }
        // 1. Hitung total konten dalam kursus ini
        $totalContents = $this->course->contents()->count();

        if ($totalContents == 0) return 0;

        // 2. Hitung berapa konten yang sudah diselesaikan oleh user ini
        $completedContents = \App\Models\Progress::where('user_id', $this->user_id)
            ->whereHas('content', function ($query) {
                $query->where('course_id', $this->course_id);
            })->count();

        // 3. Kembalikan dalam persen
        return round(($completedContents / $totalContents) * 100);
    }

    
}
