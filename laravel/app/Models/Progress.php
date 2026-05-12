<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    // Nama tabelnya (opsional, tapi bagus untuk jaga-jaga)
    protected $table = 'progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'content_id',
        'is_completed'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
