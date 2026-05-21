<?php

namespace App\Services;

class GeminiLMSService
{
    public function generateFlashcards($content)
    {
        // For now, return sample flashcards
        return json_encode([
            ['question' => 'What is AI?', 'answer' => 'Artificial Intelligence'],
            ['question' => 'What is Machine Learning?', 'answer' => 'A subset of AI'],
        ]);
    }

    public function generateQuiz($content, $numQuestions = 10)
    {
        // For now, return sample quiz
        return json_encode([
            ['question' => 'Sample Question 1', 'options' => ['A', 'B', 'C', 'D'], 'correct_answer' => 'A'],
            ['question' => 'Sample Question 2', 'options' => ['X', 'Y', 'Z', 'W'], 'correct_answer' => 'X'],
        ]);
    }
}