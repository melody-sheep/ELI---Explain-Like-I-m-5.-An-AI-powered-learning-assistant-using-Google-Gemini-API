@extends('layouts.app')

@section('content')
<style>
    .create-container {
        background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
    }
    .form-input {
        transition: all 0.2s ease;
    }
    .form-input:focus {
        outline: none;
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }
    .create-btn {
        background: #22c55e !important;
        color: #000000 !important;
        font-weight: 700;
        transition: all 0.2s ease;
    }
    .create-btn:hover {
        background: #16a34a !important;
        transform: scale(1.02);
    }
    .cancel-btn {
        border: 1px solid var(--border);
        background-color: transparent;
        color: var(--text-secondary);
        transition: all 0.2s ease;
    }
    .cancel-btn:hover {
        background-color: var(--hover-bg);
    }
</style>

<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border);">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="{{ route('lessons.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted); font-family: 'Consolas', monospace;">TO LESSONS</p>
            </div>
        </a>
    </div>
</div>

<div class="create-container" style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 600px; margin: 40px auto; padding: 24px; width: 100%;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 32px;">
            <span class="material-symbols-outlined" style="color: #22c55e; font-size: 36px;">add_circle</span>
            <div>
                <h1 style="font-size: 24px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">Create New Lesson</h1>
                <p style="font-size: 11px; color: var(--text-muted); font-family: 'Consolas', monospace;">Fill in the details to create your lesson</p>
            </div>
        </div>

        <form action="{{ route('lessons.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Consolas', monospace; letter-spacing: 0.5px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">title</span>
                    LESSON TITLE
                </label>
                <input type="text" name="title" required placeholder="e.g., Introduction to AI" value="{{ old('title') }}" class="form-input" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); font-family: 'Consolas', monospace; font-size: 13px; padding: 12px; border-radius: 10px;">
                @error('title')
                    <p style="color: var(--accent-red); font-size: 10px; margin-top: 6px; font-family: 'Consolas', monospace;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Consolas', monospace; letter-spacing: 0.5px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">category</span>
                    SUBJECT/CATEGORY
                </label>
                <input type="text" name="subject" placeholder="e.g., Mathematics, Science, History" value="{{ old('subject') }}" class="form-input" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); font-family: 'Consolas', monospace; font-size: 13px; padding: 12px; border-radius: 10px;">
                @error('subject')
                    <p style="color: var(--accent-red); font-size: 10px; margin-top: 6px; font-family: 'Consolas', monospace;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 32px;">
                <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary); font-family: 'Consolas', monospace; letter-spacing: 0.5px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">description</span>
                    DESCRIPTION
                </label>
                <textarea name="description" rows="5" placeholder="What will students learn in this lesson?..." class="form-input" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); font-family: 'Consolas', monospace; font-size: 13px; padding: 12px; border-radius: 10px; resize: vertical;">{{ old('description') }}</textarea>
                @error('description')
                    <p style="color: var(--accent-red); font-size: 10px; margin-top: 6px; font-family: 'Consolas', monospace;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 16px;">
                <button type="submit" class="create-btn" style="flex: 1; border: none; padding: 14px; border-radius: 10px; font-family: 'Consolas', monospace; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">check_circle</span>
                    CREATE LESSON
                </button>
                <a href="{{ route('lessons.index') }}" class="cancel-btn" style="flex: 1; text-align: center; padding: 14px; border-radius: 10px; text-decoration: none; font-family: 'Consolas', monospace; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection