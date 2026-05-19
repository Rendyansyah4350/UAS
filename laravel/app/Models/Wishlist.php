<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    // Pastikan database kamu membaca kolom ini
    protected $fillable = ['user_id', 'course_id'];

    /**
     * Relasi ke model Course (Kursus)
     * Nama fungsi ini harus 'course' (huruf kecil)
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
