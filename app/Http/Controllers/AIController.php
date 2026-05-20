<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AIController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
        $this->ensureUserExists();
    }

    private function ensureUserExists()
    {
        $userId = Session::get('user_id');
        
        if (!$userId) {
            // Use default user or create one
            $user = User::firstOrCreate(
                ['id' => 1],
                ['name' => 'Default User', 'email' => 'default@example.com', 'password' => bcrypt('password')]
            );
            Session::put('user_id', $user->id);
        } elseif (!User::where('id', $userId)->exists()) {
            // If user_id in session doesn't exist, fallback to default
            $user = User::firstOrCreate(
                ['id' => 1],
                ['name' => 'Default User', 'email' => 'default@example.com', 'password' => bcrypt('password')]
            );
            Session::put('user_id', $user->id);
        }
    }

    public function ask(Request $request)
    {
        try {
            $request->validate(['question' => 'required|string']);
            $answer = $this->gemini->ask($request->question);
            $this->saveConversation('ask', $request->question, $answer);
            return response()->json(['answer' => $answer], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Question is required'], 422);
        } catch (\Exception $e) {
            \Log::error('Ask error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function summarize(Request $request)
    {
        try {
            $request->validate(['text' => 'required|string']);
            $prompt = "Summarize the following text in 3-5 bullet points:\n\n" . $request->text;
            $summary = $this->gemini->ask($prompt);
            $this->saveConversation('summarize', $request->text, $summary);
            return response()->json(['summary' => $summary], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Text is required'], 422);
        } catch (\Exception $e) {
            \Log::error('Summarize error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function eli5(Request $request)
    {
        try {
            $request->validate(['text' => 'required|string']);
            $prompt = "Explain the following concept like I'm 5 years old. Use simple words and fun examples:\n\n" . $request->text;
            $explanation = $this->gemini->ask($prompt);
            $this->saveConversation('eli5', $request->text, $explanation);
            return response()->json(['explanation' => $explanation], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Text is required'], 422);
        } catch (\Exception $e) {
            \Log::error('ELI5 error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function explainCode(Request $request)
    {
        try {
            $request->validate(['code' => 'required|string']);
            $prompt = "Explain the following code line by line. Tell me what each part does:\n\n" . $request->code;
            $explanation = $this->gemini->ask($prompt);
            $this->saveConversation('code', $request->code, $explanation);
            return response()->json(['explanation' => $explanation], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Code is required'], 422);
        } catch (\Exception $e) {
            \Log::error('Explain code error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function saveConversation($mode, $input, $response)
    {
        $userId = Session::get('user_id', 1);
        Conversation::create([
            'user_id' => $userId,
            'mode' => $mode,
            'user_input' => $input,
            'ai_response' => $response,
        ]);
    }

    public function history()
    {
        try {
            $userId = Session::get('user_id', 1);
            $conversations = Conversation::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
            return response()->json($conversations, 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('History error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function delete($id)
    {
        try {
            $conversation = Conversation::findOrFail($id);
            $conversation->delete();
            return response()->json(['success' => true], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            \Log::error('Delete error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}