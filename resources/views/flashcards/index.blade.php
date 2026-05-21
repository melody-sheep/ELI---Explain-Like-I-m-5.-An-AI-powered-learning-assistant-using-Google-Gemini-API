@extends('layouts.app')

@section('content')
<style>
    .flashcard {
        perspective: 1000px;
        cursor: pointer;
        height: 400px;
    }
    .flashcard-inner {
        position: relative;
        width: 100%;
        height: 100%;
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
        height: 100%;
        backface-visibility: hidden;
        border-radius: 16px;
        padding: 32px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
        border: 2px solid var(--border);
    }
    .flashcard-back {
        transform: rotateY(180deg);
        background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%);
        border-color: var(--accent-green);
    }
    .difficulty-btn {
        transition: all 0.2s ease;
    }
    .difficulty-btn:hover {
        transform: scale(1.05);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(50px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .card-enter {
        animation: slideIn 0.3s ease;
    }
    .study-stats {
        transition: all 0.2s ease;
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

    <!-- Study Statistics -->
    <div style="padding: 20px;">
        <div class="study-stats" style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 12px; padding: 20px; border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 32px;">auto_stories</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted);">Study Progress</p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-primary);" id="mastered-count">0</p>
                </div>
            </div>
            <div style="height: 6px; background-color: var(--border); border-radius: 3px; overflow: hidden; margin-bottom: 12px;">
                <div id="mastery-progress" style="height: 100%; background: linear-gradient(90deg, var(--accent-yellow), var(--accent-green)); width: 0%; border-radius: 3px;"></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px;">
                <div style="text-align: center;">
                    <p style="font-size: 20px; font-weight: 700; color: var(--accent);" id="new-count">0</p>
                    <p style="font-size: 9px; color: var(--text-muted);">New</p>
                </div>
                <div style="text-align: center;">
                    <p style="font-size: 20px; font-weight: 700; color: var(--accent-yellow);" id="review-count">0</p>
                    <p style="font-size: 9px; color: var(--text-muted);">Due for Review</p>
                </div>
            </div>
        </div>

        <!-- Study Mode Selector -->
        <div style="margin-top: 20px;">
            <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px;">STUDY MODE</p>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button onclick="setStudyMode('normal')" id="mode-normal" class="mode-btn" style="padding: 10px; border: 1px solid var(--accent); background-color: var(--accent); color: white; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined">shuffle</span>
                    Normal Mode
                </button>
                <button onclick="setStudyMode('spaced')" id="mode-spaced" class="mode-btn" style="padding: 10px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined">schedule</span>
                    Spaced Repetition
                </button>
                <button onclick="setStudyMode('quiz')" id="mode-quiz" class="mode-btn" style="padding: 10px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined">quiz</span>
                    Quiz Mode
                </button>
            </div>
        </div>

        <!-- Settings -->
        <div style="margin-top: 20px;">
            <button onclick="toggleSettings()" style="width: 100%; padding: 8px; background: none; border: 1px solid var(--border); color: var(--text-muted); border-radius: 8px; cursor: pointer; font-size: 11px;">
                ⚙️ Settings
            </button>
            <div id="settings-panel" style="display: none; margin-top: 12px; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                    <input type="checkbox" id="auto-flip" onchange="toggleAutoFlip()">
                    <span style="font-size: 11px;">Auto-flip after 3 seconds</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" id="shuffle-cards" onchange="toggleShuffle()">
                    <span style="font-size: 11px;">Shuffle cards</span>
                </label>
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="padding: 24px;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px; color: var(--text-primary);">
                    <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 32px;">auto_stories</span>
                    FLASHCARDS
                </h1>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Master concepts with spaced repetition</p>
            </div>
            <a href="{{ route('flashcards.generate') }}" style="border: none; background: linear-gradient(135deg, var(--accent-yellow), var(--accent-orange)); color: black; padding: 10px 20px; text-decoration: none; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; border-radius: 8px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">auto_awesome</span>
                GENERATE NEW
            </a>
        </div>

        @if(session('success'))
            <div style="border: 1px solid var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 14px; margin-bottom: 20px; border-radius: 8px; color: var(--accent-green); display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if($flashcards->isEmpty())
            <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); padding: 80px 40px; text-align: center; border-radius: 12px;">
                <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 64px;">auto_stories</span>
                <p style="color: var(--text-muted); margin-top: 20px; font-size: 14px;">No flashcards yet. Upload a document to generate flashcards!</p>
                <a href="{{ route('flashcards.generate') }}" style="display: inline-block; margin-top: 20px; border: none; background: linear-gradient(135deg, var(--accent-yellow), var(--accent-orange)); color: black; padding: 10px 20px; text-decoration: none; border-radius: 8px;">Generate Flashcards</a>
            </div>
        @else
            <!-- Current Card Counter -->
            <div style="text-align: center; margin-bottom: 20px;">
                <p style="font-size: 12px; color: var(--text-muted);">
                    <span id="current-card-num">1</span> / <span id="total-cards">{{ count($flashcards) }}</span>
                </p>
            </div>

            <!-- Flashcard Display -->
            <div id="flashcard-container" style="display: flex; justify-content: center; margin-bottom: 24px;">
                <div class="flashcard" style="width: 100%; max-width: 600px;" onclick="flipCurrentCard()">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 48px; margin-bottom: 20px;">help</span>
                            <p id="question-text" style="font-size: 20px; font-weight: 500; color: var(--text-primary); line-height: 1.5;"></p>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 30px;">👆 Click to flip</p>
                        </div>
                        <div class="flashcard-back">
                            <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 48px; margin-bottom: 20px;">lightbulb</span>
                            <p id="answer-text" style="font-size: 18px; color: var(--text-secondary); line-height: 1.5;"></p>
                            <div id="difficulty-buttons" style="display: flex; gap: 12px; justify-content: center; margin-top: 30px;">
                                <button onclick="rateCard('hard', event)" class="difficulty-btn" style="border: none; background-color: var(--accent-red); color: white; padding: 8px 16px; border-radius: 20px; cursor: pointer; font-size: 12px;">😓 Hard</button>
                                <button onclick="rateCard('medium', event)" class="difficulty-btn" style="border: none; background-color: var(--accent-yellow); color: black; padding: 8px 16px; border-radius: 20px; cursor: pointer; font-size: 12px;">🤔 Medium</button>
                                <button onclick="rateCard('easy', event)" class="difficulty-btn" style="border: none; background-color: var(--accent-green); color: white; padding: 8px 16px; border-radius: 20px; cursor: pointer; font-size: 12px;">😊 Easy</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
                <button onclick="prevCard()" style="border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-primary); padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button onclick="nextCard()" style="border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-primary); padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
                <button onclick="shuffleCards()" style="border: 1px solid var(--accent); background-color: var(--bg-tertiary); color: var(--accent); padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                    <span class="material-symbols-outlined">shuffle</span>
                </button>
            </div>

            <!-- Progress Bar -->
            <div style="max-width: 600px; margin: 0 auto;">
                <div style="height: 4px; background-color: var(--border); border-radius: 2px; overflow: hidden;">
                    <div id="session-progress" style="height: 100%; background: linear-gradient(90deg, var(--accent-yellow), var(--accent-green)); width: 0%; border-radius: 2px;"></div>
                </div>
                <p style="font-size: 10px; color: var(--text-muted); text-align: center; margin-top: 8px;" id="session-status">Reviewing cards</p>
            </div>
        @endif
    </div>
