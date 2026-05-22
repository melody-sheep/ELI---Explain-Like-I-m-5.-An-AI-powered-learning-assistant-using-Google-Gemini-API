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

    public function generateQuiz($content, $numQuestions = 10, $difficulty = 'medium')
    {
        Log::info('generateQuiz called', [
            'content_length' => strlen($content),
            'num_questions' => $numQuestions,
            'difficulty' => $difficulty
        ]);
        
        // If no API key or mock mode, use fallback
        if (!$this->apiKey || $this->useMock) {
            return $this->createQuizFromContent($content, $numQuestions);
        }
        
        try {
            $prompt = $this->buildQuizPrompt($content, $numQuestions, $difficulty);
            $response = $this->callGemini($prompt);
            
            if ($response && $this->isValidJson($response)) {
                $decoded = json_decode($response, true);
                if (isset($decoded['questions']) && count($decoded['questions']) > 0) {
                    return json_encode(['questions' => $decoded['questions']]);
                }
            }
            
            // If we have a response but no questions, try to extract
            if ($response && strlen($response) > 50) {
                $extracted = $this->extractQuestionsFromText($response);
                if (count($extracted) > 0) {
                    return json_encode(['questions' => $extracted]);
                }
            }
            
            return $this->createQuizFromContent($content, $numQuestions);
            
        } catch (\Exception $e) {
            Log::error('Gemini Quiz API Error: ' . $e->getMessage());
            return $this->createQuizFromContent($content, $numQuestions);
        }
    }
    
    private function extractQuestionsFromText($text)
    {
        $questions = [];
        
        // Try to find JSON in the response
        if (preg_match('/\{[^{}]*"questions"\s*:\s*\[(.*)\]\s*\}/s', $text, $matches)) {
            $jsonStr = $matches[0];
            $decoded = json_decode($jsonStr, true);
            if (isset($decoded['questions'])) {
                return $decoded['questions'];
            }
        }
        
        // Try to find individual Q&A patterns
        $pattern = '/(?:Question|Q\.?\s*(\d+)?[:\s]*)(.+?)(?:Answer|A\.?\s*[:\s]*)(.+?)(?=Question|Q\.|$)/si';
        if (preg_match_all($pattern, $text, $matches)) {
            foreach ($matches[2] as $i => $question) {
                $answer = $matches[3][$i] ?? '';
                $questions[] = [
                    'question' => trim($question),
                    'options' => ['True', 'False'],
                    'correct_answer' => trim($answer),
                    'explanation' => trim($answer)
                ];
            }
        }
        
        return $questions;
    }
    
    private function createQuizFromContent($content, $numQuestions)
    {
        Log::info('Creating quiz from content directly (fallback mode)');
        
        // Split content into sentences
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $questions = [];
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 30 && strlen($sentence) < 300 && count($questions) < $numQuestions) {
                // Generate 4 options based on the sentence
                $correctAnswer = substr($sentence, 0, min(100, strlen($sentence)));
                $options = [
                    $correctAnswer,
                    "This is not mentioned in the text",
                    "A different concept entirely",
                    "Related but incorrect"
                ];
                shuffle($options);
                
                $questions[] = [
                    'question' => $this->sentenceToQuestion($sentence),
                    'options' => $options,
                    'correct_answer' => $correctAnswer,
                    'explanation' => $sentence
                ];
            }
        }
        
        // If still no questions, create from key phrases
        if (empty($questions)) {
            // Extract key phrases (words in title case or quoted)
            preg_match_all('/([A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,3})/', $content, $matches);
            $keyPhrases = array_unique($matches[0]);
            
            foreach ($keyPhrases as $phrase) {
                if (strlen($phrase) > 10 && count($questions) < $numQuestions) {
                    $questions[] = [
                        'question' => "What is " . $phrase . "?",
                        'options' => ["It is a key concept", "Related to the topic", "Discussed in the text", "None of the above"],
                        'correct_answer' => "Discussed in the text",
                        'explanation' => "Review the document for information about " . $phrase
                    ];
                }
            }
        }
        
        // Ultimate fallback
        if (empty($questions)) {
            for ($i = 0; $i < min($numQuestions, 5); $i++) {
                $questions[] = [
                    'question' => 'What is the main topic discussed in this document?',
                    'options' => ['Topic A', 'Topic B', 'Topic C', 'Topic D'],
                    'correct_answer' => 'Topic A',
                    'explanation' => 'Please review the document to identify the main subject.'
                ];
            }
        }
        
        return json_encode(['questions' => array_slice($questions, 0, $numQuestions)]);
    }
    
    private function sentenceToQuestion($sentence)
    {
        $sentence = trim($sentence);
        // Remove common prefixes
        $sentence = preg_replace('/^(The|A|An|This|These|Those|It is|It\'s)\s+/i', '', $sentence);
        $sentence = ucfirst($sentence);
        
        // If it doesn't end with question mark, add one
        if (!str_ends_with($sentence, '?')) {
            // If it's a statement, convert to question
            if (preg_match('/^(.+?) (is|are|was|were|has|have|can|will|would|could|should) /i', $sentence, $matches)) {
                $sentence = ucfirst($matches[2]) . ' ' . $matches[1] . '?';
            } else {
                $sentence = 'What is ' . lcfirst($sentence) . '?';
            }
        }
        
        return $sentence;
    }

    private function buildQuizPrompt($content, $numQuestions, $difficulty)
    {
        return "Create " . $numQuestions . " multiple-choice quiz questions based STRICTLY on the following text. Difficulty: " . $difficulty . "

Instructions:
1. Each question must be based on information in the text
2. Provide 4 options (A, B, C, D) for each question
3. Only ONE option should be correct
4. The correct answer must be directly from the text
5. Return ONLY valid JSON, no other text

Response format:
{
    \"questions\": [
        {
            \"question\": \"Question text here?\",
            \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
            \"correct_answer\": \"Option A\",
            \"explanation\": \"Brief explanation from the text\"
        }
    ]
}

Text:
\"\"\"$content\"\"\"";
    }

    private function callGemini($prompt)
    {
        if (!$this->apiKey) {
            Log::error('No API key provided');
            return null;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey;
        
        // Correct format for Gemini API
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 2048,
                'topP' => 0.95,
                'topK' => 40
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
            return null;
        }
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            Log::info('Gemini API success', ['response_length' => strlen($text)]);
            
            // Try to extract JSON from response
            if (preg_match('/\{[\s\S]*"questions"[\s\S]*\}/', $text, $matches)) {
                return $matches[0];
            }
            return $text;
        }
        
        Log::warning('Gemini API returned HTTP ' . $httpCode, ['response' => substr($response, 0, 500)]);
        return null;
    }

    private function isValidJson($string)
    {
        if (!$string) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}