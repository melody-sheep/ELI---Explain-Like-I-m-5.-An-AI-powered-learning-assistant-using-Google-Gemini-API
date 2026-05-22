<?php
namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Lesson;
use App\Services\GeminiLMSService;
use App\Traits\GetCurrentUserId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    use GetCurrentUserId;
    protected $gemini;

    public function __construct(GeminiLMSService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        $userId = $this->getCurrentUserId();
        $quizzes = Quiz::where('user_id', $userId)->withCount('questions')->latest()->get();
        return view('quizzes.index', compact('quizzes'));
    }

    public function generate()
    {
        $userId = $this->getCurrentUserId();
        $lessons = Lesson::where('user_id', $userId)->get();
        return view('quizzes.generate', compact('lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'num_questions' => 'integer|min:1|max:50',
            'difficulty' => 'string|in:easy,medium,hard',
            'lesson_id' => 'nullable|exists:lessons,id'
        ]);

        $userId = $this->getCurrentUserId();
        $numQuestions = $request->num_questions ?? 10;
        $difficulty = $request->difficulty ?? 'medium';
        
        $filePath = $request->file('file')->store('quiz-sources', 'public');
        $fullPath = Storage::disk('public')->path($filePath);
        
        $content = $this->extractTextFromFile($fullPath, $request->file('file')->getClientOriginalExtension());
        
        if (empty($content)) {
            return back()->with('error', 'Could not extract text from file. Using sample quiz.');
            $questions = $this->getSampleQuizQuestions($numQuestions);
        } else {
            try {
                $quizData = $this->gemini->generateQuiz($content, $numQuestions, $difficulty);
                $quizArray = json_decode($quizData, true);
                $questions = $quizArray['quiz'] ?? $quizArray['questions'] ?? $quizArray ?? [];
                
                if (empty($questions)) {
                    $questions = $this->getSampleQuizQuestions($numQuestions);
                }
            } catch (\Exception $e) {
                Log::error('AI quiz generation failed: ' . $e->getMessage());
                $questions = $this->getSampleQuizQuestions($numQuestions);
            }
        }
        
        $quiz = Quiz::create([
            'user_id' => $userId,
            'lesson_id' => $request->lesson_id,
            'title' => $request->title,
            'description' => $request->description ?? "Quiz generated from uploaded document with {$numQuestions} questions",
            'source_file' => $filePath,
            'settings' => json_encode([
                'num_questions' => $numQuestions,
                'difficulty' => $difficulty,
                'timer_enabled' => $request->has('timer_enabled'),
                'time_limit' => $request->time_limit ?? 5
            ])
        ]);
        
        foreach ($questions as $q) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $q['question'],
                'options' => json_encode($q['options'] ?? ['True', 'False']),
                'correct_answer' => $q['correct_answer'],
                'type' => $q['type'] ?? 'multiple_choice'
            ]);
        }
        
        return redirect()->route('quizzes.index')->with('success', "Quiz '{$quiz->title}' generated with " . $quiz->questions->count() . " questions!");
    }
    
    private function getSampleQuizQuestions($count)
    {
        $sampleBank = [
            ['question' => 'What is the primary purpose of active recall?', 'options' => ['To re-read notes', 'To test memory retrieval', 'To highlight text', 'To listen to lectures'], 'correct_answer' => 'To test memory retrieval', 'type' => 'multiple_choice'],
            ['question' => 'Spaced repetition involves reviewing material at increasing intervals.', 'options' => ['True', 'False'], 'correct_answer' => 'True', 'type' => 'true_false'],
            ['question' => 'Who developed the forgetting curve theory?', 'options' => ['Benjamin Bloom', 'Hermann Ebbinghaus', 'Jean Piaget', 'Lev Vygotsky'], 'correct_answer' => 'Hermann Ebbinghaus', 'type' => 'multiple_choice'],
            ['question' => 'Metacognition refers to thinking about one\'s own thinking processes.', 'options' => ['True', 'False'], 'correct_answer' => 'True', 'type' => 'true_false'],
            ['question' => 'What is the Pomodoro Technique?', 'options' => ['25 minutes work, 5 minutes break', '50 minutes work, 10 minutes break', '45 minutes work, 15 minutes break', '30 minutes work, 5 minutes break'], 'correct_answer' => '25 minutes work, 5 minutes break', 'type' => 'multiple_choice'],
            ['question' => 'What does "ELI5" stand for?', 'options' => ['Explain Like I\'m 5', 'Explain Like I\'m 50', 'Explain Like I\'m 15', 'Explain Like I\'m 25'], 'correct_answer' => 'Explain Like I\'m 5', 'type' => 'multiple_choice'],
        ];
        
        $questions = [];
        for ($i = 0; $i < min($count, count($sampleBank)); $i++) {
            $questions[] = $sampleBank[$i % count($sampleBank)];
        }
        return $questions;
    }

    private function extractTextFromFile($filePath, $extension)
    {
        $content = '';
        switch (strtolower($extension)) {
            case 'txt':
                $content = file_get_contents($filePath);
                break;
            case 'pdf':
                if (function_exists('shell_exec')) {
                    $content = shell_exec("pdftotext '{$filePath}' -");
                }
                if (empty($content)) {
                    $content = "PDF content will be processed. For better results, install pdftotext.";
                }
                break;
            case 'docx':
                $zip = new \ZipArchive();
                if ($zip->open($filePath) === true) {
                    if (($index = $zip->locateName('word/document.xml')) !== false) {
                        $data = $zip->getFromIndex($index);
                        $content = strip_tags(str_replace(['</w:p>', '</w:t>'], [' ', ' '], $data));
                    }
                    $zip->close();
                }
                break;
            default:
                $content = file_get_contents($filePath);
        }
        
        $content = preg_replace('/\s+/', ' ', strip_tags($content));
        $content = trim($content);
        
        if (strlen($content) > 15000) {
            $content = substr($content, 0, 15000) . "...";
        }
        
        return $content;
    }

    public function take($id)
    {
        $userId = $this->getCurrentUserId();
        $quiz = Quiz::where('user_id', $userId)->with('questions')->findOrFail($id);
        $settings = json_decode($quiz->settings ?? '{}', true);
        return view('quizzes.take', compact('quiz', 'settings'));
    }

    public function submit(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        $quiz = Quiz::where('user_id', $userId)->with('questions')->findOrFail($id);
        
        $score = 0;
        $total = count($quiz->questions);
        $answers = [];
        
        foreach ($quiz->questions as $question) {
            $userAnswer = $request->input('question_' . $question->id);
            $answers[$question->id] = $userAnswer;
            
            if ($userAnswer == $question->correct_answer) {
                $score++;
            }
        }
        
        $percentage = round(($score / max($total, 1)) * 100);
        
        $resultsKey = "quiz_answers_{$userId}";
        $scoreKey = "quiz_score_{$userId}";
        
        $allAnswers = Session::get($resultsKey, []);
        $allAnswers[$id] = $answers;
        Session::put($resultsKey, $allAnswers);
        
        $allScores = Session::get($scoreKey, []);
        $allScores[$id] = $percentage;
        Session::put($scoreKey, $allScores);
        
        return redirect()->route('quizzes.results', $id)->with('success', "You scored {$percentage}%!");
    }

    public function results($id)
    {
        $userId = $this->getCurrentUserId();
        $quiz = Quiz::where('user_id', $userId)->with('questions')->findOrFail($id);
        
        $resultsKey = "quiz_answers_{$userId}";
        $answers = Session::get($resultsKey, [])[$id] ?? [];
        
        $score = 0;
        $results = [];
        
        foreach ($quiz->questions as $question) {
            $userAnswer = $answers[$question->id] ?? 'Not answered';
            $isCorrect = $userAnswer === $question->correct_answer;
            
            if ($isCorrect) {
                $score++;
            }
            
            $results[] = [
                'question' => $question,
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect
            ];
        }
        
        $percentage = round(($score / max($quiz->questions->count(), 1)) * 100);
        
        return view('quizzes.results', compact('quiz', 'results', 'score', 'percentage'));
    }

    public function update(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        $quiz = Quiz::where('user_id', $userId)->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $quiz->update($validated);
        
        return response()->json(['success' => true, 'quiz' => $quiz]);
    }

    public function updateSettings(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        $quiz = Quiz::where('user_id', $userId)->findOrFail($id);
        
        $settings = json_decode($quiz->settings ?? '{}', true);
        
        if ($request->has('num_questions')) $settings['num_questions'] = $request->num_questions;
        if ($request->has('difficulty')) $settings['difficulty'] = $request->difficulty;
        if ($request->has('timer_enabled')) $settings['timer_enabled'] = $request->boolean('timer_enabled');
        if ($request->has('time_limit')) $settings['time_limit'] = $request->time_limit;
        
        $quiz->settings = json_encode($settings);
        $quiz->save();
        
        return response()->json(['success' => true, 'settings' => $settings]);
    }

    public function destroy($id)
    {
        $userId = $this->getCurrentUserId();
        $quiz = Quiz::where('user_id', $userId)->findOrFail($id);
        
        $quiz->questions()->delete();
        $quiz->delete();
        
        return redirect()->route('quizzes.index')->with('success', 'Quiz deleted successfully!');
    }

    public function resetForRetake($id)
    {
        $userId = $this->getCurrentUserId();
        $resultsKey = "quiz_answers_{$userId}";
        $scoreKey = "quiz_score_{$userId}";
        
        $allAnswers = Session::get($resultsKey, []);
        unset($allAnswers[$id]);
        Session::put($resultsKey, $allAnswers);
        
        $allScores = Session::get($scoreKey, []);
        unset($allScores[$id]);
        Session::put($scoreKey, $allScores);
        
        return redirect()->route('quizzes.take', $id)->with('success', 'Quiz reset! You can retake it now.');
    }
    
    public function updateDifficulty(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        $quiz = Quiz::where('user_id', $userId)->findOrFail($id);
        
        $settings = json_decode($quiz->settings ?? '{}', true);
        $settings['difficulty'] = $request->difficulty ?? 'medium';
        $quiz->settings = json_encode($settings);
        $quiz->save();
        
        return response()->json(['success' => true]);
    }
    
    public function saveAnswer(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $answerKey = "quiz_temp_answers_{$userId}";
        $answers = Session::get($answerKey, []);
        $answers[$request->quiz_id][$request->question_id] = $request->answer;
        Session::put($answerKey, $answers);
        
        return response()->json(['success' => true]);
    }
}