</div>

<script>
    let flashcards = @json($flashcards);
    let currentIndex = 0;
    let isFlipped = false;
    let studyMode = 'normal';
    let autoFlipEnabled = false;
    let autoFlipTimer = null;
    let shuffledCards = [...flashcards];
    let sessionHistory = [];
    
    // Study statistics
    let masteredCount = 0;
    let newCards = flashcards.length;
    let reviewDue = 0;
    
    // Load saved progress from localStorage
    function loadProgress() {
        const savedProgress = localStorage.getItem('flashcard_progress');
        if (savedProgress) {
            const progress = JSON.parse(savedProgress);
            masteredCount = progress.masteredCount || 0;
            newCards = progress.newCards || flashcards.length;
            reviewDue = progress.reviewDue || 0;
            
            // Apply saved difficulty ratings
            if (progress.cardRatings) {
                flashcards.forEach(card => {
                    if (progress.cardRatings[card.id]) {
                        card.difficulty = progress.cardRatings[card.id];
                    }
                });
            }
        }
        updateStats();
    }
    
    function updateStats() {
        document.getElementById('mastered-count').textContent = masteredCount;
        document.getElementById('new-count').textContent = newCards;
        document.getElementById('review-count').textContent = reviewDue;
        const masteryPercent = flashcards.length > 0 ? (masteredCount / flashcards.length) * 100 : 0;
        document.getElementById('mastery-progress').style.width = masteryPercent + '%';
    }
    
    function displayCard() {
        const card = shuffledCards[currentIndex];
        if (!card) return;
        
        document.getElementById('question-text').textContent = card.question;
        document.getElementById('answer-text').textContent = card.answer;
        document.getElementById('current-card-num').textContent = currentIndex + 1;
        
        // Reset flip state
        if (isFlipped) {
            const flashcardDiv = document.querySelector('.flashcard');
            flashcardDiv.classList.remove('flipped');
            isFlipped = false;
        }
        
        // Update session progress
        const progress = ((currentIndex + 1) / shuffledCards.length) * 100;
        document.getElementById('session-progress').style.width = progress + '%';
        
        // Clear auto-flip timer
        if (autoFlipTimer) clearTimeout(autoFlipTimer);
        
        // Start auto-flip if enabled
        if (autoFlipEnabled && !isFlipped) {
            autoFlipTimer = setTimeout(() => {
                flipCurrentCard();
            }, 3000);
        }
    }
    
    function flipCurrentCard() {
        const flashcardDiv = document.querySelector('.flashcard');
        flashcardDiv.classList.toggle('flipped');
        isFlipped = !isFlipped;
        
        // Clear timer when manually flipped
        if (autoFlipTimer) {
            clearTimeout(autoFlipTimer);
            autoFlipTimer = null;
        }
    }
    
    function nextCard() {
        if (currentIndex < shuffledCards.length - 1) {
            currentIndex++;
            displayCard();
        } else if (currentIndex === shuffledCards.length - 1) {
            // Session complete
            alert('🎉 Congratulations! You\'ve completed this study session!');
            if (studyMode === 'spaced') {
                scheduleNextReview();
            }
            resetSession();
        }
    }
    
    function prevCard() {
        if (currentIndex > 0) {
            currentIndex--;
            displayCard();
        }
    }
    
    function rateCard(difficulty, event) {
        event.stopPropagation();
        
        const card = shuffledCards[currentIndex];
        card.difficulty = difficulty;
        
        // Update statistics based on difficulty
        if (difficulty === 'easy') {
            masteredCount++;
            newCards--;
        } else if (difficulty === 'medium') {
            reviewDue++;
        } else if (difficulty === 'hard') {
            reviewDue += 2;
        }
        
        updateStats();
        
        // Save progress
        saveProgress();
        
        // Add to session history
        sessionHistory.push({
            cardId: card.id,
            difficulty: difficulty,
            timestamp: new Date().toISOString()
        });
        
        // Automatically move to next card after rating
        setTimeout(() => {
            nextCard();
        }, 300);
    }
    
    function saveProgress() {
        const progress = {
            masteredCount: masteredCount,
            newCards: newCards,
            reviewDue: reviewDue,
            cardRatings: {}
        };
        
        flashcards.forEach(card => {
            if (card.difficulty) {
                progress.cardRatings[card.id] = card.difficulty;
            }
        });
        
        localStorage.setItem('flashcard_progress', JSON.stringify(progress));
        
        // Send to server
        fetch('/lms/flashcards/progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(progress)
        });
    }
    
    function scheduleNextReview() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        localStorage.setItem('next_review_date', tomorrow.toISOString());
        alert(`Next review scheduled for ${tomorrow.toLocaleDateString()}`);
    }
    
    function shuffleCards() {
        for (let i = shuffledCards.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffledCards[i], shuffledCards[j]] = [shuffledCards[j], shuffledCards[i]];
        }
        currentIndex = 0;
        displayCard();
    }
    
    function resetSession() {
        if (studyMode === 'spaced') {
            // Only show cards due for review
            const dueCards = shuffledCards.filter(card => {
                const lastReviewed = localStorage.getItem(`last_reviewed_${card.id}`);
                if (!lastReviewed) return true;
                const daysSince = (new Date() - new Date(lastReviewed)) / (1000 * 60 * 60 * 24);
                const difficulty = card.difficulty;
                if (difficulty === 'easy') return daysSince >= 3;
                if (difficulty === 'medium') return daysSince >= 1;
                return daysSince >= 0.5;
            });
            shuffledCards = dueCards.length > 0 ? dueCards : flashcards;
        } else {
            shuffledCards = [...flashcards];
        }
        currentIndex = 0;
        displayCard();
    }
    
    function setStudyMode(mode) {
        studyMode = mode;
        
        // Update button styles
        document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.style.backgroundColor = 'var(--bg-tertiary)';
            btn.style.color = 'var(--text-secondary)';
            btn.style.borderColor = 'var(--border)';
        });
        document.getElementById(`mode-${mode}`).style.backgroundColor = 'var(--accent)';
        document.getElementById(`mode-${mode}`).style.color = 'white';
        document.getElementById(`mode-${mode}`).style.borderColor = 'var(--accent)';
        
        resetSession();
    }
    
    function toggleAutoFlip() {
        autoFlipEnabled = document.getElementById('auto-flip').checked;
        if (autoFlipEnabled && !isFlipped) {
            autoFlipTimer = setTimeout(() => {
                flipCurrentCard();
            }, 3000);
        }
    }
    
    function toggleShuffle() {
        if (document.getElementById('shuffle-cards').checked) {
            shuffleCards();
        } else {
            shuffledCards = [...flashcards];
            currentIndex = 0;
            displayCard();
        }
    }
    
    function toggleSettings() {
        const panel = document.getElementById('settings-panel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') prevCard();
        if (e.key === 'ArrowRight') nextCard();
        if (e.key === ' ' || e.key === 'Space') {
            e.preventDefault();
            flipCurrentCard();
        }
    });
    
    // Initialize
    loadProgress();
    resetSession();
</script>
@endsection