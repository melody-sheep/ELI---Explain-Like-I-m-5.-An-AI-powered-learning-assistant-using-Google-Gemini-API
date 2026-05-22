@extends('layouts.app')

@section('content')
<style>
    .flashcard {
        perspective: 1000px;
        cursor: pointer;
        min-height: 400px;
    }
    .flashcard-inner {
        position: relative;
        width: 100%;
        min-height: 400px;
        text-align: center;
        transition: transform 0.6s;
        transform-style: preserve-3d;
    }
    .flashcard.flipped .flashcard-inner {
        transform: rotateY(180deg);
    }
    .flashcard-front, .flashcard-back {
        position: absolute;
        width: 100%;
        min-height: 400px;
        backface-visibility: hidden;
        border-radius: 16px;
        padding: 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--card-bg);
        border: 2px solid var(--border);
    }
    .flashcard-back {
        transform: rotateY(180deg);
        border-color: var(--accent-green);
    }
    .question-text, .answer-text {
        color: var(--text-primary);
        font-size: 1.25rem;
        line-height: 1.5;
        text-align: center;
        font-family: 'Consolas', monospace;
    }
    .card-label {
        color: var(--text-muted);
        font-family: 'Consolas', monospace;
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    .card-counter {
        color: var(--text-primary);
        font-family: 'Consolas', monospace;
        font-weight: bold;
    }
    .btn-secondary {
        background-color: var(--bg-tertiary);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        font-family: 'Consolas', monospace;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .btn-secondary:hover {
        background-color: var(--hover-bg);
    }
    .btn-primary {
        background: linear-gradient(135deg, var(--accent), var(--accent-purple));
        color: white;
        font-family: 'Consolas', monospace;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .btn-primary:hover {
        opacity: 0.9;
    }
    .rating-feedback {
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
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        animation: fadeIn 0.2s ease;
    }
    .completion-modal {
        background: var(--bg-secondary);
        border-radius: 20px;
        padding: 28px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        border: 1px solid var(--border);
        animation: scaleIn 0.3s ease;
    }
    .stat-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }
    .progress-bar-bg {
        background-color: var(--border);
        border-radius: 4px;
    }
    .progress-bar-fill {
        background: linear-gradient(90deg, var(--accent-yellow), var(--accent-purple));
        border-radius: 4px;
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('flashcards.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
                <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
                </div>
                <div>
                    <span style="font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">BACK</span>
                    <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted); font-family: 'Consolas', monospace;">TO DECKS</p>
                </div>
            </a>
            <button onclick="toggleTheme()" class="theme-btn" style="width: 36px; height: 36px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); cursor: pointer; border-radius: 8px;">
                <span id="theme-icon" class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 20px;">dark_mode</span>
            </button>
        </div>
    </div>

    <div style="padding: 20px;">
        <div style="margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">{{ $deck->title }}</h2>
            <p style="font-size: 12px; color: var(--text-muted); margin-top: 8px; font-family: 'Consolas', monospace;">{{ $deck->description }}</p>
            <div style="margin-top: 12px; display: flex; gap: 12px;">
                <span class="stat-badge" style="padding: 4px 10px; border-radius: 20px; font-size: 10px; background: rgba(34,197,94,0.1); color: var(--accent-green); font-family: 'Consolas', monospace;">
                    {{ $deck->flashcards->count() }} cards
                </span>
                <span class="stat-badge-yellow" style="padding: 4px 10px; border-radius: 20px; font-size: 10px; background: rgba(234,179,8,0.1); color: var(--accent-yellow); font-family: 'Consolas', monospace;" id="mastered-count">
                    0 mastered
                </span>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px; font-family: 'Consolas', monospace; letter-spacing: 0.5px;">CARDS IN THIS DECK</p>
            <div id="cards-list" style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($deck->flashcards as $index => $card)
                    <div onclick="selectCard({{ $index }})" data-card-id="{{ $card->id }}" data-index="{{ $index }}" class="card-list-item" style="padding: 12px; border: 1px solid var(--border); border-radius: 10px; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-symbols-outlined mastery-icon" style="color: {{ $card->mastery_level == 'mastered' ? 'var(--accent-green)' : ($card->mastery_level == 'learning' ? 'var(--accent-yellow)' : 'var(--text-muted)') }}; font-size: 18px;">
                                {{ $card->mastery_level == 'mastered' ? 'check_circle' : 'radio_button_unchecked' }}
                            </span>
                            <div style="flex: 1;">
                                <p style="font-size: 12px; color: var(--text-primary); font-family: 'Consolas', monospace;">{{ Str::limit($card->question, 55) }}</p>
                            </div>
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">chevron_right</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; overflow-y: auto;">
    <div style="width: 100%; max-width: 550px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="card-label" style="font-size: 11px;">CARD</span>
                <span class="card-counter" id="currentIndex" style="font-size: 20px;">1</span>
                <span class="card-label" style="font-size: 11px;">OF {{ $deck->flashcards->count() }}</span>
            </div>
            <button onclick="shuffleDeck()" class="btn-secondary" style="padding: 5px 14px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">shuffle</span>
                SHUFFLE
            </button>
        </div>

        <div class="flashcard" onclick="flipCard()">
            <div class="flashcard-inner">
                <div class="flashcard-front">
                    <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 48px; margin-bottom: 20px;">help</span>
                    <p id="questionText" class="question-text"></p>
                    <p class="card-label" style="font-size: 10px; margin-top: 30px;">CLICK TO FLIP</p>
                </div>
                <div class="flashcard-back">
                    <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 48px; margin-bottom: 20px;">lightbulb</span>
                    <p id="answerText" class="answer-text"></p>
                    <div style="display: flex; gap: 10px; justify-content: center; margin-top: 30px;" onclick="event.stopPropagation()">
                        <button onclick="rateCard('review', event, 'Hard')" class="rating-btn" style="padding: 6px 16px; border-radius: 30px; font-size: 11px; cursor: pointer; background-color: var(--accent-red); color: white; border: none; font-family: 'Consolas', monospace; font-weight: 600;">HARD</button>
                        <button onclick="rateCard('learning', event, 'Medium')" class="rating-btn" style="padding: 6px 16px; border-radius: 30px; font-size: 11px; cursor: pointer; background-color: var(--accent-yellow); color: black; border: none; font-family: 'Consolas', monospace; font-weight: 600;">MEDIUM</button>
                        <button onclick="rateCard('mastered', event, 'Easy')" class="rating-btn" style="padding: 6px 16px; border-radius: 30px; font-size: 11px; cursor: pointer; background-color: var(--accent-green); color: white; border: none; font-family: 'Consolas', monospace; font-weight: 600;">EASY</button>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 16px; margin-top: 32px;">
            <button onclick="prevCard()" class="btn-secondary" style="padding: 8px 20px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">chevron_left</span>
                PREVIOUS
            </button>
            <button onclick="nextCard()" class="btn-primary" style="padding: 8px 20px; border-radius: 10px; cursor: pointer; border: none; display: flex; align-items: center; gap: 4px;">
                NEXT
                <span class="material-symbols-outlined" style="font-size: 16px;">chevron_right</span>
            </button>
        </div>

        <div style="margin-top: 32px;">
            <div class="progress-bar-bg" style="height: 3px; border-radius: 2px; overflow: hidden;">
                <div id="progressBar" class="progress-bar-fill" style="height: 100%; width: 0%; transition: width 0.3s;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let flashcards = @json($deck->flashcards);
    let currentIndex = 0;
    let isFlipped = false;
    let shuffledCards = [...flashcards];
    let sessionStartTime = Date.now();
    
    function updateThemeIcon() {
        const icon = document.getElementById('theme-icon');
        if (icon) {
            const isLight = document.body.classList.contains('light-mode');
            icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        }
    }
    
    function selectCard(index) {
        currentIndex = index;
        displayCard();
        document.querySelector('.flashcard').classList.remove('flipped');
        isFlipped = false;
    }
    
    function displayCard() {
        if (!shuffledCards.length) return;
        const card = shuffledCards[currentIndex];
        document.getElementById('questionText').textContent = card.question;
        document.getElementById('answerText').textContent = card.answer;
        document.getElementById('currentIndex').textContent = currentIndex + 1;
        
        const progress = ((currentIndex + 1) / shuffledCards.length) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
        
        document.querySelectorAll('.card-list-item').forEach((item, idx) => {
            if (idx === currentIndex) {
                item.style.borderColor = 'var(--accent-yellow)';
                item.style.backgroundColor = 'rgba(234,179,8,0.1)';
            } else {
                item.style.borderColor = 'var(--border)';
                item.style.backgroundColor = 'transparent';
            }
        });
    }
    
    function flipCard() {
        document.querySelector('.flashcard').classList.toggle('flipped');
        isFlipped = !isFlipped;
    }
    
    function showRatingFeedback(level, displayText) {
        const colors = {
            review: { bg: '#ef4444', icon: 'sentiment_very_dissatisfied' },
            learning: { bg: '#eab308', icon: 'sentiment_satisfied' },
            mastered: { bg: '#22c55e', icon: 'sentiment_very_satisfied' }
        };
        
        const feedback = document.createElement('div');
        feedback.className = 'rating-feedback';
        feedback.style.backgroundColor = colors[level].bg;
        feedback.style.color = level === 'learning' ? 'black' : 'white';
        feedback.innerHTML = `
            <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">${colors[level].icon}</span>
            ${displayText}
        `;
        document.body.appendChild(feedback);
        
        setTimeout(() => feedback.remove(), 1200);
    }
    
    function showCompletionDialog() {
        const totalCards = shuffledCards.length;
        const mastered = shuffledCards.filter(c => c.mastery_level === 'mastered').length;
        const learning = shuffledCards.filter(c => c.mastery_level === 'learning').length;
        const review = shuffledCards.filter(c => c.mastery_level === 'review').length;
        const timeSpent = Math.round((Date.now() - sessionStartTime) / 1000);
        const minutes = Math.floor(timeSpent / 60);
        const seconds = timeSpent % 60;
        const accuracy = totalCards > 0 ? Math.round((mastered / totalCards) * 100) : 0;
        
        const modalHtml = `
            <div class="modal-overlay" onclick="closeModal()">
                <div class="completion-modal" onclick="event.stopPropagation()">
                    <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 56px;">celebration</span>
                    <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 12px 0 6px; font-family: 'Consolas', monospace;">SESSION COMPLETE</h2>
                    <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 11px; font-family: 'Consolas', monospace;">Great job studying this deck!</p>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px;">
                        <div style="text-align: center; padding: 10px; background: var(--bg-tertiary); border-radius: 10px;">
                            <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">auto_stories</span>
                            <p style="font-size: 20px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">${totalCards}</p>
                            <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">TOTAL CARDS</p>
                        </div>
                        <div style="text-align: center; padding: 10px; background: var(--bg-tertiary); border-radius: 10px;">
                            <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 22px;">check_circle</span>
                            <p style="font-size: 20px; font-weight: 700; color: var(--accent-green); font-family: 'Consolas', monospace;">${mastered}</p>
                            <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">MASTERED</p>
                        </div>
                        <div style="text-align: center; padding: 10px; background: var(--bg-tertiary); border-radius: 10px;">
                            <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 22px;">progress_activity</span>
                            <p style="font-size: 20px; font-weight: 700; color: var(--accent-yellow); font-family: 'Consolas', monospace;">${learning}</p>
                            <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">LEARNING</p>
                        </div>
                        <div style="text-align: center; padding: 10px; background: var(--bg-tertiary); border-radius: 10px;">
                            <span class="material-symbols-outlined" style="color: var(--accent-red); font-size: 22px;">priority_high</span>
                            <p style="font-size: 20px; font-weight: 700; color: var(--accent-red); font-family: 'Consolas', monospace;">${review}</p>
                            <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">REVIEW</p>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px; padding: 12px; background: linear-gradient(135deg, var(--accent), var(--accent-purple)); border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: white; font-size: 11px; font-family: 'Consolas', monospace;">ACCURACY</span>
                            <span style="font-size: 22px; font-weight: bold; color: white; font-family: 'Consolas', monospace;">${accuracy}%</span>
                        </div>
                        <div style="height: 4px; background: rgba(255,255,255,0.3); border-radius: 2px; margin-top: 8px;">
                            <div style="width: ${accuracy}%; height: 100%; background: white; border-radius: 2px;"></div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: center; margin-bottom: 16px;">
                        <div style="text-align: center; padding: 8px 16px; background: var(--bg-tertiary); border-radius: 8px;">
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 18px;">schedule</span>
                            <p style="font-size: 16px; font-weight: 600; color: var(--text-primary); font-family: 'Consolas', monospace;">${minutes}:${seconds.toString().padStart(2, '0')}</p>
                            <p style="font-size: 8px; color: var(--text-muted); font-family: 'Consolas', monospace;">TIME SPENT</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 12px;">
                        <button onclick="closeModal(); resetAndContinue();" class="btn-primary" style="flex: 1; padding: 10px; border-radius: 8px; font-size: 11px; cursor: pointer; border: none; font-family: 'Consolas', monospace; font-weight: 600;">
                            STUDY AGAIN
                        </button>
                        <button onclick="closeModal(); window.location.href = '{{ route("flashcards.index") }}';" class="btn-secondary" style="flex: 1; padding: 10px; border-radius: 8px; font-size: 11px; cursor: pointer; font-family: 'Consolas', monospace; font-weight: 600;">
                            BACK TO DECKS
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    function closeModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) modal.remove();
    }
    
    function resetAndContinue() {
        shuffledCards = [...flashcards];
        currentIndex = 0;
        sessionStartTime = Date.now();
        displayCard();
        document.querySelector('.flashcard').classList.remove('flipped');
        isFlipped = false;
    }
    
    function nextCard() {
        if (currentIndex < shuffledCards.length - 1) {
            currentIndex++;
            displayCard();
            document.querySelector('.flashcard').classList.remove('flipped');
            isFlipped = false;
        } else {
            showCompletionDialog();
        }
    }
    
    function prevCard() {
        if (currentIndex > 0) {
            currentIndex--;
            displayCard();
            document.querySelector('.flashcard').classList.remove('flipped');
            isFlipped = false;
        }
    }
    
    function shuffleDeck() {
        for (let i = shuffledCards.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffledCards[i], shuffledCards[j]] = [shuffledCards[j], shuffledCards[i]];
        }
        currentIndex = 0;
        displayCard();
    }
    
    function rateCard(level, event, displayText) {
        event.stopPropagation();
        const card = shuffledCards[currentIndex];
        
        showRatingFeedback(level, displayText);
        
        fetch(`/lms/flashcards/${card.id}/mastery`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ mastery_level: level })
        }).then(() => {
            card.mastery_level = level;
            
            const masteryIcon = document.querySelector(`.card-list-item[data-card-id="${card.id}"] .mastery-icon`);
            if (masteryIcon) {
                masteryIcon.textContent = level === 'mastered' ? 'check_circle' : 'radio_button_unchecked';
                masteryIcon.style.color = level === 'mastered' ? 'var(--accent-green)' : (level === 'learning' ? 'var(--accent-yellow)' : 'var(--text-muted)');
            }
            updateStats();
            setTimeout(() => nextCard(), 350);
        });
    }
    
    function updateStats() {
        const mastered = shuffledCards.filter(c => c.mastery_level === 'mastered').length;
        document.getElementById('mastered-count').textContent = mastered + ' mastered';
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') prevCard();
        if (e.key === 'ArrowRight') nextCard();
        if (e.key === ' ' || e.key === 'Space') {
            e.preventDefault();
            flipCard();
        }
    });
    
    const observer = new MutationObserver(() => updateThemeIcon());
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    
    updateStats();
    displayCard();
    updateThemeIcon();
</script>
@endsection