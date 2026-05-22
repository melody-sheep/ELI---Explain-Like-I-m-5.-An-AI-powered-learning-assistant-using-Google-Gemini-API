<?php
// routes/web.php - COMPLETE REPLACEMENT

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
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::get('/guest', function () {
        // Check if already logged in
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        
        // Create unique guest user for this session
        $sessionId = session()->getId();
        $uniqueId = substr(md5($sessionId . time()), 0, 8);
        
        $guest = \App\Models\User::create([
            'name' => 'Guest_' . $uniqueId,
            'email' => 'guest_' . $sessionId . '_' . $uniqueId . '@temp.local',
            'password' => bcrypt(\Illuminate\Support\Str::random(40)),
            'is_guest' => true
        ]);
        
        Auth::login($guest);
        session()->put('is_guest', true);
        session()->put('guest_user_id', $guest->id);
        
        return redirect()->route('dashboard');
    })->name('guest.mode');
    
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');
    
    Route::post('/forgot-password', function (Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);
        // Add actual password reset logic here
        return back()->with('status', 'Password reset link sent!');
    })->name('password.email');
    
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
    
    Route::post('/reset-password', function (Illuminate\Http\Request $request) {
        // Add actual password reset logic here
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
    
    // Check if user exists first
    $user = \App\Models\User::where('email', $credentials['email'])->first();
    
    if (!$user) {
        return back()->withErrors([
            'email' => 'No account found with this email address.',
        ])->onlyInput('email');
    }
    
    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        session()->forget('is_guest');
        session()->forget('guest_id');
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
    session()->forget('is_guest');
    session()->forget('guest_id');
    
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
    
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');
    
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

        Route::post('/lessons/{lessonId}/file-note/{contentId}', [LessonController::class, 'saveFileNote']);

        // Add content completion + deletion routes
        Route::patch('/lessons/{lessonId}/content/{contentId}/complete', [LessonController::class, 'toggleContentComplete']);
        Route::delete('/lessons/{lessonId}/content/{contentId}', [LessonController::class, 'deleteContent']);
        
        // Flashcards
        Route::get('/flashcards', [FlashcardController::class, 'index'])->name('flashcards.index');
        
        // Flashcard Decks
        Route::get('/flashcards/deck/{lessonId}', [FlashcardController::class, 'deck'])->name('flashcards.deck');
        Route::delete('/flashcards/deck/{lessonId}', [FlashcardController::class, 'deleteDeck'])->name('flashcards.deck.delete');

        Route::get('/flashcards/generate', [FlashcardController::class, 'generate'])->name('flashcards.generate');
        Route::post('/flashcards/generate', [FlashcardController::class, 'store'])->name('flashcards.store');
        Route::put('/flashcards/{id}', [FlashcardController::class, 'update'])->name('flashcards.update');
        Route::delete('/flashcards/{id}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
        // Flashcard mastery (SPAs/JS may POST mastery updates)
        Route::post('/flashcards/{id}/mastery', [FlashcardController::class, 'updateMastery'])->name('flashcards.mastery');

        
        // Quizzes
        Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/generate', [QuizController::class, 'generate'])->name('quizzes.generate');
        Route::post('/quizzes/generate', [QuizController::class, 'store'])->name('quizzes.store');
        Route::put('/quizzes/{id}', [QuizController::class, 'update'])->name('quizzes.update');
        Route::get('/quizzes/{id}/take', [QuizController::class, 'take'])->name('quizzes.take');
        Route::post('/quizzes/{id}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
        Route::delete('/quizzes/{id}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::get('/quizzes/{id}/results', [QuizController::class, 'results'])->name('quizzes.results');
        Route::patch('/quizzes/{id}/retake', [QuizController::class, 'resetForRetake'])->name('quizzes.retake');
        Route::patch('/quizzes/{id}/settings', [QuizController::class, 'updateSettings'])->name('quizzes.settings');

        // Add this inside the LMS group after the other quiz routes:
        Route::post('/quizzes/save-answer', [QuizController::class, 'saveAnswer'])->name('quizzes.save-answer');

        Route::get('/test-quiz', function () {
            $userId = app(App\Traits\GetCurrentUserId::class)->getCurrentUserId();

            // Test content
            $content = "Artificial Intelligence is the simulation of human intelligence in machines. Machine Learning helps computers learn from data. Deep Learning uses neural networks.";

            $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $content, -1, PREG_SPLIT_NO_EMPTY);
            $questions = [];

            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if (strlen($sentence) > 30) {
                    $questions[] = [
                        'question' => 'What is ' . substr($sentence, 0, 50) . '?',
                        'options' => [substr($sentence, 0, 80), 'Not mentioned', 'Different concept', 'None'],
                        'correct_answer' => substr($sentence, 0, 80)
                    ];
                }
            }

            return response()->json([
                'content' => $content,
                'sentences' => $sentences,
                'questions' => $questions,
                'count' => count($questions)
            ]);
        });
    });
});

Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'Routes are working']);
});