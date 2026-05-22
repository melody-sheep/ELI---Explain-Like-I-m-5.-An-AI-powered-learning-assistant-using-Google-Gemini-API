<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonUserProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_user_progress';

    protected $fillable = [
        'user_id', 'lesson_id', 'content_id', 'is_completed', 'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function content()
    {
        return $this->belongsTo(LessonContent::class, 'content_id');
    }
}