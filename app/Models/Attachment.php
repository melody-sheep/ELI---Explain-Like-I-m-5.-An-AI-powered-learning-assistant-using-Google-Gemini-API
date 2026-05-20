<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = ['conversation_id', 'file_name', 'file_path', 'file_type', 'file_size'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}