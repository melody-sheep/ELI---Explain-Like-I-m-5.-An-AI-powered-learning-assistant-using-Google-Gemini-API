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
        return $this->hasMany(LessonContent::class)->orderBy('order_index');
    }

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function userProgress()
    {
        return $this->hasMany(LessonUserProgress::class);
    }

    public function notes()
    {
        return $this->hasMany(LessonNote::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(LessonBookmark::class);
    }

    public function getProgressPercent($userId)
    {
        $totalContents = $this->contents->count();
        if ($totalContents === 0) return 0;
        
        $completedContents = $this->userProgress()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->count();
        
        return round(($completedContents / $totalContents) * 100);
    }

    public function getCompletedCount($userId)
    {
        return $this->userProgress()
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->count();
    }

    public function isBookmarkedByUser($userId)
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function getUserNote($userId)
    {
        return $this->notes()->where('user_id', $userId)->first();
    }
}