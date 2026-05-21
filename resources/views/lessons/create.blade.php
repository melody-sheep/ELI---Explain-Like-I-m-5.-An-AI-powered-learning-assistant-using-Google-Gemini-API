@extends('layouts.app')

@section('content')
<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border);">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="{{ route('lessons.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary);">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO LESSONS</p>
            </div>
        </a>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 600px; margin: 40px auto; padding: 24px;">
        <h1 style="font-size: 20px; font-weight: 700; margin-bottom: 24px; color: var(--text-primary);">Create New Lesson</h1>

        <form action="{{ route('lessons.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">LESSON TITLE</label>
                <input type="text" name="title" required style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); font-family: monospace; font-size: 13px; padding: 10px; outline: none;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">SUBJECT/CATEGORY</label>
                <input type="text" name="subject" placeholder="e.g., Mathematics, Science, History" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); font-family: monospace; font-size: 13px; padding: 10px; outline: none;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">DESCRIPTION</label>
                <textarea name="description" rows="5" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); font-family: monospace; font-size: 13px; padding: 10px; outline: none; resize: vertical;"></textarea>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; border: 1px solid var(--accent); background-color: var(--accent); color: white; font-family: monospace; font-size: 13px; font-weight: 600; padding: 12px; cursor: pointer;">CREATE LESSON</button>
                <a href="{{ route('lessons.index') }}" style="flex: 1; border: 1px solid var(--border); background-color: transparent; color: var(--text-secondary); text-align: center; padding: 12px; text-decoration: none; font-size: 13px;">CANCEL</a>
            </div>
        </form>
    </div>
</div>
@endsection