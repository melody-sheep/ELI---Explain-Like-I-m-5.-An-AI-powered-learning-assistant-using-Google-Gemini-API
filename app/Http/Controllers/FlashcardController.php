<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Services\GeminiLMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class FlashcardController extends Controller
{
    protected $gemini;

    public function __construct(GeminiLMSService $gemini)
    {
        $this->gemini = $gemini;
    }

    private function getUserId()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $user = \App\Models\User::firstOrCreate(
                ['id' => 1],
                ['name' => 'Guest User', 'email' => 'guest@example.com', 'password' => bcrypt('password')]
            );
            Session::put('user_id', $user->id);
            return $user->id;
        }
        return $userId;
    }

    public function index()
    {
        $flashcards = Flashcard::where('user_id', $this->getUserId())->latest()->get();
        $lessons = Lesson::where('user_id', $this->getUserId())->get();
        return view('flashcards.index', compact('flashcards', 'lessons'));
    }

    public function generate()
    {
        $lessons = Lesson::where('user_id', $this->getUserId())->get();
        return view('flashcards.generate', compact('lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'lesson_id' => 'nullable|exists:lessons,id'
        ]);

        $filePath = $request->file('file')->store('flashcard-sources', 'public');
        
        // For now, create sample flashcards
        $sampleFlashcards = [
            ['question' => 'What is Laravel?', 'answer' => 'A PHP framework for web artisans'],
            ['question' => 'What is Eloquent?', 'answer' => 'Laravel\'s ORM for database interactions'],
            ['question' => 'What is a migration?', 'answer' => 'Version control for database schemas'],
            ['question' => 'What is Blade?', 'answer' => 'Laravel\'s templating engine'],
            ['question' => 'What is Composer?', 'answer' => 'Dependency manager for PHP'],
        ];
        
        foreach ($sampleFlashcards as $card) {
            Flashcard::create([
                'user_id' => $this->getUserId(),
                'lesson_id' => $request->lesson_id,
                'question' => $card['question'],
                'answer' => $card['answer'],
                'source_file' => $filePath
            ]);
        }
        
        return redirect()->route('flashcards.index')->with('success', 'Flashcards generated successfully!');
    }

    public function destroy($id)
    {
        $flashcard = Flashcard::where('user_id', $this->getUserId())->findOrFail($id);
        $flashcard->delete();
        return back()->with('success', 'Flashcard deleted!');
    }
}