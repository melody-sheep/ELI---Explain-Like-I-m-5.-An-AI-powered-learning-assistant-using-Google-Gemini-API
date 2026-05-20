<?php

namespace App\Services;

class GeminiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function ask($prompt)
    {
        try {
            if (!$this->apiKey) {
                return "Error: GEMINI_API_KEY is missing. Please add it to your .env file.";
            }

            // Add instruction to AI to return plain text without markdown
            $cleanPrompt = $prompt . "\n\nIMPORTANT: Return your response as plain text only. Do NOT use any markdown formatting like asterisks, backticks, dashes, or special characters. Use simple paragraphs and line breaks only.";

            $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $this->apiKey;
            
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $cleanPrompt]
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
                return "Network Error: " . $curlError;
            }
            
            if ($httpCode == 200) {
                $result = json_decode($response, true);
                $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response text';
                
                // Clean the text - remove all markdown special characters
                $text = $this->cleanMarkdown($text);
                
                return $text;
            }
            
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? $response;
            return "API Error (HTTP $httpCode): " . $errorMsg;
            
        } catch (\Exception $e) {
            return "Exception: " . $e->getMessage();
        }
    }

    private function cleanMarkdown($text)
    {
        // Remove code blocks ``` ... ```
        $text = preg_replace('/```[\s\S]*?```/', '', $text);
        
        // Remove inline code `...`
        $text = preg_replace('/`([^`]+)`/', '$1', $text);
        
        // Remove bold **text** or __text__
        $text = preg_replace('/\*\*([^*]+)\*\*/', '$1', $text);
        $text = preg_replace('/__([^_]+)__/', '$1', $text);
        
        // Remove italic *text* or _text_ (but not at start of line with spaces)
        $text = preg_replace('/(?<!\s)\*([^*]+)\*(?!\s)/', '$1', $text);
        $text = preg_replace('/(?<!\s)_([^_]+)_(?!\s)/', '$1', $text);
        
        // Remove headers #, ##, etc. at start of line
        $text = preg_replace('/^#{1,6}\s+/m', '', $text);
        
        // Remove horizontal lines ---, ***, ___
        $text = preg_replace('/^[\s]*[-*_]{3,}\s*$/m', '', $text);
        
        // Replace bullet points with simple dashes
        $text = preg_replace('/^[\s]*[-*+]\s+/m', '- ', $text);
        
        // Remove extra spaces
        $text = preg_replace('/[ \t]+/', ' ', $text);
        
        // Clean up multiple newlines to double newline (paragraph spacing)
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Trim whitespace
        $text = trim($text);
        
        return $text;
    }
}