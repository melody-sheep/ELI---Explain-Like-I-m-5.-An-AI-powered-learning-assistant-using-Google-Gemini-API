@extends('layouts.app')

@section('content')
<style>
    .quiz-card {
        transition: all 0.2s ease;
        cursor: pointer;
        border-radius: 12px;
    }
    .quiz-card:hover {
        transform: translateY(-2px);
        border-color: var(--accent-purple);
    }
    
    /* Purple stroke button design (matching Lessons green stroke) */
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
    
    .btn-danger {
        background: rgba(239, 68, 68, 0.1) !important;
        border: 1.5px solid #ef4444 !important;
        color: #ef4444 !important;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.2) !important;
    }
    
    .filter-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-family: 'Consolas', monospace;
        font-size: 11px;
        padding: 6px 16px;
        transition: all 0.2s ease;
    }
    .filter-btn.active {
        color: #a855f7;
        border-bottom: 2px solid #a855f7;
    }
    .filter-btn:hover {
        color: var(--accent-purple);
    }
    
    .theme-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .theme-btn:hover {
        transform: rotate(15deg);
        border-color: var(--accent-purple) !important;
    }
    
    .toast-notification {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 10px 20px;
        border-radius: 40px;
        font-size: 12px;
        font-weight: 600;
        font-family: 'Consolas', monospace;
        z-index: 1000;
        animation: slideInRight 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    @keyframes slideInRight {
        from { transform: translateX(100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .stat-card {
        background: var(--bg-tertiary);
        border: 1px solid var(--border);
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        border-color: var(--accent-purple);
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('dashboard') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
                <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
                </div>
                <div>
                    <span style="font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">BACK</span>
                    <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted); font-family: 'Consolas', monospace;">TO HOME</p>
                </div>
            </a>
            <button onclick="toggleTheme()" class="theme-btn" style="width: 36px; height: 36px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); cursor: pointer; border-radius: 8px;">
                <span id="theme-icon" class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 20px;">dark_mode</span>
            </button>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div style="padding: 20px;">
        <div class="stat-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span class="material-symbols-outlined" style="color: #a855f7; font-size: 32px;">analytics</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted); font-family: 'Consolas', monospace;">Quiz Statistics</p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;" id="avg-score">0%</p>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div style="text-align: center; padding: 12px; background-color: var(--bg-primary); border-radius: 8px;">
                    <p style="font-size: 20px; font-weight: 700; color: #a855f7; font-family: 'Consolas', monospace;" id="total-quizzes">0</p>
                    <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">Total Quizzes</p>
                </div>
                <div style="text-align: center; padding: 12px; background-color: var(--bg-primary); border-radius: 8px;">
                    <p style="font-size: 20px; font-weight: 700; color: #22c55e; font-family: 'Consolas', monospace;" id="total-questions">0</p>
                    <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">Questions</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="padding: 24px;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px; color: var(--text-primary); font-family: 'Consolas', monospace;">
                    <span class="material-symbols-outlined" style="color: #a855f7; font-size: 32px;">quiz</span>
                    PRACTICE QUIZZES
                </h1>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; font-family: 'Consolas', monospace;">Test your knowledge and track your progress</p>
            </div>
            <a href="{{ route('quizzes.generate') }}" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                GENERATE QUIZ
            </a>
        </div>

        @if(session('success'))
            <div style="border: 1px solid #22c55e; background-color: rgba(34, 197, 94, 0.1); padding: 12px; margin-bottom: 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px; font-family: 'Consolas', monospace; font-size: 12px;">
                <span class="material-symbols-outlined" style="color: #22c55e;">check_circle</span>
                <span style="color: #22c55e;">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div style="border: 1px solid #ef4444; background-color: rgba(239, 68, 68, 0.1); padding: 12px; margin-bottom: 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px; font-family: 'Consolas', monospace; font-size: 12px;">
                <span class="material-symbols-outlined" style="color: #ef4444;">error</span>
                <span style="color: #ef4444;">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filter Tabs -->
        <div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
            <button onclick="filterQuizzes('all')" class="filter-btn active" data-filter="all">ALL QUIZZES</button>
            <button onclick="filterQuizzes('untaken')" class="filter-btn" data-filter="untaken">NOT TAKEN</button>
            <button onclick="filterQuizzes('completed')" class="filter-btn" data-filter="completed">COMPLETED</button>
        </div>

        @if($quizzes->isEmpty())
            <div style="text-align: center; padding: 80px 40px; border: 1px dashed var(--border); border-radius: 16px;">
                <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 64px; margin-bottom: 20px;">quiz</span>
                <p style="color: var(--text-muted); margin-bottom: 24px; font-size: 14px; font-family: 'Consolas', monospace;">No quizzes yet. Generate your first quiz!</p>
                <a href="{{ route('quizzes.generate') }}" class="btn-primary" style="padding: 10px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 12px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                    GENERATE QUIZ
                </a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;" id="quizzes-grid">
                @foreach($quizzes as $quiz)
                    @php
                        $bestScore = session("quiz_scores_" . (Auth::id() ?? session('guest_id')) . ".{$quiz->id}", null);
                        $timesTaken = session("quiz_times_" . (Auth::id() ?? session('guest_id')) . ".{$quiz->id}", 0);
                    @endphp
                    <div class="quiz-card" data-quiz-id="{{ $quiz->id }}" data-taken="{{ $timesTaken > 0 ? 'true' : 'false' }}" data-score="{{ $bestScore ?? 0 }}" style="border: 1px solid var(--border); background-color: var(--bg-secondary); border-radius: 12px; overflow: hidden;">
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <span style="font-size: 10px; color: #a855f7; background-color: rgba(168, 85, 247, 0.1); padding: 4px 10px; border-radius: 12px; font-family: 'Consolas', monospace;">
                                    {{ $quiz->questions_count }} questions
                                </span>
                                @if($timesTaken > 0)
                                    <span style="font-size: 9px; color: #22c55e; font-family: 'Consolas', monospace; display: flex; align-items: center; gap: 4px;">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span>
                                        Taken {{ $timesTaken }}×
                                    </span>
                                @endif
                            </div>

                            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; font-family: 'Consolas', monospace;">{{ $quiz->title }}</h3>
                            <p style="font-size: 11px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 16px; font-family: 'Consolas', monospace;">{{ Str::limit($quiz->description, 80) }}</p>

                            @if($bestScore !== null)
                                <div style="margin-bottom: 16px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <span style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">Best Score</span>
                                        <span style="font-size: 10px; font-weight: 600; color: #22c55e; font-family: 'Consolas', monospace;">{{ $bestScore }}%</span>
                                    </div>
                                    <div style="height: 3px; background-color: var(--border); border-radius: 2px; overflow: hidden;">
                                        <div style="width: {{ $bestScore }}%; height: 100%; background: linear-gradient(90deg, #a855f7, #c084fc); border-radius: 2px;"></div>
                                    </div>
                                </div>
                            @endif

                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('quizzes.take', $quiz->id) }}" class="btn-primary" style="flex: 2; padding: 8px; border-radius: 8px; text-decoration: none; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 10px;">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">{{ $timesTaken > 0 ? 'replay' : 'play_arrow' }}</span>
                                    {{ $timesTaken > 0 ? 'RETAKE' : 'TAKE QUIZ' }}
                                </a>
                                @if($timesTaken > 0)
                                    <button onclick="viewResults({{ $quiz->id }})" class="btn-secondary" style="flex: 1; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">insights</span>
                                    </button>
                                @endif
                                <form action="{{ route('quizzes.destroy', $quiz->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" style="width: 100%; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center;" onclick="return confirm('Delete this quiz?')">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    let currentFilter = 'all';
    
    function updateThemeIcon() {
        const icon = document.getElementById('theme-icon');
        if (icon) {
            const isLight = document.body.classList.contains('light-mode');
            icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        }
    }
    
    function filterQuizzes(filter) {
        currentFilter = filter;
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.style.color = 'var(--text-muted)';
            btn.style.borderBottom = 'none';
            if (btn.dataset.filter === filter) {
                btn.classList.add('active');
                btn.style.color = '#a855f7';
                btn.style.borderBottom = '2px solid #a855f7';
            }
        });
        
        const cards = document.querySelectorAll('.quiz-card');
        cards.forEach(card => {
            let show = true;
            const isTaken = card.dataset.taken === 'true';
            const score = parseInt(card.dataset.score);
            
            if (filter === 'untaken' && isTaken) show = false;
            if (filter === 'completed' && !isTaken) show = false;
            if (filter === 'high-score' && score < 80) show = false;
            
            card.style.display = show ? 'block' : 'none';
        });
    }
    
    function updateStatistics() {
        const cards = document.querySelectorAll('.quiz-card');
        let totalScore = 0;
        let scoreCount = 0;
        
        cards.forEach(card => {
            const score = parseInt(card.dataset.score);
            if (score > 0) {
                totalScore += score;
                scoreCount++;
            }
        });
        
        const avgScore = scoreCount > 0 ? Math.round(totalScore / scoreCount) : 0;
        document.getElementById('avg-score').textContent = avgScore + '%';
        document.getElementById('total-quizzes').textContent = cards.length;
        
        let totalQuestions = 0;
        cards.forEach(card => {
            const questionsText = card.querySelector('span:first-child')?.textContent;
            if (questionsText) {
                totalQuestions += parseInt(questionsText) || 0;
            }
        });
        document.getElementById('total-questions').textContent = totalQuestions;
    }
    
    function viewResults(quizId) {
        window.location.href = `/lms/quizzes/${quizId}/results`;
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        updateStatistics();
        updateThemeIcon();
    });
    
    const observer = new MutationObserver(() => updateThemeIcon());
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
</script>
@endsection