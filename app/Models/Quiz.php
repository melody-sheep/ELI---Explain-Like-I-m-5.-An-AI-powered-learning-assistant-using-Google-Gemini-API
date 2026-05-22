<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'title',
        'description',
        'source_file',
        'time_limit_per_question',
        'time_spent_total',
        'last_score',
        'times_taken',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}