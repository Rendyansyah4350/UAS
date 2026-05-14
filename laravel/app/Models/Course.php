<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Progress;

class Course extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
        'price',
        'image',
        'rating',
    ];
    public function contents(): HasMany
    {
        // Mengurutkan materi berdasarkan kolom 'order' secara otomatis
        return $this->hasMany(Content::class)->orderBy('order', 'asc');
    }
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function users()
    {
        // Ini akan mengambil user melalui tabel enrollments
        return $this->hasManyThrough(User::class, Enrollment::class, 'course_id', 'id', 'id', 'user_id');
    }

    public function progress()
    {
        // Course punya banyak data progress (hasMany)
        return $this->hasMany(Progress::class);
    }
}
