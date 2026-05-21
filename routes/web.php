<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

// DEMO LESSON ROUTES (No API, Hardcoded content)
Route::prefix('demo')->group(function () {
    Route::get('/lessons', [App\Http\Controllers\DemoLessonController::class, 'index'])->name('demo.lessons.index');
    Route::get('/lessons/{id}', [App\Http\Controllers\DemoLessonController::class, 'show'])->name('demo.lessons.show');
});

Route::get('/', function () {
    return view('index');
});

Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Routes are working']);
});


// API routes
Route::post('/ask', [AIController::class, 'ask']);
Route::post('/summarize', [AIController::class, 'summarize']);
Route::post('/eli5', [AIController::class, 'eli5']);
Route::post('/explain-code', [AIController::class, 'explainCode']);
Route::post('/code', [AIController::class, 'explainCode']);
Route::get('/history', [AIController::class, 'history']);
Route::delete('/history/{id}', [AIController::class, 'delete']);

// LMS Routes
Route::prefix('lms')->group(function () {
    Route::get('/lessons', [App\Http\Controllers\LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [App\Http\Controllers\LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [App\Http\Controllers\LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{id}', [App\Http\Controllers\LessonController::class, 'show'])->name('lessons.show');
    Route::delete('/lessons/{id}', [App\Http\Controllers\LessonController::class, 'destroy'])->name('lessons.destroy');
    Route::post('/lessons/{id}/add-content', [App\Http\Controllers\LessonController::class, 'addContent'])->name('lessons.add-content');
    
    Route::get('/flashcards', [App\Http\Controllers\FlashcardController::class, 'index'])->name('flashcards.index');
    Route::get('/flashcards/generate', [App\Http\Controllers\FlashcardController::class, 'generate'])->name('flashcards.generate');
    Route::post('/flashcards/generate', [App\Http\Controllers\FlashcardController::class, 'store'])->name('flashcards.store');
    Route::delete('/flashcards/{id}', [App\Http\Controllers\FlashcardController::class, 'destroy'])->name('flashcards.destroy');
    
    Route::get('/quizzes', [App\Http\Controllers\QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/generate', [App\Http\Controllers\QuizController::class, 'generate'])->name('quizzes.generate');
    Route::post('/quizzes/generate', [App\Http\Controllers\QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{id}/take', [App\Http\Controllers\QuizController::class, 'take'])->name('quizzes.take');
    Route::post('/quizzes/{id}/submit', [App\Http\Controllers\QuizController::class, 'submit'])->name('quizzes.submit');
    Route::delete('/quizzes/{id}', [App\Http\Controllers\QuizController::class, 'destroy'])->name('quizzes.destroy');

    // Additional Routes for Enhanced Features
    Route::post('/lessons/{id}/bookmark', [App\Http\Controllers\LessonController::class, 'toggleBookmark']);
    Route::post('/lessons/{id}/progress', [App\Http\Controllers\LessonController::class, 'updateProgress']);
    Route::get('/lessons/{id}/notes', [App\Http\Controllers\LessonController::class, 'getNotes']);
    Route::post('/lessons/{id}/notes', [App\Http\Controllers\LessonController::class, 'saveNotes']);
    
    // Flashcard progress routes
    Route::post('/flashcards/progress', [App\Http\Controllers\FlashcardController::class, 'saveProgress']);
    
    // Quiz answer routes
    Route::post('/quizzes/save-answer', [App\Http\Controllers\QuizController::class, 'saveAnswer']);
    Route::get('/quizzes/{id}/results', [App\Http\Controllers\QuizController::class, 'results']);
});
