<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcard_mastery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('flashcard_id')->constrained()->onDelete('cascade');
            $table->enum('mastery_level', ['new', 'learning', 'mastered', 'review'])->default('new');
            $table->integer('times_reviewed')->default(0);
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'flashcard_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_mastery');
    }
};