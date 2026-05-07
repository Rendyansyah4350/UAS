<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'image'
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
}
