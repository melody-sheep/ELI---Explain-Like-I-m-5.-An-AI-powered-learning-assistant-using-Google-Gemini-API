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

    private function createFlashcardsFromContent($content, $numCards)
    {
        Log::info('Creating flashcards from content directly');
        
        // Split content into sentences
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $flashcards = [];
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 20 && strlen($sentence) < 300 && count($flashcards) < $numCards) {
                // Create a question from the sentence
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
        
        return json_encode(['flashcards' => $flashcards]);
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
        
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }
        
        if ($httpCode == 200) {
            $result = json_decode($response, true);
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
                return $matches[0];
            }
            return $text;
        }
        
        return null;
    }

    private function isValidJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}