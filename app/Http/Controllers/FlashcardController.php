<?php
namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\FlashcardMastery;
use App\Models\Lesson;
use App\Services\GeminiLMSService;
use App\Services\TextExtractorService;
use App\Traits\GetCurrentUserId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FlashcardController extends Controller
{
    use GetCurrentUserId;
    protected $gemini;
    protected $textExtractor;

    public function __construct(GeminiLMSService $gemini, TextExtractorService $textExtractor)
    {
        $this->gemini = $gemini;
        $this->textExtractor = $textExtractor;
    }

    public function index()
    {
        $userId = $this->getCurrentUserId();
        
        $decks = Lesson::where('user_id', $userId)
            ->whereHas('flashcards')
            ->with(['flashcards.mastery'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Delete orphaned flashcards
        Flashcard::where('user_id', $userId)->whereNull('lesson_id')->delete();
        
        $allLessons = Lesson::where('user_id', $userId)->get();
        
        return view('flashcards.index', compact('decks', 'allLessons'));
    }
    
    public function deck($lessonId)
    {
        $userId = $this->getCurrentUserId();
        
        $deck = Lesson::where('user_id', $userId)
            ->with(['flashcards.mastery'])
            ->findOrFail($lessonId);
        
        foreach ($deck->flashcards as $card) {
            $card->mastery_level = $card->mastery ? $card->mastery->mastery_level : 'new';
        }
        
        return view('flashcards.deck', compact('deck'));
    }

    public function generate()
    {
        $userId = $this->getCurrentUserId();
        $lessons = Lesson::where('user_id', $userId)->get();
        return view('flashcards.generate', compact('lessons'));
    }

    public function store(Request $request)
    {
        Log::info('=== FLASHCARD GENERATION STARTED ===');
        
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:20480',
            'num_cards' => 'nullable|integer|min:5|max:30'
        ]);

        $userId = $this->getCurrentUserId();
        $file = $request->file('file');
        $filePath = $file->store('flashcard-sources', 'public');
        $fullPath = Storage::disk('public')->path($filePath);
        $numCards = $request->num_cards ?? 10;
        
        Log::info('File uploaded', ['path' => $fullPath, 'size' => $file->getSize()]);
        
        // Extract text from file
        $content = $this->textExtractor->extract($fullPath, $file->getClientOriginalExtension());
        
        Log::info('Content extracted', ['length' => strlen($content)]);
        
        if (empty($content) || strlen($content) < 50) {
            Log::error('Text extraction failed');
            return back()->with('error', 'Could not extract readable text from file. Please ensure the file contains text, not just images.');
        }
        
        // Create lesson deck
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $lesson = Lesson::create([
            'user_id' => $userId,
            'title' => $originalName,
            'description' => "Flashcards generated from {$originalName}",
            'subject' => 'Flashcards'
        ]);
        
        Log::info('Lesson created', ['lesson_id' => $lesson->id]);
        
        // Generate flashcards using AI
        try {
            $aiResponse = $this->gemini->generateFlashcards($content, $numCards);
            Log::info('AI Response received', ['response' => $aiResponse]);
            
            // Parse the JSON response
            $decoded = json_decode($aiResponse, true);
            
            // Handle different response formats
            $flashcards = [];
            if (isset($decoded['flashcards']) && is_array($decoded['flashcards'])) {
                $flashcards = $decoded['flashcards'];
            } elseif (is_array($decoded) && !isset($decoded['flashcards'])) {
                // If response is directly an array of flashcards
                $flashcards = $decoded;
            }
            
            Log::info('Parsed flashcards', ['count' => count($flashcards)]);
            
            if (empty($flashcards)) {
                throw new \Exception('No flashcards generated');
            }
            
        } catch (\Exception $e) {
            Log::error('AI generation failed: ' . $e->getMessage());
            $lesson->delete();
            return back()->with('error', 'AI generation failed. Please try again.');
        }
        
        // Save flashcards
        $savedCount = 0;
        foreach ($flashcards as $card) {
            // Handle both associative and indexed arrays
            $question = is_array($card) ? ($card['question'] ?? $card[0] ?? null) : null;
            $answer = is_array($card) ? ($card['answer'] ?? $card[1] ?? null) : null;
            
            if ($question && $answer) {
                $flashcard = Flashcard::create([
                    'user_id' => $userId,
                    'lesson_id' => $lesson->id,
                    'question' => trim($question),
                    'answer' => trim($answer),
                    'source_file' => $filePath
                ]);
                
                FlashcardMastery::create([
                    'user_id' => $userId,
                    'flashcard_id' => $flashcard->id,
                    'mastery_level' => 'new',
                    'times_reviewed' => 0
                ]);
                
                $savedCount++;
            }
        }
        
        Log::info('Flashcards saved', ['count' => $savedCount]);
        
        if ($savedCount === 0) {
            $lesson->delete();
            return back()->with('error', 'No valid flashcards were generated. Please try again.');
        }
        
        return redirect()->route('flashcards.deck', $lesson->id)
            ->with('success', "{$savedCount} flashcards generated from '{$originalName}'!");
    }
    
    public function updateMastery(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        
        $request->validate([
            'mastery_level' => 'required|in:new,learning,mastered,review'
        ]);
        
        $mastery = FlashcardMastery::where('user_id', $userId)
            ->where('flashcard_id', $id)
            ->first();
        
        if ($mastery) {
            $mastery->update([
                'mastery_level' => $request->mastery_level,
                'times_reviewed' => $mastery->times_reviewed + 1,
                'last_reviewed_at' => now()
            ]);
        } else {
            FlashcardMastery::create([
                'user_id' => $userId,
                'flashcard_id' => $id,
                'mastery_level' => $request->mastery_level,
                'times_reviewed' => 1,
                'last_reviewed_at' => now()
            ]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function deleteDeck($lessonId)
    {
        $userId = $this->getCurrentUserId();
        $lesson = Lesson::where('user_id', $userId)->findOrFail($lessonId);
        
        foreach ($lesson->flashcards as $flashcard) {
            FlashcardMastery::where('flashcard_id', $flashcard->id)->delete();
            $flashcard->delete();
        }
        
        $lesson->delete();
        
        return redirect()->route('flashcards.index')->with('success', 'Deck deleted successfully!');
    }
    
    public function destroy($id)
    {
        $userId = $this->getCurrentUserId();
        $flashcard = Flashcard::where('user_id', $userId)->findOrFail($id);
        $lessonId = $flashcard->lesson_id;
        
        FlashcardMastery::where('flashcard_id', $id)->delete();
        $flashcard->delete();
        
        if ($lessonId) {
            $remainingCards = Flashcard::where('lesson_id', $lessonId)->count();
            if ($remainingCards === 0) {
                Lesson::where('id', $lessonId)->delete();
                return redirect()->route('flashcards.index')->with('success', 'Card deleted. Empty deck removed.');
            }
            return redirect()->route('flashcards.deck', $lessonId)->with('success', 'Flashcard deleted!');
        }
        
        return redirect()->route('flashcards.index')->with('success', 'Flashcard deleted!');
    }
}