<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GeminiLMSService
{
    protected $apiKey;
    protected $useMock;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $mockValue = env('USE_MOCK_AI', 'false');
        $this->useMock = $mockValue === 'true' || $mockValue === true;
        
        Log::info('GeminiLMSService initialized', [
            'api_key_exists' => !empty($this->apiKey),
            'use_mock' => $this->useMock
        ]);
    }

    public function generateFlashcards($content, $numCards = 10)
    {
        Log::info('generateFlashcards called', [
            'content_length' => strlen($content),
            'num_cards' => $numCards
        ]);
        
        if (!$this->apiKey || $this->useMock) {
            return $this->createFlashcardsFromContent($content, $numCards);
        }

        try {
            $prompt = $this->buildFlashcardPrompt($content, $numCards);
            $response = $this->callGemini($prompt);
            
            // Check if response indicates API error
            if ($response && $this->isApiErrorResponse($response)) {
                return $this->createFlashcardsFromContent($content, $numCards);
            }
            
            if ($response && $this->isValidJson($response)) {
                $decoded = json_decode($response, true);
                if (isset($decoded['flashcards']) && count($decoded['flashcards']) > 0) {
                    return json_encode(['flashcards' => $decoded['flashcards']]);
                }
            }
            
            return $this->createFlashcardsFromContent($content, $numCards);
            
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            return $this->createFlashcardsFromContent($content, $numCards);
        }
    }
    
    public function generateQuiz($content, $numQuestions = 10, $difficulty = 'medium')
    {
        Log::info('generateQuiz called', [
            'content_length' => strlen($content),
            'num_questions' => $numQuestions,
            'difficulty' => $difficulty
        ]);
        
        if (!$this->apiKey || $this->useMock) {
            return $this->createQuizFromContent($content, $numQuestions);
        }
        
        try {
            $prompt = $this->buildQuizPrompt($content, $numQuestions, $difficulty);
            $response = $this->callGemini($prompt);
            
            if ($response && $this->isApiErrorResponse($response)) {
                return $this->createQuizFromContent($content, $numQuestions);
            }
            
            if ($response && $this->isValidJson($response)) {
                $decoded = json_decode($response, true);
                if (isset($decoded['questions']) && count($decoded['questions']) > 0) {
                    return json_encode(['questions' => $decoded['questions']]);
                }
            }
            
            return $this->createQuizFromContent($content, $numQuestions);
            
        } catch (\Exception $e) {
            Log::error('Gemini Quiz API Error: ' . $e->getMessage());
            return $this->createQuizFromContent($content, $numQuestions);
        }
    }
    
    private function createQuizFromContent($content, $numQuestions)
    {
        Log::info('Creating quiz from content directly (API fallback)');
        
        // Extract key sentences
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $questions = [];
        
        foreach ($sentences as $index => $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 30 && count($questions) < $numQuestions) {
                $questions[] = [
                    'question' => $this->sentenceToQuestion($sentence),
                    'options' => $this->generateOptions($sentence, $sentences),
                    'correct_answer' => $this->extractKeyPhrase($sentence),
                    'explanation' => $sentence
                ];
            }
        }
        
        // If no questions, add sample
        if (empty($questions)) {
            $questions[] = [
                'question' => 'What is the main topic of this document?',
                'options' => ['Topic A', 'Topic B', 'Topic C', 'Topic D'],
                'correct_answer' => 'Review the document for the main topic',
                'explanation' => 'Read the document carefully to identify the main subject matter.'
            ];
        }
        
        return json_encode(['questions' => $questions]);
    }
    
    private function generateOptions($correctAnswer, $allSentences)
    {
        $options = [$correctAnswer];
        $keyPhrases = [];
        
        // Extract key phrases from other sentences
        foreach ($allSentences as $sentence) {
            if ($sentence !== $correctAnswer && strlen($sentence) > 10) {
                $phrase = substr($sentence, 0, 50);
                if (!in_array($phrase, $options)) {
                    $keyPhrases[] = $phrase;
                }
            }
        }
        
        // Add up to 3 distractors
        $distractors = array_slice($keyPhrases, 0, 3);
        $options = array_merge($options, $distractors);
        
        // Shuffle options
        shuffle($options);
        
        return $options;
    }
    
    private function extractKeyPhrase($sentence)
    {
        // Extract first 50 characters as key phrase
        return substr($sentence, 0, 100);
    }

    private function createFlashcardsFromContent($content, $numCards)
    {
        Log::info('Creating flashcards from content directly (API fallback)');
        
        // Split content into sentences
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $flashcards = [];
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 20 && strlen($sentence) < 300 && count($flashcards) < $numCards) {
                $question = $this->sentenceToQuestion($sentence);
                $flashcards[] = [
                    'question' => $question,
                    'answer' => $sentence
                ];
            }
        }
        
        // If still no flashcards, extract key phrases
        if (empty($flashcards)) {
            preg_match_all('/([A-Z][a-z]+(?:\s+[A-Za-z]+){1,5})/', $content, $matches);
            $keyPhrases = array_unique($matches[0]);
            
            foreach ($keyPhrases as $phrase) {
                if (strlen($phrase) > 10 && count($flashcards) < $numCards) {
                    $flashcards[] = [
                        'question' => "What is {$phrase}?",
                        'answer' => "Review the document for information about {$phrase}."
                    ];
                }
            }
        }
        
        // Add fallback message for API limit
        $fallbackNote = "\n\n⚠️ *Note: Generated from document content (AI API temporarily unavailable)*";
        
        return json_encode(['flashcards' => $flashcards, 'fallback_mode' => true, 'fallback_note' => $fallbackNote]);
    }
    
    private function sentenceToQuestion($sentence)
    {
        $sentence = trim($sentence);
        
        // Remove common prefixes
        $sentence = preg_replace('/^(The|A|An|This|These|Those|It is|It\'s)\s+/i', '', $sentence);
        
        // Capitalize first letter
        $sentence = ucfirst($sentence);
        
        // If sentence doesn't end with question mark, add one
        if (!str_ends_with($sentence, '?')) {
            $sentence .= '?';
        }
        
        // Add question word if needed
        if (!preg_match('/^(What|Why|How|When|Where|Who|Which|Is|Are|Can|Does|Do)/i', $sentence)) {
            $sentence = 'What is ' . lcfirst($sentence);
        }
        
        return $sentence;
    }

    private function buildFlashcardPrompt($content, $numCards)
    {
        return 'Create ' . $numCards . ' flashcards from the content below.

CRITICAL RULES:
1. Questions must be CLEAR and CONCISE (max 15 words)
2. Answers must be DIRECT from the content
3. Use proper capitalization (LARAVEL, not IARAVEL)
4. No extra text or punctuation

Return ONLY valid JSON:
{"flashcards": [{"question": "Clear question", "answer": "Direct answer"}]}

Content:
"""' . $content . '"""';
    }
    
    private function buildQuizPrompt($content, $numQuestions, $difficulty)
    {
        return 'Create ' . $numQuestions . ' multiple-choice quiz questions from the content below. Difficulty: ' . $difficulty . '

Return ONLY valid JSON:
{
    "questions": [
        {
            "question": "Question text",
            "options": ["Option A", "Option B", "Option C", "Option D"],
            "correct_answer": "Option A",
            "explanation": "Why this is correct"
        }
    ]
}

Content:
"""' . $content . '"""';
    }

    private function callGemini($prompt)
    {
        if (!$this->apiKey) {
            return null;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=' . $this->apiKey;
        
        $data = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            Log::error('CURL Error: ' . $curlError);
            return "Connection Error: " . $curlError;
        }
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
                return $matches[0];
            }
            return $text;
        }
        
        // Log API error
        Log::warning('Gemini API returned HTTP ' . $httpCode, ['response' => substr($response, 0, 500)]);
        
        // Return structured error message
        $errorMessage = $this->getApiErrorMessage($httpCode);
        return $errorMessage;
    }
    
    private function getApiErrorMessage($httpCode)
    {
        switch ($httpCode) {
            case 429:
                return "API_RATE_LIMIT: You've reached the API request limit. Please wait a minute.";
            case 503:
                return "API_UNAVAILABLE: The AI service is temporarily busy. Using fallback mode.";
            case 401:
            case 403:
                return "API_AUTH_ERROR: Invalid API key. Please check your configuration.";
            default:
                return "API_ERROR_{$httpCode}: Service unavailable. Using fallback mode.";
        }
    }
    
    private function isApiErrorResponse($response)
    {
        if (!$response) return true;
        
        $errorPatterns = [
            'API_RATE_LIMIT',
            'API_UNAVAILABLE',
            'API_AUTH_ERROR',
            'API_ERROR_',
            'Connection Error'
        ];
        
        foreach ($errorPatterns as $pattern) {
            if (strpos($response, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    private function isValidJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}