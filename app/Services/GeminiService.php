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

    /**
     * Generate flashcards from document text
     * 
     * @param string $text The extracted text from the document
     * @param int $count Number of flashcards to generate (default: 10)
     * @return string JSON encoded array of flashcards with 'question' and 'answer' keys
     */
    public function generateFlashcards($text, $count = 10)
    {
        try {
            // Limit text length to avoid token limits
            $truncatedText = substr($text, 0, 15000); // Reduced from 30000 to be safer
            
            // Build the prompt for flashcard generation - Simpler format
            $prompt = $this->buildSimplerFlashcardPrompt($truncatedText, $count);
            
            // Call Gemini API
            $response = $this->callGeminiForFlashcards($prompt);
            
            Log::info('Raw AI Response for flashcards', ['response' => $response]);
            
            // Parse the response into flashcards
            $flashcards = $this->parseFlexibleFlashcardResponse($response, $count);
            
            if (empty($flashcards)) {
                throw new \Exception('No flashcards generated from AI response');
            }
            
            return json_encode(['flashcards' => $flashcards]);
            
        } catch (\Exception $e) {
            Log::error('Flashcard generation failed: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            
            // Fallback to simple extraction from text
            $fallbackFlashcards = $this->fallbackFlashcardGeneration($text, $count);
            return json_encode(['flashcards' => $fallbackFlashcards]);
        }
    }

    /**
     * Build a simpler prompt for flashcard generation
     */
    protected function buildSimplerFlashcardPrompt($text, $count)
    {
        return "Create {$count} flashcards from this text. 

Format each flashcard as:
Question: [the question]
Answer: [the answer]

Separate each flashcard with a blank line.

Example:
Question: What is Artificial Intelligence?
Answer: AI is simulating human intelligence in machines.

Now create {$count} flashcards from this text:

{$text}";
    }

    /**
     * Parse the AI response into flashcards with flexible format
     */
    protected function parseFlexibleFlashcardResponse($response, $expectedCount)
    {
        $flashcards = [];
        
        // Method 1: Look for Question:/Answer: pattern
        if (preg_match_all('/Question:\s*(.+?)\s*Answer:\s*(.+?)(?=\n\n|\Z)/is', $response, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $flashcards[] = [
                    'question' => trim($match[1]),
                    'answer' => trim($match[2])
                ];
            }
            Log::info('Parsed using Question/Answer pattern', ['count' => count($flashcards)]);
        }
        
        // Method 2: Look for Q:/A: pattern
        if (empty($flashcards)) {
            if (preg_match_all('/Q:\s*(.+?)\s*A:\s*(.+?)(?=\n\n|\Z)/is', $response, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $flashcards[] = [
                        'question' => trim($match[1]),
                        'answer' => trim($match[2])
                    ];
                }
                Log::info('Parsed using Q/A pattern', ['count' => count($flashcards)]);
            }
        }
        
        // Method 3: Look for numbered list with question and answer
        if (empty($flashcards)) {
            if (preg_match_all('/(\d+)[\.\)]\s*(.+?)\s*[-–]\s*(.+?)(?=\n\d+\.|\n\n|\Z)/is', $response, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $flashcards[] = [
                        'question' => trim($match[2]),
                        'answer' => trim($match[3])
                    ];
                }
                Log::info('Parsed using numbered list pattern', ['count' => count($flashcards)]);
            }
        }
        
        // Method 4: Try to parse as JSON if it's valid
        if (empty($flashcards)) {
            $jsonAttempt = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($jsonAttempt['flashcards']) && is_array($jsonAttempt['flashcards'])) {
                    $flashcards = $jsonAttempt['flashcards'];
                } elseif (is_array($jsonAttempt) && isset($jsonAttempt[0]['question'])) {
                    $flashcards = $jsonAttempt;
                }
                Log::info('Parsed using JSON', ['count' => count($flashcards)]);
            }
        }
        
        // Clean up and validate flashcards
        $validFlashcards = [];
        foreach ($flashcards as $card) {
            $question = $card['question'] ?? ($card[0] ?? null);
            $answer = $card['answer'] ?? ($card[1] ?? null);
            
            if ($question && $answer && strlen($question) > 5 && strlen($answer) > 5) {
                $validFlashcards[] = [
                    'question' => trim(preg_replace('/\s+/', ' ', $question)),
                    'answer' => trim(preg_replace('/\s+/', ' ', $answer))
                ];
            }
        }
        
        // Limit to requested count
        $result = array_slice($validFlashcards, 0, $expectedCount);
        Log::info('Final valid flashcards', ['count' => count($result)]);
        
        return $result;
    }

    /**
     * Call Gemini API for flashcard generation
     */
    protected function callGeminiForFlashcards($prompt)
    {
        if (!$this->apiKey) {
            throw new \Exception('GEMINI_API_KEY not configured in .env file');
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=' . $this->apiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3, // Lower temperature for more consistent output
                'maxOutputTokens' => 4096,
                'topP' => 0.95,
                'topK' => 40
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        Log::info('Gemini API Response Code', ['http_code' => $httpCode]);
        
        if ($curlError) {
            throw new \Exception('CURL Error: ' . $curlError);
        }
        
        if ($httpCode !== 200) {
            // Log the error response for debugging
            $errorResponse = json_decode($response, true);
            $errorMsg = isset($errorResponse['error']['message']) ? $errorResponse['error']['message'] : "HTTP {$httpCode}";
            throw new \Exception("API Error: {$errorMsg}");
        }
        
        $result = json_decode($response, true);
        
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception('Invalid API response structure: ' . json_encode($result));
        }
        
        $text = $result['candidates'][0]['content']['parts'][0]['text'];
        
        if (empty($text)) {
            throw new \Exception('Empty response from Gemini API');
        }
        
        return $text;
    }

    /**
     * Fallback method when AI fails
     */
    protected function fallbackFlashcardGeneration($text, $count)
    {
        $flashcards = [];
        
        // Split text into sentences
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Create simple flashcards from sentences
        foreach ($sentences as $sentence) {
            if (count($flashcards) >= $count) break;
            
            $sentence = trim($sentence);
            if (strlen($sentence) > 30 && strlen($sentence) < 500) {
                // Extract key terms (simple heuristic)
                $words = explode(' ', $sentence);
                $keyTerms = [];
                
                // Look for capitalized words or long words as potential key terms
                foreach ($words as $word) {
                    $cleanWord = preg_replace('/[^a-zA-Z]/', '', $word);
                    if (strlen($cleanWord) > 5 && ctype_upper(substr($cleanWord, 0, 1))) {
                        $keyTerms[] = $cleanWord;
                    }
                }
                
                if (!empty($keyTerms)) {
                    $question = "What is " . implode(' or ', array_slice($keyTerms, 0, 2)) . "?";
                    $answer = substr($sentence, 0, 250);
                } else {
                    // Create a question based on the first few words
                    $firstWords = implode(' ', array_slice($words, 0, 5));
                    $question = "What does the text say about " . $firstWords . "...?";
                    $answer = substr($sentence, 0, 250);
                }
                
                $flashcards[] = [
                    'question' => $question,
                    'answer' => $answer . (strlen($sentence) > 250 ? '...' : '')
                ];
            }
        }
        
        // If still no flashcards, create generic ones
        if (empty($flashcards)) {
            for ($i = 0; $i < min($count, 5); $i++) {
                $flashcards[] = [
                    'question' => "What is key concept " . ($i + 1) . " from the document?",
                    'answer' => "Review the original document for details on this concept. The document discusses important topics in the uploaded file."
                ];
            }
        }
        
        Log::info('Fallback flashcards generated', ['count' => count($flashcards)]);
        
        return $flashcards;
    }

    private function callGemini($prompt)
    {
        if (!$this->apiKey) {
            return "⚠️ API Key Missing: Please add GEMINI_API_KEY to your .env file.";
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=' . $this->apiKey;
        
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
            'internet' => "🌐 **How the Internet Works (Simple Explanation)**\n\nThink of the internet as a giant network of computers all connected together, like a massive spider web!\n\n📦 **Data Packets:** Information is broken into small pieces called \"packets\"\n🚚 **Routers:** These are like traffic directors that send packets the fastest way\n🗺️ **IP Addresses:** Every device has a unique address (like your home address)\n📡 **DNS:** Like a phonebook that turns website names into numbers\n\nWhen you visit a website, your computer sends packets that hop from router to router until they reach the destination, then come back with the website data!\n\n💡 *The AI service is temporarily unavailable, but you can learn more by trying again in a few minutes!*",
            
            'computer' => "💻 **What is a Computer?**\n\nA computer is an electronic device that processes data and performs tasks according to instructions (programs).\n\n**Basic Parts:**\n• CPU (Central Processing Unit) - The brain\n• RAM (Memory) - Short-term memory\n• Storage (SSD/HDD) - Long-term memory\n• Input devices (keyboard, mouse)\n• Output devices (screen, speakers)\n\n💡 *Try asking again when the AI service is back!*",
            
            'ai' => "🤖 **What is Artificial Intelligence?**\n\nAI is technology that enables computers to perform tasks that normally require human intelligence - like understanding language, recognizing images, or making decisions.\n\n**Types of AI:**\n• Narrow AI - Specialized (like voice assistants)\n• General AI - Human-like (still in development)\n\n💡 *The AI assistant is currently experiencing high demand. Please try again shortly!*",
            
            'programming' => "💻 **What is Programming?**\n\nProgramming is giving computers instructions to perform specific tasks, using languages they understand (like Python, JavaScript, or PHP).\n\n**Think of it like:** Writing a recipe that the computer follows step-by-step!\n\n💡 *More details will be available when the AI service is back online.*",
            
            'database' => "🗄️ **What is a Database?**\n\nA database is an organized collection of data stored electronically. Think of it like a digital filing cabinet!\n\n• Tables store data in rows and columns\n• Queries help you find specific information\n• Most websites use databases to store user data\n\n💡 *Try again in a few minutes for more information!*"
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