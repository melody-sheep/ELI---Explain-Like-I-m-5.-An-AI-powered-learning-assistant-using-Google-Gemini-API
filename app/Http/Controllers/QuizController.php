<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Lesson;
use App\Services\GeminiLMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class QuizController extends Controller
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
        $quizzes = Quiz::where('user_id', $this->getUserId())->withCount('questions')->latest()->get();
        return view('quizzes.index', compact('quizzes'));
    }

    public function generate()
    {
        $lessons = Lesson::where('user_id', $this->getUserId())->get();
        return view('quizzes.generate', compact('lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'num_questions' => 'integer|min:1|max:20',
            'lesson_id' => 'nullable|exists:lessons,id'
        ]);

        $filePath = $request->file('file')->store('quiz-sources', 'public');
        
        // Sample quiz questions
        $sampleQuestions = [
            ['question' => 'What is Laravel?', 'options' => ['PHP Framework', 'JavaScript Library', 'CSS Framework', 'Database'], 'correct_answer' => 'PHP Framework'],
            ['question' => 'What does ORM stand for?', 'options' => ['Object Relational Mapping', 'Online Resource Management', 'Object Request Model', 'Open Relation Method'], 'correct_answer' => 'Object Relational Mapping'],
            ['question' => 'Which command starts Laravel server?', 'options' => ['php artisan serve', 'php start', 'laravel run', 'php serve'], 'correct_answer' => 'php artisan serve'],
        ];
        
        $quiz = Quiz::create([
            'user_id' => $this->getUserId(),
            'lesson_id' => $request->lesson_id,
            'title' => $request->title,
            'description' => $request->description,
            'source_file' => $filePath
        ]);
        
        foreach ($sampleQuestions as $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'options' => json_encode($q['options']),
                'correct_answer' => $q['correct_answer'],
                'type' => 'multiple_choice'
            ]);
        }
        
        return redirect()->route('quizzes.index')->with('success', 'Quiz generated successfully!');
    }

    public function take($id)
    {
        $quiz = Quiz::where('user_id', $this->getUserId())->with('questions')->findOrFail($id);
        return view('quizzes.take', compact('quiz'));
    }

    public function submit(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $score = 0;
        $total = count($quiz->questions);
        
        foreach ($quiz->questions as $question) {
            $userAnswer = $request->input('question_' . $question->id);
            if ($userAnswer == $question->correct_answer) {
                $score++;
            }
        }
        
        $percentage = round(($score / $total) * 100);
        return back()->with('result', "You scored $score out of $total ($percentage%)");
    }

    public function destroy($id)
    {
        $quiz = Quiz::where('user_id', $this->getUserId())->findOrFail($id);
        $quiz->delete();
        return redirect()->route('quizzes.index')->with('success', 'Quiz deleted!');
    }

    public function saveAnswer(Request $request)
    {
        $answers = Session::get("quiz_answers.{$request->quiz_id}", []);
        $answers[$request->question_id] = $request->answer;
        Session::put("quiz_answers.{$request->quiz_id}", $answers);

        return response()->json(['success' => true]);
    }

    public function results($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        $answers = Session::get("quiz_answers.{$id}", []);

        $score = 0;
        foreach ($quiz->questions as $question) {
            if (($answers[$question->id] ?? '') === $question->correct_answer) {
                $score++;
            }
        }

        $percentage = round(($score / max($quiz->questions->count(), 1)) * 100);

        Session::put("quiz_score.{$id}", $percentage);

        Session::put("quiz_times.{$id}", (Session::get("quiz_times.{$id}", 0) + 1));

        return view('quizzes.results', compact('quiz', 'answers', 'score', 'percentage'));
    }
}
