<?php

namespace App\Services;

use App\Models\Conversation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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

            $response = $this->callGemini($fullPrompt);
            
            // Check if response contains an API error
            if ($this->isApiError($response)) {
                return $this->getFallbackResponse($prompt, $response);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            Log::error('GeminiService Error: ' . $e->getMessage());
            return $this->getFallbackResponse($prompt, $e->getMessage());
        }
    }

    private function callGemini($prompt)
    {
        if (!$this->apiKey) {
            return "⚠️ API Key Missing: Please add GEMINI_API_KEY to your .env file.";
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
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            return "🔌 Connection Error: " . $curlError;
        }
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response text';
        }
        
        // Return structured error based on HTTP code
        $errorMessage = $this->getErrorMessage($httpCode, $response);
        return $errorMessage;
    }
    
    private function getErrorMessage($httpCode, $response = null)
    {
        switch ($httpCode) {
            case 429:
                return "⏳ **Rate Limit Exceeded**\n\nYou've reached the API request limit. Please wait 1-2 minutes before trying again.\n\n💡 *Tip: The free tier allows 60 requests per minute.*";
            case 503:
                return "🔄 **Service Temporarily Unavailable**\n\nThe AI service is currently busy or at capacity. Please wait a moment and try again.\n\n💡 *This usually resolves within a few minutes.*";
            case 400:
                return "❌ **Invalid Request**\n\nThe request could not be processed. Please try rephrasing your question.";
            case 401:
                return "🔑 **Authentication Error**\n\nYour API key is invalid or expired. Please check your GEMINI_API_KEY in .env";
            case 403:
                return "🚫 **Access Denied**\n\nYour API key doesn't have permission to access this service.";
            case 500:
                return "⚠️ **Server Error**\n\nThe AI service is experiencing issues. Please try again in a few minutes.";
            default:
                return "📡 **API Error (HTTP {$httpCode})**\n\nThe AI service is currently unavailable. Please try again later.\n\n💡 *This is usually temporary.*";
        }
    }
    
    private function isApiError($response)
    {
        $errorPatterns = [
            'API Error',
            'Rate Limit Exceeded',
            'Service Unavailable',
            'Authentication Error',
            'Connection Error',
            'API Key Missing'
        ];
        
        foreach ($errorPatterns as $pattern) {
            if (strpos($response, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function getFallbackResponse($prompt, $errorDetails = null)
    {
        // Smart fallback based on question keywords
        $lowerPrompt = strtolower($prompt);
        
        $fallbacks = [
            'internet' => "🌐 **How the Internet Works (Simple Explanation)**

    Think of the internet as a giant network of computers all connected together, like a massive spider web!

    📦 **Data Packets:** Information is broken into small pieces called \"packets\"
    🚚 **Routers:** These are like traffic directors that send packets the fastest way
    🗺️ **IP Addresses:** Every device has a unique address (like your home address)
    📡 **DNS:** Like a phonebook that turns website names into numbers

    When you visit a website, your computer sends packets that hop from router to router until they reach the destination, then come back with the website data!

    💡 *The AI service is temporarily unavailable, but you can learn more by trying again in a few minutes!*",
            
            'computer' => "💻 **What is a Computer?**

    A computer is an electronic device that processes data and performs tasks according to instructions (programs).

    **Basic Parts:**
    • CPU (Central Processing Unit) - The brain
    • RAM (Memory) - Short-term memory
    • Storage (SSD/HDD) - Long-term memory
    • Input devices (keyboard, mouse)
    • Output devices (screen, speakers)

    💡 *Try asking again when the AI service is back!*",
            
            'ai' => "🤖 **What is Artificial Intelligence?**

    AI is technology that enables computers to perform tasks that normally require human intelligence - like understanding language, recognizing images, or making decisions.

    **Types of AI:**
    • Narrow AI - Specialized (like voice assistants)
    • General AI - Human-like (still in development)

    💡 *The AI assistant is currently experiencing high demand. Please try again shortly!*",
            
            'programming' => "💻 **What is Programming?**

    Programming is giving computers instructions to perform specific tasks, using languages they understand (like Python, JavaScript, or PHP).

    **Think of it like:** Writing a recipe that the computer follows step-by-step!

    💡 *More details will be available when the AI service is back online.*",
            
            'database' => "🗄️ **What is a Database?**

    A database is an organized collection of data stored electronically. Think of it like a digital filing cabinet!

    • Tables store data in rows and columns
    • Queries help you find specific information
    • Most websites use databases to store user data

    💡 *Try again in a few minutes for more information!*"
        ];
        
        // Find matching fallback
        foreach ($fallbacks as $key => $fallback) {
            if (strpos($lowerPrompt, $key) !== false) {
                return $fallback;
            }
        }
        
        // Generic fallback with the user's question context
        $questionContext = ucfirst(substr($prompt, 0, 100));
        
        return "🤔 **I'd love to answer your question about:**\n\n\"{$questionContext}...\"\n\n" .
            "📡 **Current Status:** The AI service is temporarily unavailable (API limit reached).\n\n" .
            "**What you can do:**\n" .
            "• Wait 1-2 minutes and try again\n" .
            "• The rate limit resets automatically\n" .
            "• Your question will be answered once the service is back\n\n" .
            "💡 *This is a temporary issue - please try again shortly!*";
    }
}