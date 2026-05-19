<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    // Nama tabel di database
    protected $table = 'contents';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'course_id',
        'title',
        'content_url',
        'type',
        'order'
    ];

    // Relasi balik ke Course (Satu materi punya satu kursus)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
