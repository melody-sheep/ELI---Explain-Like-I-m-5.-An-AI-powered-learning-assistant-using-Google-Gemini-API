@extends('layouts.app')

@section('content')
<style>
    .question-card {
        transition: all 0.3s ease;
        scroll-margin-top: 80px;
    }
    .question-card.answered {
        border-left: 4px solid var(--accent-green);
    }
    .option-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .option-btn:hover:not(.disabled) {
        transform: translateX(4px);
        border-color: var(--accent-purple);
    }
    .option-btn.selected {
        background-color: var(--accent-purple);
        border-color: var(--accent-purple);
        color: white;
    }
    .timer-circle {
        transition: stroke-dashoffset 0.1s linear;
    }
    .result-card {
        animation: slideUp 0.5s ease;
    }
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .confetti {
        position: fixed;
        pointer-events: none;
        z-index: 1000;
    }
</style>

<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="{{ route('quizzes.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO QUIZZES</p>
            </div>
        </a>
    </div>

    <!-- Quiz Info Sidebar -->
    <div style="padding: 20px;">
        <div style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 12px; padding: 16px; border: 1px solid var(--border);">
            <h3 style="font-size: 14px; font-weight: 700; color: var(--text-primary);">{{ $quiz->title }}</h3>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">{{ Str::limit($quiz->description, 100) }}</p>
            
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; color: var(--text-muted);">Questions</span>
                    <span style="font-size: 13px; font-weight: 600; color: var(--text-primary);">{{ $quiz->questions->count() }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 11px; color: var(--text-muted);">Answered</span>
                    <span style="font-size: 13px; font-weight: 600; color: var(--accent-green);" id="answered-count">0</span>
                </div>
            </div>
        </div>

        <!-- Question Navigator -->
        <div style="margin-top: 20px;">
            <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px;">JUMP TO QUESTION</p>
            <div id="question-nav" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px;">
                @foreach($quiz->questions as $index => $question)
                    <button onclick="scrollToQuestion({{ $index }})" data-q-index="{{ $index }}" style="padding: 8px; background-color: var(--bg-tertiary); border: 1px solid var(--border); border-radius: 8px; cursor: pointer; font-size: 12px; color: var(--text-secondary);">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 800px; margin: 0 auto; padding: 24px; width: 100%;">
        <!-- Quiz Header with Timer -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding: 16px; background-color: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border);">
            <div>
                <h1 style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ $quiz->title }}</h1>
                <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Answer all questions to see your score</p>
            </div>
            <div id="timer-container" style="text-align: center;">
                <div id="timer-display" style="position: relative; width: 60px; height: 60px; margin: 0 auto;">
                    <svg width="60" height="60">
                        <circle cx="30" cy="30" r="26" fill="none" stroke="var(--border)" stroke-width="4"/>
                        <circle id="timer-circle" cx="30" cy="30" r="26" fill="none" stroke="var(--accent-purple)" stroke-width="4" stroke-dasharray="163.36" stroke-dashoffset="0" transform="rotate(-90 30 30)"/>
                    </svg>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <span id="timer-text" style="font-size: 14px; font-weight: 700; color: var(--text-primary);">00:00</span>
                    </div>
                </div>
                <p style="font-size: 9px; color: var(--text-muted); margin-top: 4px;">Time Left</p>
                <button type="button" onclick="toggleTimer()" style="margin-top: 8px; background: rgba(168, 85, 247, 0.1); border: 1px solid #a855f7; color: #a855f7; padding: 4px 12px; border-radius: 20px; font-size: 10px; cursor: pointer;">
                    <span id="timer-toggle-text">Hide Timer</span>
                </button>
            </div>

        </div>

        @if(session('result'))
            <div class="result-card" style="border: 2px solid var(--accent-green); background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(168, 85, 247, 0.1)); padding: 30px; margin-bottom: 30px; text-align: center; border-radius: 16px;">
                <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 64px;">emoji_events</span>
                <p style="font-size: 32px; font-weight: 700; color: var(--text-primary); margin-top: 16px;">{{ session('result') }}</p>
                <div style="margin-top: 20px;">
                    <button onclick="reviewAnswers()" style="border: 1px solid var(--accent-purple); background-color: transparent; color: var(--accent-purple); padding: 10px 20px; border-radius: 8px; cursor: pointer; margin-right: 10px;">
                        Review Answers
                    </button>
                    <a href="{{ route('quizzes.index') }}" style="border: none; background: linear-gradient(135deg, var(--accent-purple), var(--accent)); color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; display: inline-block;">
                        Back to Quizzes
                    </a>
                </div>
            </div>
        @endif

        <form id="quiz-form" action="{{ route('quizzes.submit', $quiz->id) }}" method="POST">
            @csrf
            @foreach($quiz->questions as $index => $question)
                @php
                    $savedAnswer = session("quiz_answers.{$quiz->id}.{$question->id}", null);
                @endphp
                <div id="question-{{ $index }}" class="question-card" data-question-id="{{ $question->id }}" style="border: 1px solid var(--border); background-color: var(--bg-secondary); padding: 24px; margin-bottom: 20px; border-radius: 12px; {{ $savedAnswer ? 'border-left: 4px solid var(--accent-green);' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <span style="font-size: 11px; color: var(--accent-purple); background-color: rgba(168, 85, 247, 0.1); padding: 4px 12px; border-radius: 20px;">
                            Question {{ $index + 1 }} of {{ $quiz->questions->count() }}
                        </span>
                        <span id="status-{{ $index }}" style="font-size: 10px; color: {{ $savedAnswer ? 'var(--accent-green)' : 'var(--text-muted)' }};">
                            {{ $savedAnswer ? '✓ Answered' : 'Not answered' }}
                        </span>
                    </div>

                    <p style="font-size: 18px; font-weight: 500; color: var(--text-primary); margin-bottom: 24px; line-height: 1.5;">
                        {{ $question->question }}
                    </p>

                    @php 
                        $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                    @endphp
                    @if(is_array($options))
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            @foreach($options as $option)
                                <label class="option-btn" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid var(--border); border-radius: 10px; cursor: pointer;">
                                    <input type="radio" name="question_{{ $question->id }}" value="{{ $option }}" style="cursor: pointer;" {{ $savedAnswer == $option ? 'checked' : '' }} onchange="markAnswered({{ $index }}, {{ $question->id }})">
                                    <span style="font-size: 14px; color: var(--text-secondary);">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            <div style="display: flex; gap: 16px; margin-top: 32px; position: sticky; bottom: 20px; background-color: var(--bg-primary); padding: 16px 0;">
                <button type="button" onclick="submitQuiz()" style="flex: 2; border: none; background: linear-gradient(135deg, var(--accent-purple), var(--accent)); color: white; padding: 14px; cursor: pointer; font-size: 14px; font-weight: 600; border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span class="material-symbols-outlined">check_circle</span>
                    SUBMIT QUIZ
                </button>
                <button type="button" onclick="resetQuiz()" style="flex: 1; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); padding: 14px; cursor: pointer; font-size: 14px; border-radius: 10px;">
                    <span class="material-symbols-outlined">refresh</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let totalQuestions = {{ $quiz->questions->count() }};
    let answeredCount = 0;
    let timer = null;
    let timeLeft = 300; // 5 minutes in seconds
    let quizSubmitted = false;
    
    // Calculate initial answered count
    document.querySelectorAll('input[type="radio"]:checked').forEach(() => {
        answeredCount++;
    });
    updateAnsweredCount();
    
    function markAnswered(index, questionId) {
        const questionCard = document.getElementById(`question-${index}`);
        const statusSpan = document.getElementById(`status-${index}`);
        const radio = document.querySelector(`input[name="question_${questionId}"]:checked`);
        
        if (radio) {
            if (!questionCard.classList.contains('answered')) {
                answeredCount++;
                questionCard.style.borderLeft = '4px solid var(--accent-green)';
                statusSpan.textContent = '✓ Answered';
                statusSpan.style.color = 'var(--accent-green)';
                updateAnsweredCount();
                saveAnswer(questionId, radio.value);
            }
        } else {
            if (questionCard.classList.contains('answered')) {
                answeredCount--;
                questionCard.style.borderLeft = '1px solid var(--border)';
                statusSpan.textContent = 'Not answered';
                statusSpan.style.color = 'var(--text-muted)';
                updateAnsweredCount();
                saveAnswer(questionId, null);
            }
        }
        
        // Update navigation button style
        const navBtn = document.querySelector(`#question-nav button[data-q-index="${index}"]`);
        if (navBtn) {
            if (radio) {
                navBtn.style.backgroundColor = 'var(--accent-green)';
                navBtn.style.color = 'white';
                navBtn.style.borderColor = 'var(--accent-green)';
            } else {
                navBtn.style.backgroundColor = 'var(--bg-tertiary)';
                navBtn.style.color = 'var(--text-secondary)';
                navBtn.style.borderColor = 'var(--border)';
            }
        }
    }
    
    function updateAnsweredCount() {
        document.getElementById('answered-count').textContent = `${answeredCount}/${totalQuestions}`;
        
        // Auto-save progress
        localStorage.setItem(`quiz_${ {{ $quiz->id }} }_progress`, JSON.stringify({
            answers: getCurrentAnswers(),
            timestamp: new Date().toISOString()
        }));
    }
    
    function getCurrentAnswers() {
        const answers = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const name = radio.getAttribute('name');
            answers[name] = radio.value;
        });
        return answers;
    }
    
    function saveAnswer(questionId, answer) {
        fetch('/lms/quizzes/save-answer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                quiz_id: {{ $quiz->id }},
                question_id: questionId,
                answer: answer
            })
        });
    }
    
    function scrollToQuestion(index) {
        const element = document.getElementById(`question-${index}`);
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        element.style.backgroundColor = 'rgba(168, 85, 247, 0.05)';
        setTimeout(() => {
            element.style.backgroundColor = '';
        }, 1000);
    }
    
    function startTimer() {
        const savedTime = localStorage.getItem(`quiz_${ {{ $quiz->id }} }_time`);
        if (savedTime && !quizSubmitted) {
            timeLeft = parseInt(savedTime);
        }
        
        timer = setInterval(() => {
            if (quizSubmitted) return;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                alert('Time is up! Submitting your quiz...');
                submitQuiz();
            } else {
                timeLeft--;
                localStorage.setItem(`quiz_${ {{ $quiz->id }} }_time`, timeLeft);
                updateTimerDisplay();
            }
        }, 1000);
    }
    
    function updateTimerDisplay() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timer-text').textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Update timer circle
        const circumference = 163.36;
        const offset = circumference - (timeLeft / 300) * circumference;
        const timerCircle = document.getElementById('timer-circle');
        if (timerCircle) {
            timerCircle.style.strokeDashoffset = offset;
        }
        
        // Change color when time is low
        if (timeLeft < 60) {
            timerCircle.style.stroke = 'var(--accent-red)';
        }
    }
    
    function submitQuiz() {
        if (quizSubmitted) return;
        
        const unanswered = totalQuestions - answeredCount;
        if (unanswered > 0) {
            if (!confirm(`You have ${unanswered} unanswered question(s). Submit anyway?`)) {
                return;
            }
        }
        
        quizSubmitted = true;
        clearInterval(timer);
        document.getElementById('quiz-form').submit();
    }
    
    function resetQuiz() {
        if (confirm('Are you sure you want to reset all answers?')) {
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.checked = false;
            });
            answeredCount = 0;
            updateAnsweredCount();
            
            // Reset all question cards
            document.querySelectorAll('.question-card').forEach((card, index) => {
                card.style.borderLeft = '1px solid var(--border)';
                const statusSpan = document.getElementById(`status-${index}`);
                if (statusSpan) {
                    statusSpan.textContent = 'Not answered';
                    statusSpan.style.color = 'var(--text-muted)';
                }
            });
            
            // Reset navigation buttons
            document.querySelectorAll('#question-nav button').forEach(btn => {
                btn.style.backgroundColor = 'var(--bg-tertiary)';
                btn.style.color = 'var(--text-secondary)';
                btn.style.borderColor = 'var(--border)';
            });
            
            localStorage.removeItem(`quiz_${ {{ $quiz->id }} }_progress`);
        }
    }
    
    function reviewAnswers() {
        // Scroll to first question with wrong answer
        alert('📝 Review mode: Click on any question to see detailed explanations');
    }
    
    function createConfetti() {
        for (let i = 0; i < 100; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = '-10px';
            confetti.style.width = Math.random() * 10 + 5 + 'px';
            confetti.style.height = Math.random() * 10 + 5 + 'px';
            confetti.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
            confetti.style.position = 'fixed';
            confetti.style.animation = `slideDown ${Math.random() * 2 + 1}s linear forwards`;
            document.body.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 3000);
        }
    }
    
    // Load saved progress
    function loadSavedProgress() {
        const savedProgress = localStorage.getItem(`quiz_${ {{ $quiz->id }} }_progress`);
        if (savedProgress && !quizSubmitted) {
            const progress = JSON.parse(savedProgress);
            for (const [name, value] of Object.entries(progress.answers)) {
                const radio = document.querySelector(`input[name="${name}"][value="${value}"]`);
                if (radio) {
                    radio.checked = true;
                    const match = name.match(/\d+/);
                    if (match) {
                        const questionCard = document.querySelector(`.question-card[data-question-id="${match[0]}"]`);
                        if (questionCard) {
                            const index = Array.from(document.querySelectorAll('.question-card')).indexOf(questionCard);
                            markAnswered(index, match[0]);
                        }
                    }
                }
            }
        }
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            submitQuiz();
        }
    });
    
    let timerVisible = true;

    function toggleTimer() {
        timerVisible = !timerVisible;
        const timerDisplay = document.getElementById('timer-display');
        const toggleText = document.getElementById('timer-toggle-text');

        if (timerVisible) {
            if (timerDisplay) timerDisplay.style.display = 'block';
            if (toggleText) toggleText.textContent = 'Hide Timer';
        } else {
            if (timerDisplay) timerDisplay.style.display = 'none';
            if (toggleText) toggleText.textContent = 'Show Timer';
        }
    }

    // Initialize
    loadSavedProgress();
    startTimer();

    
    // Check for high score and create confetti
    @if(session('result'))
        const scoreMatch = '{{ session('result') }}'.match(/(\d+)%/);
        if (scoreMatch && parseInt(scoreMatch[1]) >= 80) {
            createConfetti();
        }
    @endif
</script>

<style>
    @keyframes slideDown {
        to {
            transform: translateY(100vh);
            opacity: 0;
        }
    }
</style>
@endsection