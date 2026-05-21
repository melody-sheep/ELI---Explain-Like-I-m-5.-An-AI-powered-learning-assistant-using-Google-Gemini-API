@extends('layouts.app')

@section('content')
<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border);">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="{{ route('quizzes.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary);">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO QUIZZES</p>
            </div>
        </a>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 500px; margin: 40px auto; padding: 24px;">
        <h1 style="font-size: 20px; font-weight: 700; margin-bottom: 24px; color: var(--text-primary);">Generate AI Quiz</h1>
        <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 24px;">Upload a document and AI will create a multiple-choice quiz based on the content.</p>

        <form action="{{ route('quizzes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px;">QUIZ TITLE</label>
                <input type="text" name="title" required style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px;">DESCRIPTION (OPTIONAL)</label>
                <textarea name="description" rows="3" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px;">UPLOAD DOCUMENT</label>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.txt" required style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px;">NUMBER OF QUESTIONS</label>
                <input type="number" name="num_questions" value="10" min="1" max="20" style="width: 100px; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px;">ASSIGN TO LESSON (OPTIONAL)</label>
                <select name="lesson_id" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px;">
                    <option value="">No lesson</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" style="width: 100%; border: 1px solid var(--accent-purple); background-color: var(--accent-purple); color: white; padding: 14px; cursor: pointer; font-size: 13px; font-weight: 600;">
                GENERATE QUIZ
            </button>
        </form>
    </div>
</div>
@endsection