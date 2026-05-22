@extends('layouts.app')

@section('content')
<style>
    * {
        font-family: 'Consolas', monospace;
    }
    
    .btn-primary {
        background: rgba(168, 85, 247, 0.1) !important;
        border: 1.5px solid #a855f7 !important;
        color: #a855f7 !important;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: rgba(168, 85, 247, 0.2) !important;
        transform: scale(1.02);
    }
    .btn-primary:active {
        transform: scale(0.98);
    }
    
    .theme-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .theme-btn:hover {
        transform: rotate(15deg);
        border-color: var(--accent-purple) !important;
    }
    
    .form-input {
        background-color: var(--bg-tertiary);
        border: 1.5px solid var(--border);
        color: var(--text-primary);
        padding: 12px;
        border-radius: 10px;
        outline: none;
        transition: all 0.2s ease;
        font-size: 12px;
        width: 100%;
    }
    .form-input:focus {
        border-color: #a855f7;
    }
    
    .time-picker-group {
        display: flex;
        gap: 12px;
        background-color: var(--bg-tertiary);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 12px;
    }
    .time-input-wrapper {
        flex: 1;
        text-align: center;
    }
    .time-input {
        width: 100%;
        background: rgba(168, 85, 247, 0.05);
        border: 1px solid var(--border);
        color: var(--text-primary);
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        padding: 8px;
        border-radius: 8px;
        outline: none;
    }
    .time-input:focus {
        border-color: #a855f7;
    }
    .time-label {
        font-size: 9px;
        color: var(--text-muted);
        display: block;
        margin-top: 6px;
    }
    .total-seconds {
        flex: 0.5;
        text-align: center;
        background: rgba(168, 85, 247, 0.1);
        border-radius: 8px;
        padding: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .total-seconds-value {
        font-size: 20px;
        font-weight: bold;
        color: #a855f7;
    }
    
    .number-input {
        display: flex;
        align-items: center;
        gap: 12px;
        background-color: var(--bg-tertiary);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 8px 12px;
    }
    .number-input button {
        background: rgba(168, 85, 247, 0.1);
        border: 1.5px solid #a855f7;
        color: #a855f7;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .number-input button:hover {
        background: rgba(168, 85, 247, 0.2);
        transform: scale(1.05);
    }
    .number-input input {
        width: 80px;
        text-align: center;
        background: none;
        border: none;
        color: var(--text-primary);
        font-size: 18px;
        font-weight: bold;
        outline: none;
    }
    
    .form-select {
        background-color: var(--bg-tertiary);
        border: 1.5px solid var(--border);
        color: var(--text-primary);
        padding: 12px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 12px;
        width: 100%;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    input[type="number"]::-webkit-inner-spin-button, 
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border);">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('quizzes.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
                <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
                </div>
                <div>
                    <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                    <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO QUIZZES</p>
                </div>
            </a>
            <button onclick="toggleTheme()" class="theme-btn" style="width: 36px; height: 36px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); cursor: pointer; border-radius: 8px;">
                <span id="theme-icon" class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 20px;">dark_mode</span>
            </button>
        </div>
    </div>

    <div style="padding: 20px;">
        <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 16px; letter-spacing: 0.5px;">HOW IT WORKS</p>
        <ul style="font-size: 11px; color: var(--text-secondary); line-height: 2; list-style: none; padding-left: 0;">
            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span class="material-symbols-outlined" style="color: #a855f7; font-size: 16px;">upload_file</span>
                Upload a document
            </li>
            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span class="material-symbols-outlined" style="color: #a855f7; font-size: 16px;">auto_awesome</span>
                AI analyzes content
            </li>
            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span class="material-symbols-outlined" style="color: #a855f7; font-size: 16px;">quiz</span>
                Quiz is generated
            </li>
            <li style="display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined" style="color: #a855f7; font-size: 16px;">school</span>
                Test your knowledge
            </li>
        </ul>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 600px; margin: 40px auto; padding: 24px; width: 100%;">
        <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; color: var(--text-primary);">
            <span class="material-symbols-outlined" style="color: #a855f7; font-size: 32px;">auto_awesome</span>
            Generate AI Quiz
        </h1>
        
        <div style="background: rgba(168, 85, 247, 0.1); border: 1.5px solid #a855f7; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
            <p style="font-size: 12px; color: var(--text-secondary); display: flex; align-items: center; gap: 8px; margin: 0;">
                <span class="material-symbols-outlined" style="color: #a855f7; font-size: 18px;">info</span>
                Upload a document (PDF, DOC, DOCX, TXT) and AI will create a multiple-choice quiz based on the content.
            </p>
        </div>

        <form id="quiz-generate-form" action="{{ route('quizzes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); letter-spacing: 0.5px;">QUIZ TITLE</label>
                <input type="text" name="title" required class="form-input" placeholder="e.g., Introduction to AI">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); letter-spacing: 0.5px;">DESCRIPTION (OPTIONAL)</label>
                <textarea name="description" rows="3" class="form-input" style="resize: vertical;" placeholder="Brief description of this quiz..."></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); letter-spacing: 0.5px;">UPLOAD DOCUMENT</label>
                <div style="border: 1.5px dashed var(--border); border-radius: 10px; padding: 20px; text-align: center; background-color: var(--bg-tertiary);">
                    <input type="file" name="file" id="file-input" accept=".pdf,.doc,.docx,.txt" required style="display: none;" onchange="updateFileName(this)">
                    <button type="button" onclick="document.getElementById('file-input').click()" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; font-size: 12px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">upload</span>
                        Choose File
                    </button>
                    <span id="file-name" style="margin-left: 12px; font-size: 11px; color: var(--text-muted);">No file chosen</span>
                </div>
                <p style="font-size: 9px; color: var(--text-muted); margin-top: 8px;">Supports PDF, DOC, DOCX, TXT (Max 10MB)</p>
            </div>

            <!-- NUMBER OF QUESTIONS -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); letter-spacing: 0.5px;">NUMBER OF QUESTIONS</label>
                <div class="number-input">
                    <button type="button" onclick="changeQuestions(-1)">−</button>
                    <input type="number" name="num_questions" id="num-questions" value="10" min="1" max="50" step="1">
                    <button type="button" onclick="changeQuestions(1)">+</button>
                </div>
            </div>

            <!-- TIME LIMIT -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); letter-spacing: 0.5px;">TIME LIMIT (PER QUESTION)</label>
                <div class="time-picker-group">
                    <div class="time-input-wrapper">
                        <input type="number" id="time-minutes" class="time-input" value="0" min="0" max="60" step="1" onchange="updateTotalSeconds()">
                        <span class="time-label">MINUTES</span>
                    </div>
                    <div class="time-input-wrapper">
                        <input type="number" id="time-seconds" class="time-input" value="30" min="0" max="59" step="1" onchange="updateTotalSeconds()">
                        <span class="time-label">SECONDS</span>
                    </div>
                    <div class="total-seconds">
                        <span class="total-seconds-value" id="total-seconds-display">30</span>
                        <span class="time-label">TOTAL SEC</span>
                    </div>
                </div>
                <input type="hidden" name="time_limit" id="time-limit-total" value="30">
            </div>

            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 8px; color: var(--text-muted); letter-spacing: 0.5px;">ASSIGN TO LESSON (OPTIONAL)</label>
                <select name="lesson_id" class="form-select">
                    <option value="">No lesson</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" id="generate-btn" class="btn-primary" style="width: 100%; padding: 14px; border-radius: 12px; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">auto_awesome</span>
                GENERATE QUIZ
            </button>
        </form>

        <div id="loading-indicator" style="display: none; margin-top: 20px; text-align: center; padding: 20px; background: rgba(168, 85, 247, 0.1); border: 1.5px solid #a855f7; border-radius: 12px;">
            <div style="display: inline-block; width: 32px; height: 32px; border: 3px solid var(--border); border-top-color: #a855f7; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 12px; color: var(--text-secondary); font-size: 12px;">Generating your quiz...</p>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const fileName = input.files[0]?.name || 'No file chosen';
        document.getElementById('file-name').textContent = fileName;
    }
    
    function changeQuestions(delta) {
        const input = document.getElementById('num-questions');
        let value = parseInt(input.value) + delta;
        value = Math.min(50, Math.max(1, value));
        input.value = value;
    }
    
    function updateTotalSeconds() {
        const minutes = parseInt(document.getElementById('time-minutes').value) || 0;
        const seconds = parseInt(document.getElementById('time-seconds').value) || 0;
        const totalSeconds = (minutes * 60) + seconds;
        document.getElementById('total-seconds-display').textContent = totalSeconds;
        document.getElementById('time-limit-total').value = totalSeconds;
    }
    
    document.getElementById('time-minutes').addEventListener('change', updateTotalSeconds);
    document.getElementById('time-seconds').addEventListener('change', updateTotalSeconds);
    updateTotalSeconds();
    
    document.getElementById('quiz-generate-form').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('file-input');
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select a file to upload.');
            return;
        }
        
        document.getElementById('loading-indicator').style.display = 'block';
        document.getElementById('generate-btn').disabled = true;
        document.getElementById('generate-btn').style.opacity = '0.6';
    });
</script>
@endsection