<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\LessonUserProgress;
use App\Models\LessonNote;
use App\Models\LessonBookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        
        if (Session::has('guest_id')) {
            return Session::get('guest_id');
        }
        
        $guestId = Session::getId();
        $user = \App\Models\User::create([
            'name' => 'Guest_' . substr($guestId, 0, 8),
            'email' => 'guest_' . $guestId . '@temp.local',
            'password' => bcrypt(\Illuminate\Support\Str::random(40)),
            'is_guest' => true
        ]);
        Session::put('guest_id', $user->id);
        return $user->id;
    }

    public function index()
    {
        $userId = $this->getCurrentUserId();
        $lessons = Lesson::where('user_id', $userId)
            ->with('contents')
            ->latest()
            ->get();
        
        foreach ($lessons as $lesson) {
            $lesson->progress_percent = $lesson->getProgressPercent($userId);
            $lesson->is_bookmarked = $lesson->isBookmarkedByUser($userId);
            $lesson->completed_count = $lesson->getCompletedCount($userId);
            $lesson->total_contents = $lesson->contents->count();
        }
        
        $totalLessons = $lessons->count();
        $completedLessons = $lessons->filter(function($l) { return $l->progress_percent === 100; })->count();
        $inProgressLessons = $lessons->filter(function($l) { return $l->progress_percent > 0 && $l->progress_percent < 100; })->count();
        $bookmarkedCount = LessonBookmark::where('user_id', $userId)->count();
        $overallProgress = $totalLessons > 0 ? round($lessons->sum('progress_percent') / $totalLessons) : 0;
        
        return view('lessons.index', compact('lessons', 'totalLessons', 'completedLessons', 'inProgressLessons', 'bookmarkedCount', 'overallProgress'));
    }

    public function create()
    {
        return view('lessons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'nullable|string|max:100'
        ]);

        $lesson = Lesson::create([
            'user_id' => $this->getCurrentUserId(),
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject
        ]);

        return redirect()->route('lessons.show', $lesson->id)->with('success', 'Lesson created! Add your first content section below.');
    }

    public function show($id)
    {
        $userId = $this->getCurrentUserId();
        $lesson = Lesson::where('user_id', $userId)->with('contents')->findOrFail($id);
        
        // Get completed status for each content
        $completedMap = LessonUserProgress::where('user_id', $userId)
            ->where('lesson_id', $id)
            ->where('is_completed', true)
            ->pluck('content_id')
            ->flip();
        
        foreach ($lesson->contents as $content) {
            $content->is_completed = isset($completedMap[$content->id]);
        }
        
        $totalContents = $lesson->contents->count();
        $completedContents = count($completedMap);
        $progressPercent = $totalContents > 0 ? round(($completedContents / $totalContents) * 100) : 0;
        $isBookmarked = $lesson->isBookmarkedByUser($userId);
        $userNote = $lesson->getUserNote($userId);
        $noteContent = $userNote ? $userNote->content : '';
        
        return view('lessons.show', compact('lesson', 'progressPercent', 'completedContents', 'totalContents', 'isBookmarked', 'noteContent'));
    }

    public function update(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        $lesson = Lesson::where('user_id', $userId)->findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'nullable|string|max:100'
        ]);
        
        $lesson->update($request->only(['title', 'description', 'subject']));
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'lesson' => $lesson]);
        }
        
        return back()->with('success', 'Lesson updated!');
    }

    public function destroy($id)
    {
        $userId = $this->getCurrentUserId();
        $lesson = Lesson::where('user_id', $userId)->findOrFail($id);
        
        // Delete related records
        LessonUserProgress::where('lesson_id', $id)->delete();
        LessonNote::where('lesson_id', $id)->delete();
        LessonBookmark::where('lesson_id', $id)->delete();
        $lesson->delete();
        
        return redirect()->route('lessons.index')->with('success', 'Lesson deleted!');
    }

    public function addContent(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'content_type' => 'required|in:text,video,file,link',
                'content' => 'nullable|string',
                'file' => 'nullable|file|max:10240'
            ]);

            $filePath = null;
            $contentValue = $request->content;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if ($file->isValid()) {
                    $filePath = $file->store('lesson-files', 'public');
                    $contentValue = $filePath;
                }
            }

            LessonContent::create([
                'lesson_id' => $id,
                'title' => $request->title,
                'content_type' => $request->content_type,
                'content' => $contentValue,
                'file_path' => $filePath,
                'order_index' => $request->order_index ?? 0
            ]);

            return back()->with('success', 'Content added!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleContentComplete(Request $request, $lessonId, $contentId)
    {
        $userId = $this->getCurrentUserId();
        
        $progress = LessonUserProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'content_id' => $contentId
            ],
            [
                'is_completed' => $request->completed,
                'completed_at' => $request->completed ? now() : null
            ]
        );
        
        $lesson = Lesson::find($lessonId);
        $totalContents = $lesson->contents->count();
        $completedContents = LessonUserProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->where('is_completed', true)
            ->count();
        
        $progressPercent = $totalContents > 0 ? round(($completedContents / $totalContents) * 100) : 0;
        
        return response()->json([
            'success' => true,
            'progress_percent' => $progressPercent,
            'completed_count' => $completedContents,
            'total_count' => $totalContents,
            'is_complete' => $completedContents === $totalContents && $totalContents > 0
        ]);
    }

    public function toggleBookmark($id)
    {
        $userId = $this->getCurrentUserId();
        
        $bookmark = LessonBookmark::where('user_id', $userId)
            ->where('lesson_id', $id)
            ->first();
        
        if ($bookmark) {
            $bookmark->delete();
            $isBookmarked = false;
        } else {
            LessonBookmark::create([
                'user_id' => $userId,
                'lesson_id' => $id
            ]);
            $isBookmarked = true;
        }
        
        return response()->json(['bookmarked' => $isBookmarked]);
    }

    public function getNotes($id)
    {
        $userId = $this->getCurrentUserId();
        $note = LessonNote::where('user_id', $userId)
            ->where('lesson_id', $id)
            ->first();
        
        return response()->json(['notes' => $note->content ?? '']);
    }

    public function saveNotes(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        
        LessonNote::updateOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $id
            ],
            [
                'content' => $request->notes
            ]
        );
        
        return response()->json(['success' => true]);
    }

    public function saveFileNote(Request $request, $lessonId, $contentId)
    {
        $userId = $this->getCurrentUserId();
        $key = "file_note_{$userId}_{$contentId}";
        Session::put($key, $request->note);

        return response()->json(['success' => true]);
    }
    
    public function deleteContent($lessonId, $contentId)
    {
        $userId = $this->getCurrentUserId();
        $lesson = Lesson::where('user_id', $userId)->findOrFail($lessonId);
        $content = LessonContent::where('lesson_id', $lessonId)->findOrFail($contentId);
        
        LessonUserProgress::where('content_id', $contentId)->delete();
        $content->delete();
        
        // Check if it's an AJAX/fetch request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Content deleted successfully',
                'redirect' => route('lessons.show', $lessonId)
            ]);
        }
        
        return redirect()->route('lessons.show', $lessonId)->with('success', 'Content deleted!');
    }
}