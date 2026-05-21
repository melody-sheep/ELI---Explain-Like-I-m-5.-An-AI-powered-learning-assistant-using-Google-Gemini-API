@extends('layouts.app')

@section('content')
<style>
    .lesson-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .lesson-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .progress-bar {
        transition: width 0.5s ease;
    }
    .filter-btn.active {
        background-color: var(--accent);
        color: white;
        border-color: var(--accent);
    }
    .search-highlight {
        background-color: rgba(34, 197, 94, 0.3);
        border-radius: 4px;
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <!-- Sidebar with progress summary -->
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

    <!-- Progress Summary -->
    <div style="padding: 20px;">
        <div style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 12px; padding: 20px; border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 32px;">trending_up</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted);">Overall Progress</p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-primary);" id="overall-progress">0%</p>
                </div>
            </div>
            <div style="height: 6px; background-color: var(--border); border-radius: 3px; overflow: hidden;">
                <div class="progress-bar" id="progress-bar-fill" style="height: 100%; background: linear-gradient(90deg, var(--accent-green), var(--accent)); width: 0%; border-radius: 3px;"></div>
            </div>
            <p style="font-size: 10px; color: var(--text-muted); margin-top: 12px;" id="completed-count">0 lessons completed</p>
        </div>

        <!-- Quick Filters -->
        <div style="margin-top: 20px;">
            <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px;">QUICK FILTERS</p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <button onclick="filterLessons('all')" class="filter-btn active" data-filter="all" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); border-radius: 20px; cursor: pointer; font-size: 11px; transition: all 0.2s;">All</button>
                <button onclick="filterLessons('in-progress')" class="filter-btn" data-filter="in-progress" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); border-radius: 20px; cursor: pointer; font-size: 11px;">In Progress</button>
                <button onclick="filterLessons('completed')" class="filter-btn" data-filter="completed" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); border-radius: 20px; cursor: pointer; font-size: 11px;">Completed</button>
                <button onclick="filterLessons('bookmarked')" class="filter-btn" data-filter="bookmarked" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); border-radius: 20px; cursor: pointer; font-size: 11px;">Bookmarked</button>
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="padding: 24px;">
        <!-- Header with Search -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; font-family: monospace; color: var(--text-primary); display: flex; align-items: center; gap: 12px;">
                    <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 32px;">menu_book</span>
                    MY LESSONS
                </h1>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Create, manage, and track your learning journey</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button onclick="toggleSearch()" style="border: 1px solid var(--border); background-color: var(--bg-secondary); color: var(--text-primary); padding: 10px; cursor: pointer; border-radius: 8px;">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <a href="{{ route('lessons.create') }}" style="border: none; background: linear-gradient(135deg, var(--accent) 0%, var(--accent-hover) 100%); color: white; font-family: monospace; font-size: 13px; font-weight: 600; padding: 10px 20px; text-decoration: none; display: flex; align-items: center; gap: 8px; border-radius: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                    NEW LESSON
                </a>
            </div>
        </div>

        <!-- Search Bar (Hidden by default) -->
        <div id="search-bar" style="display: none; margin-bottom: 24px;">
            <div style="display: flex; gap: 12px;">
                <input type="text" id="search-input" placeholder="Search by title, subject, or description..." style="flex: 1; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 12px; border-radius: 8px; font-size: 13px;">
                <button onclick="searchLessons()" style="border: 1px solid var(--accent); background-color: var(--accent); color: white; padding: 12px 24px; cursor: pointer; border-radius: 8px;">Search</button>
                <button onclick="clearSearch()" style="border: 1px solid var(--border); background-color: var(--bg-secondary); color: var(--text-secondary); padding: 12px 24px; cursor: pointer; border-radius: 8px;">Clear</button>
            </div>
        </div>

        @if(session('success'))
            <div style="border: 1px solid var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 14px; margin-bottom: 20px; border-radius: 8px; color: var(--accent-green); display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <div id="lessons-container">
            @if($lessons->isEmpty())
                <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); padding: 80px 40px; text-align: center; border-radius: 12px;">
                    <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 64px;">menu_book</span>
                    <p style="color: var(--text-muted); margin-top: 20px; font-size: 14px;">No lessons yet. Create your first lesson!</p>
                    <a href="{{ route('lessons.create') }}" style="display: inline-block; margin-top: 20px; border: 1px solid var(--accent); background-color: var(--accent); color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px;">Create Lesson</a>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;" id="lessons-grid">
                    @foreach($lessons as $lesson)
                        @php
                            $totalContents = $lesson->contents->count();
                            $completedContents = session("lesson_progress.{$lesson->id}", 0);
                            $progressPercent = $totalContents > 0 ? ($completedContents / $totalContents) * 100 : 0;
                            $isBookmarked = session("bookmarks.{$lesson->id}", false);
                        @endphp
                        <div class="lesson-card" data-lesson-id="{{ $lesson->id }}" data-title="{{ strtolower($lesson->title) }}" data-subject="{{ strtolower($lesson->subject ?? 'general') }}" data-progress="{{ $progressPercent }}" data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}" style="border: 1px solid var(--border); background-color: var(--bg-secondary); overflow: hidden; border-radius: 12px; position: relative;">
                            <!-- Bookmark Badge -->
                            <button onclick="toggleBookmark({{ $lesson->id }}, event)" style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.5); border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; z-index: 10;">
                                <span class="material-symbols-outlined" style="color: {{ $isBookmarked ? 'var(--accent-yellow)' : 'var(--text-muted)' }}; font-size: 20px;">
                                    {{ $isBookmarked ? 'bookmark' : 'bookmark_border' }}
                                </span>
                            </button>

                            <!-- Progress Bar at Top -->
                            <div style="height: 4px; background-color: var(--border);">
                                <div class="progress-bar" style="width: {{ $progressPercent }}%; height: 100%; background: linear-gradient(90deg, var(--accent-green), var(--accent));"></div>
                            </div>

                            <div style="padding: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                    <span style="font-size: 11px; color: var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 4px 10px; border-radius: 12px;">
                                        {{ $lesson->subject ?? 'General' }}
                                    </span>
                                    <span style="font-size: 10px; color: var(--text-muted);">
                                        <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">schedule</span>
                                        {{ $lesson->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <h3 style="font-size: 18px; font-weight: 700; margin: 12px 0 8px; color: var(--text-primary);">{{ $lesson->title }}</h3>
                                <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 16px;">{{ Str::limit($lesson->description, 100) }}</p>

                                <!-- Stats -->
                                <div style="display: flex; gap: 16px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="material-symbols-outlined" style="color: var(--accent); font-size: 16px;">description</span>
                                        <span style="font-size: 11px; color: var(--text-muted);">{{ $totalContents }} sections</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 16px;">check_circle</span>
                                        <span style="font-size: 11px; color: var(--text-muted);">{{ $completedContents }}/{{ $totalContents }} done</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div style="display: flex; gap: 10px;">
                                    <a href="{{ route('lessons.show', $lesson->id) }}" style="flex: 2; border: none; background: linear-gradient(135deg, var(--accent) 0%, var(--accent-hover) 100%); color: white; text-align: center; padding: 10px; text-decoration: none; font-size: 12px; font-weight: 600; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">play_arrow</span>
                                        CONTINUE
                                    </a>
                                    <form action="{{ route('lessons.destroy', $lesson->id) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="width: 100%; border: 1px solid var(--accent-red); background-color: transparent; color: var(--accent-red); padding: 10px; cursor: pointer; font-size: 12px; border-radius: 8px;" onclick="return confirm('Delete this lesson?')">
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
</div>

<script>
    let currentFilter = 'all';
    let currentSearchTerm = '';

    function updateProgressStats() {
        const cards = document.querySelectorAll('.lesson-card');
        let totalProgress = 0;
        let completedCount = 0;
        
        cards.forEach(card => {
            const progress = parseFloat(card.dataset.progress);
            totalProgress += progress;
            if (progress === 100) completedCount++;
        });
        
        const avgProgress = cards.length > 0 ? totalProgress / cards.length : 0;
        document.getElementById('overall-progress').textContent = Math.round(avgProgress) + '%';
        document.getElementById('progress-bar-fill').style.width = avgProgress + '%';
        document.getElementById('completed-count').textContent = `${completedCount} of ${cards.length} lessons completed`;
    }

    function filterLessons(filter) {
        currentFilter = filter;
        
        // Update active button styling
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === filter) {
                btn.classList.add('active');
                btn.style.backgroundColor = 'var(--accent)';
                btn.style.color = 'white';
            } else {
                btn.style.backgroundColor = 'var(--bg-tertiary)';
                btn.style.color = 'var(--text-secondary)';
            }
        });
        
        const cards = document.querySelectorAll('.lesson-card');
        cards.forEach(card => {
            let show = true;
            const progress = parseFloat(card.dataset.progress);
            const isBookmarked = card.dataset.bookmarked === 'true';
            
            if (filter === 'in-progress' && (progress === 100 || progress === 0)) show = false;
            if (filter === 'completed' && progress !== 100) show = false;
            if (filter === 'bookmarked' && !isBookmarked) show = false;
            
            if (show && currentSearchTerm) {
                const title = card.dataset.title;
                if (!title.includes(currentSearchTerm.toLowerCase())) show = false;
            }
            
            card.style.display = show ? 'block' : 'none';
        });
    }

    function searchLessons() {
        currentSearchTerm = document.getElementById('search-input').value;
        filterLessons(currentFilter);
        
        // Highlight search terms
        if (currentSearchTerm) {
            document.querySelectorAll('.lesson-card h3').forEach(title => {
                const text = title.textContent;
                if (text.toLowerCase().includes(currentSearchTerm.toLowerCase())) {
                    title.innerHTML = text.replace(new RegExp(`(${currentSearchTerm})`, 'gi'), '<mark class="search-highlight">$1</mark>');
                }
            });
        }
    }

    function clearSearch() {
        document.getElementById('search-input').value = '';
        currentSearchTerm = '';
        filterLessons(currentFilter);
        
        // Remove highlights
        document.querySelectorAll('.lesson-card h3').forEach(title => {
            title.innerHTML = title.textContent;
        });
    }

    function toggleSearch() {
        const searchBar = document.getElementById('search-bar');
        searchBar.style.display = searchBar.style.display === 'none' ? 'block' : 'none';
        if (searchBar.style.display === 'block') {
            document.getElementById('search-input').focus();
        }
    }

    function toggleBookmark(lessonId, event) {
        event.stopPropagation();
        fetch(`/lms/lessons/${lessonId}/bookmark`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const card = document.querySelector(`.lesson-card[data-lesson-id="${lessonId}"]`);
            const bookmarkIcon = card.querySelector('button span');
            const isBookmarked = data.bookmarked;
            
            card.dataset.bookmarked = isBookmarked;
            bookmarkIcon.textContent = isBookmarked ? 'bookmark' : 'bookmark_border';
            bookmarkIcon.style.color = isBookmarked ? 'var(--accent-yellow)' : 'var(--text-muted)';
            
            if (currentFilter === 'bookmarked') filterLessons('bookmarked');
        });
    }

    // Save progress when viewing content
    function markContentComplete(lessonId, contentIndex) {
        fetch(`/lms/lessons/${lessonId}/progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ content_index: contentIndex })
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateProgressStats();
        
        // Load saved preferences
        const savedFilter = localStorage.getItem('lessonFilter');
        if (savedFilter) filterLessons(savedFilter);
    });
</script>
@endsection