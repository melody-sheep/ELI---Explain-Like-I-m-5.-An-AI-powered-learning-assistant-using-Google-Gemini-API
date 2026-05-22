<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Conversation;
use App\Traits\GetCurrentUserId;
use Illuminate\Support\Facades\Session;

class AIController extends Controller
{
    use GetCurrentUserId;
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function ask(Request $request)
    {
        try {
            $request->validate(['question' => 'required|string']);
            $question = $request->question;
            $userId = $this->getCurrentUserId();
            $sessionId = $userId . '_' . Session::getId();
            
            $answer = $this->gemini->askWithMemory($question, $sessionId, 'ask');
            
            Conversation::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'mode' => 'ask',
                'user_input' => $question,
                'ai_response' => $answer,
            ]);
            
            $formattedAnswer = "📝 Question: " . $question . "\n\n🤖 Answer:\n" . $answer;
            
            return response()->json(['answer' => $formattedAnswer], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('Ask error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function summarize(Request $request)
    {
        try {
            $request->validate(['text' => 'required|string']);
            $text = $request->text;
            $userId = $this->getCurrentUserId();
            $sessionId = $userId . '_' . Session::getId();
            
            $prompt = "Summarize the following text in 3-5 bullet points:\n\n" . $text;
            $summary = $this->gemini->askWithMemory($prompt, $sessionId, 'summarize');
            
            Conversation::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'mode' => 'summarize',
                'user_input' => $text,
                'ai_response' => $summary,
            ]);
            
            $textPreview = strlen($text) > 200 ? substr($text, 0, 200) . '...' : $text;
            $formattedSummary = "📄 Original Text:\n" . $textPreview . "\n\n📝 Summary:\n" . $summary;
            
            return response()->json(['summary' => $formattedSummary], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('Summarize error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function eli5(Request $request)
    {
        try {
            $request->validate(['text' => 'required|string']);
            $text = $request->text;
            $userId = $this->getCurrentUserId();
            $sessionId = $userId . '_' . Session::getId();
            
            $prompt = "Explain the following concept like I'm 5 years old. Use simple words and fun examples:\n\n" . $text;
            $explanation = $this->gemini->askWithMemory($prompt, $sessionId, 'eli5');
            
            Conversation::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'mode' => 'eli5',
                'user_input' => $text,
                'ai_response' => $explanation,
            ]);
            
            $formattedExplanation = "🧸 ELI5 Question: " . $text . "\n\n📖 Simplified Explanation:\n" . $explanation;
            
            return response()->json(['explanation' => $formattedExplanation], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('ELI5 error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function explainCode(Request $request)
    {
        try {
            $request->validate(['code' => 'required|string']);
            $code = $request->code;
            $userId = $this->getCurrentUserId();
            $sessionId = $userId . '_' . Session::getId();
            
            $prompt = "Explain the following code line by line. Tell me what each part does:\n\n" . $code;
            $explanation = $this->gemini->askWithMemory($prompt, $sessionId, 'code');
            
            Conversation::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'mode' => 'code',
                'user_input' => $code,
                'ai_response' => $explanation,
            ]);
            
            $formattedExplanation = "💻 Code:\n" . $code . "\n\n🔍 Explanation:\n" . $explanation;
            
            return response()->json(['explanation' => $formattedExplanation], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('Explain code error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function history()
    {
        try {
            $conversations = Conversation::where('user_id', $this->getCurrentUserId())
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json($conversations, 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('History error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($id)
    {
        try {
            $conversation = Conversation::where('id', $id)
                ->where('user_id', $this->getCurrentUserId())
                ->firstOrFail();
            $conversation->delete();
            return response()->json(['success' => true], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('Delete error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    public function clearAll()
    {
        try {
            Conversation::where('user_id', $this->getCurrentUserId())->delete();
            return response()->json(['success' => true], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}