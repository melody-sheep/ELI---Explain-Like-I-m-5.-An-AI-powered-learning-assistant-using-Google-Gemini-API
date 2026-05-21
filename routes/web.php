<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\DemoLessonController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Guest Routes (Not logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Landing page redirects to login
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    // Login Routes
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    // Guest Mode - Start session without account
    Route::get('/guest', function () {
        // Create a temporary guest user if doesn't exist
        $guest = \App\Models\User::firstOrCreate(
            ['email' => 'guest_' . session()->getId() . '@temp.com'],
            [
                'name' => 'Guest User',
                'password' => bcrypt(\Str::random(40)),
                'is_guest' => true
            ]
        );
        Auth::login($guest);
        session()->put('is_guest', true);
        return redirect()->route('dashboard');
    })->name('guest.mode');
    
    // Password Reset Routes
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Illuminate\Http\Request $request) {
        return back()->with('status', 'Password reset link sent!');
    })->name('password.email');

    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (Illuminate\Http\Request $request) {
        return redirect('/login')->with('status', 'Password reset successfully!');
    })->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        session()->forget('is_guest');
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login');

Route::post('/register', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'is_guest' => false
    ]);

    Auth::login($user);

    return redirect(route('dashboard'));
})->name('register');

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Dashboard (Main Chat Page)
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');
    
    // Demo Lesson Routes
    Route::prefix('demo')->group(function () {
        Route::get('/lessons', [DemoLessonController::class, 'index'])->name('demo.lessons.index');
        Route::get('/lessons/{id}', [DemoLessonController::class, 'show'])->name('demo.lessons.show');
    });
    
    // API routes (Chat)
    Route::post('/ask', [AIController::class, 'ask']);
    Route::post('/summarize', [AIController::class, 'summarize']);
    Route::post('/eli5', [AIController::class, 'eli5']);
    Route::post('/explain-code', [AIController::class, 'explainCode']);
    Route::post('/code', [AIController::class, 'explainCode']);
    Route::get('/history', [AIController::class, 'history']);
    Route::delete('/history/{id}', [AIController::class, 'delete']);
    Route::delete('/history/clear', [AIController::class, 'clearAll']);
    
    // LMS Routes
    Route::prefix('lms')->group(function () {
        // Lessons
        Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
        Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{id}', [LessonController::class, 'show'])->name('lessons.show');
        Route::put('/lessons/{id}', [LessonController::class, 'update'])->name('lessons.update');
        Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->name('lessons.destroy');
        Route::post('/lessons/{id}/add-content', [LessonController::class, 'addContent'])->name('lessons.add-content');
        Route::patch('/lessons/{id}/progress', [LessonController::class, 'updateProgress'])->name('lessons.progress');
        Route::patch('/lessons/{id}/bookmark', [LessonController::class, 'toggleBookmark'])->name('lessons.bookmark');
        Route::patch('/lessons/{id}/complete', [LessonController::class, 'markComplete'])->name('lessons.complete');
        Route::get('/lessons/{id}/notes', [LessonController::class, 'getNotes']);
        Route::post('/lessons/{id}/notes', [LessonController::class, 'saveNotes']);
        
        // Flashcards
        Route::get('/flashcards', [FlashcardController::class, 'index'])->name('flashcards.index');
        Route::get('/flashcards/generate', [FlashcardController::class, 'generate'])->name('flashcards.generate');
        Route::post('/flashcards/generate', [FlashcardController::class, 'store'])->name('flashcards.store');
        Route::put('/flashcards/{id}', [FlashcardController::class, 'update'])->name('flashcards.update');
        Route::delete('/flashcards/{id}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
        Route::post('/flashcards/progress', [FlashcardController::class, 'saveProgress']);
        Route::patch('/flashcards/{id}/mastery', [FlashcardController::class, 'updateMastery'])->name('flashcards.mastery');
        Route::patch('/flashcards/{id}/difficulty', [FlashcardController::class, 'updateDifficulty'])->name('flashcards.difficulty');
        
        // Quizzes
        Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/generate', [QuizController::class, 'generate'])->name('quizzes.generate');
        Route::post('/quizzes/generate', [QuizController::class, 'store'])->name('quizzes.store');
        Route::put('/quizzes/{id}', [QuizController::class, 'update'])->name('quizzes.update');
        Route::get('/quizzes/{id}/take', [QuizController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
        Route::delete('/quizzes/{id}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::post('/quizzes/save-answer', [QuizController::class, 'saveAnswer']);
        Route::get('/quizzes/{id}/results', [QuizController::class, 'results']);
        Route::patch('/quizzes/{id}/retake', [QuizController::class, 'resetForRetake'])->name('quizzes.retake');
    });
});

// Test route (public)
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Routes are working']);
});