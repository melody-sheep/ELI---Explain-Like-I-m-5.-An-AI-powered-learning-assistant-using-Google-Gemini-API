<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashcardMastery extends Model
{
    use HasFactory;

    protected $table = 'flashcard_mastery'; // Important: singular, not plural
    
    protected $fillable = [
        'user_id',
        'flashcard_id',
        'mastery_level',
        'times_reviewed',
        'last_reviewed_at',
        'next_review_at'
    ];

    protected $casts = [
        'last_reviewed_at' => 'datetime',
        'next_review_at' => 'datetime',
    ];

    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}