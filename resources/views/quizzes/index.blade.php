@extends('layouts.app')

@section('content')
<style>
    .quiz-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .quiz-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .streak-flame {
        animation: pulse 1s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="/" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO HOME</p>
            </div>
        </a>
    </div>

    <!-- Statistics Dashboard -->
    <div style="padding: 20px;">
        <div style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 12px; padding: 20px; border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span class="material-symbols-outlined" style="color: var(--accent-purple); font-size: 32px;">analytics</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted);">Quiz Statistics</p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-primary);" id="avg-score">0%</p>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div style="text-align: center; padding: 8px; background-color: var(--bg-primary); border-radius: 8px;">
                    <p style="font-size: 20px; font-weight: 700; color: var(--accent);" id="total-quizzes">0</p>
                    <p style="font-size: 9px; color: var(--text-muted);">Total Quizzes</p>
                </div>
                <div style="text-align: center; padding: 8px; background-color: var(--bg-primary); border-radius: 8px;">
                    <p style="font-size: 20px; font-weight: 700; color: var(--accent-green);" id="total-questions">0</p>
                    <p style="font-size: 9px; color: var(--text-muted);">Questions</p>
                </div>
            </div>

            <!-- Current Streak -->
            <div style="text-align: center; padding: 12px; background: linear-gradient(135deg, rgba(234, 179, 8, 0.1), rgba(249, 115, 22, 0.1)); border-radius: 8px;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span class="material-symbols-outlined streak-flame" style="color: var(--accent-orange);">local_fire_department</span>
                    <span style="font-size: 18px; font-weight: 700; color: var(--accent-orange);" id="current-streak">0</span>
                    <span style="font-size: 11px; color: var(--text-muted);">day streak</span>
                </div>
            </div>
        </div>

        <!-- Daily Challenge -->
        <div id="daily-challenge" style="margin-top: 20px; padding: 16px; background: linear-gradient(135deg, var(--accent-purple), var(--accent)); border-radius: 12px; cursor: pointer;" onclick="startDailyChallenge()">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="material-symbols-outlined" style="color: white; font-size: 28px;">emoji_events</span>
                <div>
                    <p style="font-size: 12px; font-weight: 600; color: white;">Daily Challenge</p>
                    <p style="font-size: 10px; color: rgba(255,255,255,0.8);">5 questions · 2 minutes</p>
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
                <h1 style="font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px; color: var(--text-primary);">
                    <span class="material-symbols-outlined" style="color: var(--accent-purple); font-size: 32px;">quiz</span>
                    PRACTICE QUIZZES
                </h1>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Test your knowledge and track your progress</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button onclick="showLeaderboard()" style="border: 1px solid var(--border); background-color: var(--bg-secondary); color: var(--text-primary); padding: 10px 16px; border-radius: 8px; cursor: pointer;">
                    <span class="material-symbols-outlined">leaderboard</span>
                </button>
                <a href="{{ route('quizzes.generate') }}" style="border: none; background: linear-gradient(135deg, var(--accent-purple), var(--accent)); color: white; padding: 10px 20px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; border-radius: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                    GENERATE QUIZ
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="border: 1px solid var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 14px; margin-bottom: 20px; border-radius: 8px; color: var(--accent-green); display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Tabs -->
        <div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
            <button onclick="filterQuizzes('all')" class="quiz-filter active" data-filter="all" style="padding: 6px 16px; border: none; background: none; color: var(--text-primary); cursor: pointer; font-size: 13px; border-radius: 20px;">All Quizzes</button>
            <button onclick="filterQuizzes('untaken')" class="quiz-filter" data-filter="untaken" style="padding: 6px 16px; border: none; background: none; color: var(--text-muted); cursor: pointer; font-size: 13px;">Not Taken</button>
            <button onclick="filterQuizzes('completed')" class="quiz-filter" data-filter="completed" style="padding: 6px 16px; border: none; background: none; color: var(--text-muted); cursor: pointer; font-size: 13px;">Completed</button>
            <button onclick="filterQuizzes('high-score')" class="quiz-filter" data-filter="high-score" style="padding: 6px 16px; border: none; background: none; color: var(--text-muted); cursor: pointer; font-size: 13px;">High Scores</button>
        </div>

        @if($quizzes->isEmpty())
            <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); padding: 80px 40px; text-align: center; border-radius: 12px;">
                <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 64px;">quiz</span>
                <p style="color: var(--text-muted); margin-top: 20px; font-size: 14px;">No quizzes yet. Generate your first quiz from a document!</p>
                <a href="{{ route('quizzes.generate') }}" style="display: inline-block; margin-top: 20px; border: none; background: linear-gradient(135deg, var(--accent-purple), var(--accent)); color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;">Generate Quiz</a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;" id="quizzes-grid">
                @foreach($quizzes as $quiz)
                    @php
                        $bestScore = session("quiz_score.{$quiz->id}", null);
                        $timesTaken = session("quiz_times.{$quiz->id}", 0);
                    @endphp
                    <div class="quiz-card" data-quiz-id="{{ $quiz->id }}" data-taken="{{ $timesTaken > 0 ? 'true' : 'false' }}" data-score="{{ $bestScore ?? 0 }}" style="border: 1px solid var(--border); background-color: var(--bg-secondary); border-radius: 12px; overflow: hidden;">
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <span style="font-size: 11px; color: var(--accent-purple); background-color: rgba(168, 85, 247, 0.1); padding: 4px 10px; border-radius: 12px;">
                                    {{ $quiz->questions_count }} questions
                                </span>
                                @if($timesTaken > 0)
                                    <span style="font-size: 10px; color: var(--accent-green);">
                                        <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span>
                                        Taken {{ $timesTaken }}×
                                    </span>
                                @endif
                            </div>

                            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">{{ $quiz->title }}</h3>
                            <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 16px;">{{ Str::limit($quiz->description, 80) }}</p>

                            @if($bestScore !== null)
                                <div style="margin-bottom: 16px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <span style="font-size: 10px; color: var(--text-muted);">Best Score</span>
                                        <span style="font-size: 11px; font-weight: 600; color: var(--accent-green);">{{ $bestScore }}%</span>
                                    </div>
                                    <div style="height: 4px; background-color: var(--border); border-radius: 2px; overflow: hidden;">
                                        <div style="width: {{ $bestScore }}%; height: 100%; background: linear-gradient(90deg, var(--accent-purple), var(--accent)); border-radius: 2px;"></div>
                                    </div>
                                </div>
                            @endif

                            <div style="display: flex; gap: 10px;">
                                <a href="{{ route('quizzes.take', $quiz->id) }}" style="flex: 2; border: none; background: linear-gradient(135deg, var(--accent-purple), var(--accent)); color: white; text-align: center; padding: 10px; text-decoration: none; font-size: 12px; font-weight: 600; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">{{ $timesTaken > 0 ? 'replay' : 'play_arrow' }}</span>
                                    {{ $timesTaken > 0 ? 'RETAKE' : 'TAKE QUIZ' }}
                                </a>
                                @if($timesTaken > 0)
                                    <button onclick="viewResults({{ $quiz->id }})" style="flex: 1; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); padding: 10px; cursor: pointer; border-radius: 8px;">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">insights</span>
                                    </button>
                                @endif
                                <form action="{{ route('quizzes.destroy', $quiz->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="width: 100%; border: 1px solid var(--accent-red); background-color: transparent; color: var(--accent-red); padding: 10px; cursor: pointer; border-radius: 8px;" onclick="return confirm('Delete this quiz?')">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
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
    
    function filterQuizzes(filter) {
        currentFilter = filter;
        
        // Update active button styling
        document.querySelectorAll('.quiz-filter').forEach(btn => {
            btn.classList.remove('active');
            btn.style.color = 'var(--text-muted)';
            if (btn.dataset.filter === filter) {
                btn.classList.add('active');
                btn.style.color = 'var(--accent-purple)';
                btn.style.borderBottom = '2px solid var(--accent-purple)';
            } else {
                btn.style.borderBottom = 'none';
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
        
        // Load streak from localStorage
        const streak = localStorage.getItem('quiz_streak') || 0;
        document.getElementById('current-streak').textContent = streak;
    }
    
    function viewResults(quizId) {
        window.location.href = `/lms/quizzes/${quizId}/results`;
    }
    
    function showLeaderboard() {
        alert('🏆 Leaderboard coming soon! Compete with friends and track rankings.');
    }
    
    function startDailyChallenge() {
        alert('🎯 Daily Challenge: Complete 5 random questions to earn bonus points!');
    }
    
    // Update streak when quiz is completed
    function updateStreak() {
        const lastQuizDate = localStorage.getItem('last_quiz_date');
        const today = new Date().toDateString();
        let streak = parseInt(localStorage.getItem('quiz_streak') || 0);
        
        if (lastQuizDate === today) {
            // Already updated today
            return;
        }
        
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        
        if (lastQuizDate === yesterday.toDateString()) {
            streak++;
        } else {
            streak = 1;
        }
        
        localStorage.setItem('quiz_streak', streak);
        localStorage.setItem('last_quiz_date', today);
        document.getElementById('current-streak').textContent = streak;
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        updateStatistics();
        
        // Check if we just completed a quiz
        if (window.location.hash === '#quiz-completed') {
            updateStreak();
        }
    });
</script>
@endsection