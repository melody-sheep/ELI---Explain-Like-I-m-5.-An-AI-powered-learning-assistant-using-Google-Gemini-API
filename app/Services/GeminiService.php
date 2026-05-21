<?php

namespace App\Services;

use App\Models\Conversation;
use Illuminate\Support\Facades\Session;

class GeminiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function askWithMemory($prompt, $sessionId, $mode = 'ask')
    {
        try {
            // Get last 10 conversations for context
            $history = Conversation::where('session_id', $sessionId)
                ->where('mode', $mode)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse();

            // Build conversation context
            $conversationHistory = "";
            foreach ($history as $conv) {
                $conversationHistory .= "User: " . $conv->user_input . "\n";
                $conversationHistory .= "Assistant: " . $conv->ai_response . "\n\n";
            }

            // Add memory instruction
            $fullPrompt = "";
            if (!empty($conversationHistory)) {
                $fullPrompt = "Here is the conversation history so far:\n\n" . $conversationHistory;
                $fullPrompt .= "\nNow respond to this: " . $prompt;
                $fullPrompt .= "\n\nIMPORTANT: Remember the conversation above. Be consistent. If the user refers to something from earlier, reference it.";
            } else {
                $fullPrompt = $prompt . "\n\nIMPORTANT: Return your response as plain text only. Do NOT use any markdown formatting.";
            }

            return $this->callGemini($fullPrompt);
            
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    private function callGemini($prompt)
    {
        if (!$this->apiKey) {
            return "Error: GEMINI_API_KEY is missing.";
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $this->apiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response text';
        }
        
        return "API Error (HTTP $httpCode)";
    }
}