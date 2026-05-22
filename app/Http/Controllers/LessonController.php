<?php
// app/Http/Controllers/LessonController.php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LessonController extends Controller
{
    private function getCurrentUserId()
    {
        // Priority 1: Logged in user
        if (Auth::check()) {
            return Auth::id();
        }
        
        // Priority 2: Guest session
        if (Session::has('guest_id')) {
            return Session::get('guest_id');
        }
        
        // Priority 3: Create new guest (should not happen due to middleware)
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
        $lessons = Lesson::where('user_id', $userId)->latest()->get();
        return view('lessons.index', compact('lessons'));
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

        return redirect()->route('lessons.show', $lesson->id)->with('success', 'Lesson created successfully!');
    }

    public function show($id)
    {
        $userId = $this->getCurrentUserId();
        $lesson = Lesson::where('user_id', $userId)->with('contents')->findOrFail($id);
        return view('lessons.show', compact('lesson'));
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
        
        return response()->json(['success' => true, 'lesson' => $lesson]);
    }

    public function destroy($id)
    {
        $userId = $this->getCurrentUserId();
        $lesson = Lesson::where('user_id', $userId)->findOrFail($id);
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

            return back()->with('success', 'Content added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleBookmark($id)
    {
        $userId = $this->getCurrentUserId();
        $bookmarkKey = "bookmarks_{$userId}";
        $bookmarks = Session::get($bookmarkKey, []);
        $bookmarks[$id] = !($bookmarks[$id] ?? false);
        Session::put($bookmarkKey, $bookmarks);
        return response()->json(['bookmarked' => $bookmarks[$id]]);
    }

    public function updateProgress(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        $progressKey = "lesson_progress_{$userId}";
        $contentCompletedKey = "content_completed_{$userId}";
        
        $progress = Session::get($progressKey, []);
        $contentCompleted = Session::get($contentCompletedKey, []);

        if ($request->has('content_id')) {
            $contentCompleted[$request->content_id] = (bool) $request->completed;
            Session::put($contentCompletedKey, $contentCompleted);

            $lesson = Lesson::where('user_id', $userId)->with('contents')->find($id);
            if ($lesson) {
                $completedCount = count(array_filter($contentCompleted, function ($key) use ($lesson) {
                    return $lesson->contents->pluck('id')->contains($key);
                }, ARRAY_FILTER_USE_KEY));
                $progress[$id] = $completedCount;
                Session::put($progressKey, $progress);
            }
        }
        return response()->json(['success' => true]);
    }

    public function getNotes($id)
    {
        $userId = $this->getCurrentUserId();
        $notes = Session::get("lesson_notes_{$userId}_{$id}", '');
        return response()->json(['notes' => $notes]);
    }

    public function saveNotes(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        Session::put("lesson_notes_{$userId}_{$id}", $request->notes);
        return response()->json(['success' => true]);
    }
    
    public function markComplete(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        $completedKey = "completed_lessons_{$userId}";
        $completed = Session::get($completedKey, []);
        $completed[$id] = $request->completed ?? true;
        Session::put($completedKey, $completed);
        return response()->json(['success' => true]);
    }
}