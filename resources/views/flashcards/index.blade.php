@extends('layouts.app')

@section('content')
<style>
    .deck-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .deck-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .theme-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .theme-btn:hover {
        transform: rotate(15deg);
        border-color: var(--accent) !important;
    }
    
    /* Flashcard Button Styles - Yellow/Orange stroke design */
    .new-deck-btn {
        background: rgba(234, 179, 8, 0.1) !important;
        border: 1.5px solid #eab308 !important;
        color: #eab308 !important;
        font-weight: 600;
        transition: all 0.2s ease;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 12px;
        font-family: 'Consolas', monospace;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .new-deck-btn:hover {
        background: rgba(234, 179, 8, 0.2) !important;
        transform: scale(1.02);
    }
    .new-deck-btn:active {
        transform: scale(0.98);
    }
    
    .study-btn {
        background: rgba(234, 179, 8, 0.1) !important;
        border: 1.5px solid #eab308 !important;
        color: #eab308 !important;
        font-weight: 600;
        transition: all 0.2s ease;
        display: block;
        text-align: center;
        padding: 10px;
        text-decoration: none;
        border-radius: 8px;
        font-size: 11px;
        font-family: 'Consolas', monospace;
        letter-spacing: 0.5px;
    }
    .study-btn:hover {
        background: rgba(234, 179, 8, 0.2) !important;
        transform: scale(1.02);
    }
    .study-btn:active {
        transform: scale(0.98);
    }
    
    /* Delete button - red stroke design */
    .delete-deck-btn {
        background: rgba(239, 68, 68, 0.1) !important;
        border: 1.5px solid #ef4444 !important;
        color: #ef4444 !important;
        transition: all 0.2s ease;
        cursor: pointer;
        padding: 4px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .delete-deck-btn:hover {
        background: rgba(239, 68, 68, 0.2) !important;
        transform: scale(1.05);
    }
    .delete-deck-btn:active {
        transform: scale(0.95);
    }
    
    /* Bookmark button - no gray background */
    .bookmark-btn {
        background: transparent !important;
        border: none !important;
        cursor: pointer;
        padding: 4px;
    }
    .bookmark-btn:hover {
        transform: scale(1.1);
    }
    .bookmark-btn:active {
        transform: scale(0.95);
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="/" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
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

    <div style="padding: 20px;">
        <div style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 12px; padding: 20px; border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span class="material-symbols-outlined" style="color: #eab308; font-size: 32px;">auto_stories</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted); font-family: 'Consolas', monospace;">FLASHCARD STATS</p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;" id="total-decks">{{ $decks->count() }}</p>
                </div>
            </div>
            <p style="font-size: 11px; color: var(--text-secondary); font-family: 'Consolas', monospace; line-height: 1.4;">Generate flashcards from PDFs, DOCX, or TXT files. Each document becomes its own deck.</p>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px; color: var(--text-primary); font-family: 'Consolas', monospace;">
                    <span class="material-symbols-outlined" style="color: #eab308; font-size: 32px;">auto_stories</span>
                    FLASHCARD DECKS
                </h1>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; font-family: 'Consolas', monospace;">Study by topic - each deck is created from a document</p>
            </div>
            <a href="{{ route('flashcards.generate') }}" class="new-deck-btn">
                <span class="material-symbols-outlined" style="font-size: 18px;">auto_awesome</span>
                NEW DECK
            </a>
        </div>

        @if(session('success'))
            <div style="border: 1px solid #22c55e; background-color: rgba(34, 197, 94, 0.1); padding: 12px; margin-bottom: 20px; border-radius: 8px; color: #22c55e; display: flex; align-items: center; gap: 10px; font-family: 'Consolas', monospace; font-size: 12px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if($decks->isEmpty())
            <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); padding: 80px 40px; text-align: center; border-radius: 12px;">
                <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 64px;">auto_stories</span>
                <p style="color: var(--text-muted); margin-top: 20px; font-size: 14px; font-family: 'Consolas', monospace;">No flashcard decks yet. Upload a document to create your first deck!</p>
                <a href="{{ route('flashcards.generate') }}" class="new-deck-btn" style="display: inline-block; margin-top: 20px;">CREATE DECK</a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                @foreach($decks as $deck)
                    @php
                        $totalCards = $deck->flashcards->count();
                        $masteredCards = $deck->flashcards->filter(function($card) {
                            return $card->mastery_level === 'mastered';
                        })->count();
                        $progressPercent = $totalCards > 0 ? ($masteredCards / $totalCards) * 100 : 0;
                    @endphp
                    <div class="deck-card" style="border: 1px solid var(--border); background-color: var(--bg-secondary); border-radius: 16px; overflow: hidden;">
                        <div style="height: 4px; background: linear-gradient(90deg, #eab308, #f97316); width: {{ $progressPercent }}%;"></div>
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <span class="material-symbols-outlined" style="color: #eab308; font-size: 40px;">auto_stories</span>
                                <form action="{{ route('flashcards.deck.delete', $deck->id) }}" method="POST" onsubmit="return confirm('Delete this entire deck?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-deck-btn">
                                        <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                    </button>
                                </form>
                            </div>
                            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 12px 0 8px; font-family: 'Consolas', monospace;">{{ $deck->title }}</h3>
                            <p style="font-size: 11px; color: var(--text-secondary); margin-bottom: 16px; font-family: 'Consolas', monospace;">{{ Str::limit($deck->description, 80) }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span style="font-size: 10px; color: var(--text-muted); font-family: 'Consolas', monospace;">{{ $totalCards }} cards</span>
                                <span style="font-size: 10px; color: #22c55e; font-family: 'Consolas', monospace;">{{ $masteredCards }} mastered</span>
                            </div>
                            <div style="height: 4px; background-color: var(--border); border-radius: 2px; margin-bottom: 16px;">
                                <div style="width: {{ $progressPercent }}%; height: 100%; background: linear-gradient(90deg, #eab308, #f97316); border-radius: 2px;"></div>
                            </div>
                            <a href="{{ route('flashcards.deck', $deck->id) }}" class="study-btn">
                                STUDY DECK
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function updateThemeIcon() {
        const icon = document.getElementById('theme-icon');
        if (icon) {
            const isLight = document.body.classList.contains('light-mode');
            icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        }
    }
    
    const observer = new MutationObserver(() => updateThemeIcon());
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    
    updateThemeIcon();
</script>
@endsection