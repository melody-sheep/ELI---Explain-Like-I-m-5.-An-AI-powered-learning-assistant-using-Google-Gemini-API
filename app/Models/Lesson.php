<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'subject'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contents()
    {
        return $this->hasMany(LessonContent::class);
    }

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}