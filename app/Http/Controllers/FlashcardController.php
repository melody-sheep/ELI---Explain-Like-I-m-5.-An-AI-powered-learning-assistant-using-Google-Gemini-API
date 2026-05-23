<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\FlashcardMastery;
use App\Models\Lesson;
use App\Services\GeminiService;
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

    public function __construct(GeminiService $gemini, TextExtractorService $textExtractor)
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
        try {
            Log::info('=== FLASHCARD GENERATION STARTED ===');
            
            // Validate request
            $validator = $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,txt|max:20480',
                'num_cards' => 'nullable|integer|min:5|max:30'
            ]);

            $userId = $this->getCurrentUserId();
            $file = $request->file('file');
            
            // Check if file was uploaded
            if (!$file) {
                Log::error('No file uploaded');
                return back()->with('error', '❌ No file uploaded. Please select a file.');
            }
            
            // Check file size
            if ($file->getSize() > 20480 * 1024) {
                Log::error('File too large', ['size' => $file->getSize()]);
                return back()->with('error', '❌ File too large. Maximum size is 20MB.');
            }
            
            // Store file
            $filePath = $file->store('flashcard-sources', 'public');
            $fullPath = Storage::disk('public')->path($filePath);
            $numCards = $request->num_cards ?? 10;
            
            Log::info('File uploaded', ['path' => $fullPath, 'size' => $file->getSize(), 'original_name' => $file->getClientOriginalName()]);
            
            // Check if file exists after storage
            if (!file_exists($fullPath)) {
                Log::error('File not found after storage', ['path' => $fullPath]);
                return back()->with('error', '❌ File storage failed. Please try again.');
            }
            
            // Extract text from file
            Log::info('Starting text extraction...');
            $content = $this->textExtractor->extract($fullPath, $file->getClientOriginalExtension());
            
            Log::info('Content extracted', ['length' => strlen($content), 'preview' => substr($content, 0, 200)]);
            
            if (empty($content) || strlen($content) < 50) {
                Log::error('Text extraction failed - content too short', ['length' => strlen($content)]);
                
                // Try to read raw file content for debugging
                $rawContent = file_get_contents($fullPath);
                Log::debug('Raw file content length', ['raw_length' => strlen($rawContent)]);
                
                return back()->with('error', '❌ Could not extract readable text from file. Please ensure the file contains actual text, not just images or scanned pages. Supported: PDF, DOCX, DOC, TXT with selectable text.');
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
            
            // Check Gemini API key
            if (!env('GEMINI_API_KEY')) {
                Log::error('GEMINI_API_KEY not configured');
                $lesson->delete();
                return back()->with('error', '❌ AI Service Error: GEMINI_API_KEY is not configured in .env file. Please check your configuration.');
            }
            
            // Generate flashcards using AI
            Log::info('Calling Gemini API for flashcard generation...');
            try {
                $aiResponse = $this->gemini->generateFlashcards($content, $numCards);
                Log::info('AI Response received', ['response_length' => strlen($aiResponse), 'preview' => substr($aiResponse, 0, 500)]);
                
                // Parse the JSON response
                $decoded = json_decode($aiResponse, true);
                
                // Check for JSON errors
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('JSON decode error', ['error' => json_last_error_msg(), 'response' => $aiResponse]);
                    throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
                }
                
                // Handle different response formats
                $flashcards = [];
                if (isset($decoded['flashcards']) && is_array($decoded['flashcards'])) {
                    $flashcards = $decoded['flashcards'];
                    Log::info('Using flashcards from decoded.flashcards', ['count' => count($flashcards)]);
                } elseif (is_array($decoded) && !isset($decoded['flashcards'])) {
                    $flashcards = $decoded;
                    Log::info('Using direct decoded array', ['count' => count($flashcards)]);
                } else {
                    Log::warning('Unexpected response format', ['type' => gettype($decoded)]);
                    throw new \Exception('AI returned unexpected format');
                }
                
                if (empty($flashcards)) {
                    throw new \Exception('No flashcards generated from AI response');
                }
                
            } catch (\Exception $e) {
                Log::error('AI generation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                $lesson->delete();
                
                // Provide specific error message based on the exception
                $errorMessage = '❌ AI Generation Failed: ';
                if (strpos($e->getMessage(), 'API key') !== false) {
                    $errorMessage .= 'Invalid or missing API key. Please check GEMINI_API_KEY in .env';
                } elseif (strpos($e->getMessage(), 'CURL') !== false) {
                    $errorMessage .= 'Network error. Please check your internet connection.';
                } elseif (strpos($e->getMessage(), 'HTTP') !== false) {
                    $errorMessage .= 'API service error. Please try again later.';
                } else {
                    $errorMessage .= $e->getMessage();
                }
                
                return back()->with('error', $errorMessage . ' Please try again with a different file or fewer flashcards.');
            }
            
            // Save flashcards
            $savedCount = 0;
            $errors = [];
            
            foreach ($flashcards as $index => $card) {
                try {
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
                    } else {
                        $errors[] = "Card " . ($index + 1) . " missing question or answer";
                        Log::warning('Invalid flashcard format', ['card' => $card]);
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error saving card " . ($index + 1) . ": " . $e->getMessage();
                    Log::error('Failed to save flashcard', ['error' => $e->getMessage(), 'card' => $card]);
                }
            }
            
            Log::info('Flashcards saved', ['count' => $savedCount, 'errors' => $errors]);
            
            if ($savedCount === 0) {
                $lesson->delete();
                $errorDetail = !empty($errors) ? implode(', ', $errors) : 'No valid flashcards were generated';
                return back()->with('error', '❌ ' . $errorDetail . '. Please try again with a different file.');
            }
            
            $message = "✅ {$savedCount} flashcards generated successfully from '{$originalName}'!";
            if (!empty($errors)) {
                $message .= " (Note: " . count($errors) . " cards were skipped due to formatting issues)";
            }
            
            return redirect()->route('flashcards.deck', $lesson->id)
                ->with('success', $message);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->with('error', '❌ Validation failed: ' . implode(', ', array_merge(...array_values($e->errors()))));
            
        } catch (\Exception $e) {
            Log::error('Unexpected error in flashcard generation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', '❌ Unexpected error: ' . $e->getMessage() . ' Please check logs for details.');
        }
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