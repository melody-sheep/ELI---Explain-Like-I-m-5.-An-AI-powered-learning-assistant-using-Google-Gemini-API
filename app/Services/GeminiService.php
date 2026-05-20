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
                return "⚠️ Error: GEMINI_API_KEY is missing. Please add it to your .env file.";
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
                return "⚠️ Network Error: " . $curlError;
            }
            
            if ($httpCode == 200) {
                $result = json_decode($response, true);
                return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response text';
            }
            
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? $response;
            return "⚠️ API Error (HTTP $httpCode): " . $errorMsg;
            
        } catch (\Exception $e) {
            return "⚠️ Exception: " . $e->getMessage();
        }
    }
}