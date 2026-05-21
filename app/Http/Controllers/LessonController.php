<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LessonController extends Controller
{
    private function getUserId()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            $user = \App\Models\User::firstOrCreate(
                ['id' => 1],
                ['name' => 'Guest User', 'email' => 'guest@example.com', 'password' => bcrypt('password')]
            );
            Session::put('user_id', $user->id);
            return $user->id;
        }
        return $userId;
    }

    public function index()
    {
        $lessons = Lesson::where('user_id', $this->getUserId())->latest()->get();
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
            'user_id' => $this->getUserId(),
            'title' => $request->title,
            'description' => $request->description,
            'subject' => $request->subject
        ]);

        return redirect()->route('lessons.show', $lesson->id)->with('success', 'Lesson created successfully!');
    }

    public function show($id)
    {
        $lesson = Lesson::where('user_id', $this->getUserId())->with('contents')->findOrFail($id);
        return view('lessons.show', compact('lesson'));
    }

    public function destroy($id)
    {
        $lesson = Lesson::where('user_id', $this->getUserId())->findOrFail($id);
        $lesson->delete();
        return redirect()->route('lessons.index')->with('success', 'Lesson deleted!');
    }

    public function addContent(Request $request, $id)
    {
        try {
            // Validate
            $request->validate([
                'title' => 'required|string',
                'content_type' => 'required|in:text,video,file,link',
                'content' => 'nullable|string',
                'file' => 'nullable|file|max:10240'
            ]);

            $filePath = null;
            $contentValue = $request->content;

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if ($file->isValid()) {
                    $filePath = $file->store('lesson-files', 'public');
                    $contentValue = $filePath;
                }
            }

            // Create content
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
        $bookmarks = Session::get('bookmarks', []);
        $bookmarks[$id] = !($bookmarks[$id] ?? false);
        Session::put('bookmarks', $bookmarks);

        return response()->json(['bookmarked' => $bookmarks[$id]]);
    }

    public function updateProgress(Request $request, $id)
    {
        $progress = Session::get('lesson_progress', []);
        $contentCompleted = Session::get('content_completed', []);

        if ($request->has('content_id')) {
            $contentCompleted[$request->content_id] = (bool) $request->completed;
            Session::put('content_completed', $contentCompleted);

            // Update overall progress count
            $lesson = Lesson::where('user_id', $this->getUserId())->with('contents')->find($id);
            if ($lesson) {
                $completedCount = count(array_filter($contentCompleted, function ($key) use ($lesson) {
                    return $lesson->contents->pluck('id')->contains($key);
                }, ARRAY_FILTER_USE_KEY));

                $progress[$id] = $completedCount;
                Session::put('lesson_progress', $progress);
            }
        }

        return response()->json(['success' => true]);
    }

    public function getNotes($id)
    {
        $notes = Session::get("lesson_notes.{$id}", '');
        return response()->json(['notes' => $notes]);
    }

    public function saveNotes(Request $request, $id)
    {
        Session::put("lesson_notes.{$id}", $request->notes);
        return response()->json(['success' => true]);
    }
}
