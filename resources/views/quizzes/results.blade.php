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
    
    .btn-secondary {
        background: rgba(100, 100, 100, 0.1) !important;
        border: 1.5px solid var(--text-muted) !important;
        color: var(--text-muted) !important;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background: rgba(100, 100, 100, 0.2) !important;
    }
    
    .theme-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .theme-btn:hover {
        transform: rotate(15deg);
        border-color: var(--accent-purple) !important;
    }
    
    .score-circle {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
        animation: pulse 0.5s ease;
    }
    @keyframes pulse {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .score-inner {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: var(--bg-primary);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .score-percent {
        font-size: 32px;
        font-weight: bold;
    }
    .score-fraction {
        font-size: 12px;
    }
    
    .stat-card {
        background: var(--bg-tertiary);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        border-color: #a855f7;
        transform: translateY(-2px);
    }
    .stat-number {
        font-size: 28px;
        font-weight: bold;
    }
    .stat-label {
        font-size: 9px;
        color: var(--text-muted);
        letter-spacing: 0.5px;
    }
    
    .breakdown-item {
        padding: 16px;
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid var(--border);
        background-color: var(--bg-secondary);
    }
    .breakdown-item.correct {
        border-left: 3px solid #22c55e;
    }
    .breakdown-item.wrong {
        border-left: 3px solid #ef4444;
    }
    .breakdown-item.unanswered {
        border-left: 3px solid #f59e0b;
    }
    .breakdown-item:hover {
        transform: translateX(4px);
        border-color: #a855f7;
    }
    
    .time-bar {
        height: 6px;
        background: var(--border);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 8px;
    }
    .time-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #a855f7, #c084fc);
        border-radius: 3px;
        transition: width 0.5s ease;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .result-card {
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .score-icon {
        font-size: 56px;
        animation: bounce 0.5s ease;
    }
</style>

@php
    $totalQuestions = $quiz->questions->count();
    $percentage = round(($score / max($totalQuestions, 1)) * 100);
    
    // Determine score color and icon
    if ($percentage >= 90) {
        $scoreColor = '#22c55e';
        $scoreBg = 'rgba(34, 197, 94, 0.15)';
        $scoreBorder = '2px solid #22c55e';
        $scoreIcon = 'sentiment_very_satisfied';
        $scoreMessage = 'Excellent! Outstanding performance!';
        $scoreConicStart = '#22c55e';
        $scoreConicEnd = '#4ade80';
    } elseif ($percentage >= 80) {
        $scoreColor = '#3b82f6';
        $scoreBg = 'rgba(59, 130, 246, 0.15)';
        $scoreBorder = '2px solid #3b82f6';
        $scoreIcon = 'sentiment_satisfied';
        $scoreMessage = 'Great job! Keep it up!';
        $scoreConicStart = '#3b82f6';
        $scoreConicEnd = '#60a5fa';
    } elseif ($percentage >= 70) {
        $scoreColor = '#f59e0b';
        $scoreBg = 'rgba(245, 158, 11, 0.15)';
        $scoreBorder = '2px solid #f59e0b';
        $scoreIcon = 'sentiment_neutral';
        $scoreMessage = 'Good effort! Room for improvement.';
        $scoreConicStart = '#f59e0b';
        $scoreConicEnd = '#fbbf24';
    } else {
        $scoreColor = '#ef4444';
        $scoreBg = 'rgba(239, 68, 68, 0.15)';
        $scoreBorder = '2px solid #ef4444';
        $scoreIcon = 'sentiment_dissatisfied';
        $scoreMessage = 'Keep practicing! You can do better!';
        $scoreConicStart = '#ef4444';
        $scoreConicEnd = '#f87171';
    }
@endphp

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
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
        <div style="background: var(--bg-tertiary); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span class="material-symbols-outlined" style="color: #a855f7; font-size: 28px;">quiz</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted);">Quiz Title</p>
                    <p style="font-size: 13px; font-weight: 700; color: var(--text-primary);">{{ $quiz->title }}</p>
                </div>
            </div>
        </div>
        
        <!-- FIXED PERFORMANCE SECTION -->
        <div style="margin-top: 20px;">
            <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px; letter-spacing: 0.5px;">PERFORMANCE</p>
            <div style="background: var(--bg-tertiary); border: 1px solid var(--border); border-radius: 12px; padding: 20px;">
                <div style="text-align: center;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 70px; height: 70px; border-radius: 50%; background: rgba(168, 85, 247, 0.1); margin-bottom: 12px;">
                        <span class="material-symbols-outlined" style="font-size: 32px; color: #a855f7;">insights</span>
                    </div>
                    <p style="font-size: 24px; font-weight: bold; color: #a855f7;">{{ $percentage }}%</p>
                    <p style="font-size: 10px; color: var(--text-muted);">OVERALL SCORE</p>
                </div>
                <div style="margin-top: 16px;">
                    <div style="height: 4px; background: var(--border); border-radius: 4px; overflow: hidden;">
                        <div style="width: {{ $percentage }}%; height: 100%; background: linear-gradient(90deg, #a855f7, #c084fc); border-radius: 4px;"></div>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 12px;">
                    <div style="text-align: center;">
                        <p style="font-size: 18px; font-weight: bold; color: #22c55e;">{{ $score }}</p>
                        <p style="font-size: 8px; color: var(--text-muted);">CORRECT</p>
                    </div>
                    <div style="text-align: center;">
                        <p style="font-size: 18px; font-weight: bold; color: #ef4444;">{{ $totalQuestions - $score }}</p>
                        <p style="font-size: 8px; color: var(--text-muted);">WRONG</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 800px; margin: 0 auto; padding: 32px; width: 100%;">
        <!-- Results Header -->
        <div style="text-align: center; margin-bottom: 32px;" class="result-card">
            <div class="score-icon" style="color: {{ $scoreColor }};">
                <span class="material-symbols-outlined" style="font-size: 64px;">{{ $scoreIcon }}</span>
            </div>
            <h1 style="font-size: 24px; font-weight: 700; margin: 16px 0 8px; color: var(--text-primary);">Quiz Complete!</h1>
            <p style="font-size: 13px; color: {{ $scoreColor }}; font-weight: 500;">{{ $scoreMessage }}</p>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Here's how you did on "{{ $quiz->title }}"</p>
        </div>

        <!-- Score Circle -->
        <div style="display: flex; justify-content: center; margin-bottom: 32px;">
            <div class="score-circle" style="background: conic-gradient({{ $scoreConicStart }} 0deg {{ $percentage * 3.6 }}deg, {{ $scoreBg }} {{ $percentage * 3.6 }}deg 360deg); border: {{ $scoreBorder }};">
                <div class="score-inner" style="background: var(--bg-primary);">
                    <div class="score-percent" style="color: {{ $scoreColor }};">{{ $percentage }}%</div>
                    <div class="score-fraction" style="color: var(--text-muted);">{{ $score }}/{{ $totalQuestions }}</div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px;">
            <div class="stat-card">
                <div class="stat-number" style="color: #22c55e;">{{ $score }}</div>
                <div class="stat-label">CORRECT</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #ef4444;">{{ $totalQuestions - $score }}</div>
                <div class="stat-label">WRONG</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="time-spent-stat" style="color: #a855f7;">--:--</div>
                <div class="stat-label">TIME SPENT</div>
            </div>
        </div>

        <!-- Time Limit Display -->
        @php
            $timeLimit = $quiz->time_limit_per_question ?? 30;
            $totalTimeLimit = $timeLimit * $totalQuestions;
            $minutesLimit = floor($totalTimeLimit / 60);
            $secondsLimit = $totalTimeLimit % 60;
        @endphp
        <div style="background: var(--bg-tertiary); border: 1px solid var(--border); border-radius: 12px; padding: 16px; margin-bottom: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 11px; color: var(--text-muted);">TIME LIMIT</span>
                <span style="font-size: 13px; font-weight: 600; color: #a855f7;">{{ $minutesLimit }}m {{ $secondsLimit }}s</span>
            </div>
            <div class="time-bar">
                <div class="time-bar-fill" id="time-limit-fill" style="width: 0%;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                <span style="font-size: 10px; color: var(--text-muted);">Time used: <span id="time-used-display">0m 0s</span></span>
                <span style="font-size: 10px; color: var(--text-muted);">of {{ $minutesLimit }}m {{ $secondsLimit }}s</span>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div style="margin-bottom: 32px;">
            <h2 style="font-size: 14px; font-weight: 700; margin-bottom: 16px; color: var(--text-primary); letter-spacing: 0.5px;">DETAILED BREAKDOWN</h2>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($results as $index => $result)
                <div class="breakdown-item {{ $result['is_correct'] ? 'correct' : ($result['user_answer'] !== 'Not answered' ? 'wrong' : 'unanswered') }}">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <span class="material-symbols-outlined" style="color: {{ $result['is_correct'] ? '#22c55e' : ($result['user_answer'] !== 'Not answered' ? '#ef4444' : '#f59e0b') }}; font-size: 18px;">
                                    {{ $result['is_correct'] ? 'check_circle' : ($result['user_answer'] !== 'Not answered' ? 'cancel' : 'hourglass_empty') }}
                                </span>
                                <span style="font-size: 11px; font-weight: 600; color: var(--text-muted);">Question {{ $index + 1 }}</span>
                            </div>
                            <p style="font-size: 13px; font-weight: 500; color: var(--text-primary); margin-bottom: 8px; line-height: 1.4;">
                                {{ Str::limit($result['question']->question, 150) }}
                            </p>
                            <div style="background: var(--bg-tertiary); padding: 10px; border-radius: 8px; margin-top: 8px;">
                                <p style="font-size: 11px; color: var(--text-secondary);">
                                    <span style="color: var(--text-muted);">Your answer:</span>
                                    <span style="color: {{ $result['is_correct'] ? '#22c55e' : '#ef4444' }}; font-weight: 500;">
                                        {{ $result['user_answer'] }}
                                    </span>
                                </p>
                                @if(!$result['is_correct'] && $result['user_answer'] !== 'Not answered')
                                <p style="font-size: 11px; color: var(--text-secondary); margin-top: 6px;">
                                    <span style="color: var(--text-muted);">Correct answer:</span>
                                    <span style="color: #22c55e; font-weight: 500;">{{ $result['question']->correct_answer }}</span>
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 16px; justify-content: center; padding: 16px 0;">
            <button onclick="retakeQuiz()" class="btn-primary" style="padding: 12px 28px; border-radius: 10px; display: flex; align-items: center; gap: 8px; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">replay</span>
                RETAKE QUIZ
            </button>
            <a href="{{ route('quizzes.index') }}" class="btn-secondary" style="padding: 12px 28px; border-radius: 10px; text-decoration: none; display: flex; align-items: center; gap: 8px; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">menu_book</span>
                BACK TO QUIZZES
            </a>
        </div>
    </div>
</div>

<script>
    function retakeQuiz() {
        fetch('{{ route("quizzes.retake", $quiz->id) }}', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            if(response.ok) {
                window.location.href = '{{ route("quizzes.take", $quiz->id) }}';
            }
        });
    }
    
    // Load and display time spent
    const timeSpent = localStorage.getItem('quiz_time_{{ $quiz->id }}');
    const timeLimit = {{ $quiz->time_limit_per_question ?? 30 }} * {{ $totalQuestions }};
    
    if(timeSpent) {
        const minutes = Math.floor(timeSpent / 60);
        const seconds = timeSpent % 60;
        const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('time-spent-stat').textContent = timeString;
        document.getElementById('time-used-display').textContent = `${minutes}m ${seconds}s`;
        
        const timePercent = Math.min(100, (timeSpent / timeLimit) * 100);
        document.getElementById('time-limit-fill').style.width = timePercent + '%';
        
        if (timeSpent > timeLimit) {
            document.getElementById('time-limit-fill').style.background = '#ef4444';
        }
    }
</script>
@endsection