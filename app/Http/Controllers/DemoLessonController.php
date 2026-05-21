<?php

namespace App\Http\Controllers;

use App\Services\DemoLessonService;

class DemoLessonController extends Controller
{
    /**
     * Display demo lessons as clickable cards
     */
    public function index()
    {
        $lessons = DemoLessonService::getAllLessons();
        return view('lessons.demo-index', compact('lessons'));
    }

    /**
     * Show a specific demo lesson with its contents
     */
    public function show($id)
    {
        $lesson = DemoLessonService::getLesson($id);
        
        if (!$lesson) {
            abort(404, 'Lesson not found');
        }
        
        return view('lessons.demo-show', compact('lesson'));
    }
}