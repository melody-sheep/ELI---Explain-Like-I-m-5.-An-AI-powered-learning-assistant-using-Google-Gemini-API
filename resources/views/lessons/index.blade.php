@extends('layouts.app')

@section('content')
<style>
    .lesson-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .lesson-card:hover {
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
    .filter-btn {
        transition: all 0.2s ease;
        padding: 6px 14px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 11px;
        font-family: 'Consolas', monospace;
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
    }
    .filter-btn:hover {
        background: rgba(34, 197, 94, 0.2) !important;
        transform: scale(1.02);
    }
    .filter-btn.active {
        background: #22c55e !important;
        color: #000000 !important;
        border-color: #22c55e !important;
    }
    .progress-bar-fill {
        transition: width 0.5s ease;
    }
    
    /* Fixed: Card menu position */
    .card-menu {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
    }
    
    /* Fixed: Bookmark position */
/* Bookmark button - NO gray background */
    .bookmark-btn {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 10;
        background: transparent !important;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bookmark-btn button {
        background: transparent !important;
        border: none !important;
        cursor: pointer;
        padding: 4px;
        transition: all 0.2s ease;
    }
    .bookmark-btn button:hover {
        transform: scale(1.1);
    }
    .bookmark-btn button:active {
        transform: scale(0.95);
    }
    
    .card-menu-dropdown {
        position: absolute;
        top: 30px;
        right: 0;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        min-width: 140px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        display: none;
        z-index: 20;
    }
    .card-menu-dropdown.show {
        display: block;
    }
    .card-menu-dropdown a, .card-menu-dropdown button {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        color: var(--text-primary);
        font-size: 12px;
        font-family: 'Consolas', monospace;
        cursor: pointer;
        transition: background 0.2s;
    }
    .card-menu-dropdown a:hover, .card-menu-dropdown button:hover {
        background: var(--hover-bg);
    }
    .card-menu-dropdown .delete-btn {
        color: #ef4444;
    }
    
    /* NEW BUTTON DESIGN - Green stroke with light background */
    .continue-btn {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
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
    .continue-btn:hover {
        background: rgba(34, 197, 94, 0.2) !important;
        transform: scale(1.02);
    }
    .continue-btn:active {
        transform: scale(0.98);
    }
    
    /* New lesson button */
    .new-lesson-btn {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
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
    .new-lesson-btn:hover {
        background: rgba(34, 197, 94, 0.2) !important;
        transform: scale(1.02);
    }
    .new-lesson-btn:active {
        transform: scale(0.98);
    }
    
    .sort-select {
        background-color: var(--bg-tertiary);
        border: 1px solid var(--border);
        color: var(--text-primary);
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-family: 'Consolas', monospace;
        cursor: pointer;
    }
    .sort-select option {
        background-color: var(--bg-secondary);
    }
    
    /* Light mode specific */
    body.light-mode .filter-btn {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
    }
    body.light-mode .filter-btn.active {
        background: #22c55e !important;
        color: #000000 !important;
    }
    body.light-mode .continue-btn,
    body.light-mode .new-lesson-btn {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="/dashboard" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
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
                <span class="material-symbols-outlined" style="color: #22c55e; font-size: 32px;">menu_book</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted); font-family: 'Consolas', monospace;">LESSON STATS</p>
                    <p style="font-size: 24px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;" id="total-lessons">{{ $totalLessons }}</p>
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-size: 10px; color: var(--text-muted); font-family: 'Consolas', monospace;">Overall Progress</span>
                    <span style="font-size: 10px; color: #22c55e; font-family: 'Consolas', monospace;" id="overall-progress">{{ $overallProgress }}%</span>
                </div>
                <div style="height: 4px; background-color: var(--border); border-radius: 2px; overflow: hidden;">
                    <div id="progress-bar-fill" style="height: 100%; background: #22c55e; width: {{ $overallProgress }}%; border-radius: 2px;"></div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <p style="font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;" id="completed-count-display">{{ $completedLessons }}</p>
                    <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">COMPLETED</p>
                </div>
                <div>
                    <p style="font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;" id="inprogress-count-display">{{ $inProgressLessons }}</p>
                    <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">IN PROGRESS</p>
                </div>
                <div>
                    <p style="font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;" id="bookmarked-count-display">{{ $bookmarkedCount }}</p>
                    <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">BOOKMARKED</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 12px; font-family: 'Consolas', monospace; letter-spacing: 0.5px;">QUICK FILTERS</p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <button onclick="filterLessons('all')" class="filter-btn active" data-filter="all">All</button>
                <button onclick="filterLessons('in-progress')" class="filter-btn" data-filter="in-progress">In Progress</button>
                <button onclick="filterLessons('completed')" class="filter-btn" data-filter="completed">Completed</button>
                <button onclick="filterLessons('bookmarked')" class="filter-btn" data-filter="bookmarked">Bookmarked</button>
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px; color: var(--text-primary); font-family: 'Consolas', monospace;">
                    <span class="material-symbols-outlined" style="color: #22c55e; font-size: 32px;">menu_book</span>
                    MY LESSONS
                </h1>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; font-family: 'Consolas', monospace;">Create, manage, and track your learning journey</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <select id="sort-select" class="sort-select" onchange="sortLessons()">
                    <option value="date-desc">Latest First</option>
                    <option value="date-asc">Oldest First</option>
                    <option value="title-asc">Title A-Z</option>
                    <option value="title-desc">Title Z-A</option>
                    <option value="progress-desc">Highest Progress</option>
                    <option value="progress-asc">Lowest Progress</option>
                </select>
                <a href="{{ route('lessons.create') }}" class="new-lesson-btn">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                    NEW LESSON
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="border: 1px solid #22c55e; background-color: rgba(34, 197, 94, 0.1); padding: 12px; margin-bottom: 20px; border-radius: 8px; color: #22c55e; display: flex; align-items: center; gap: 10px; font-family: 'Consolas', monospace; font-size: 12px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if($lessons->isEmpty())
            <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); padding: 80px 40px; text-align: center; border-radius: 12px;">
                <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 64px;">menu_book</span>
                <p style="color: var(--text-muted); margin-top: 20px; font-size: 14px; font-family: 'Consolas', monospace;">No lessons yet. Create your first lesson!</p>
                <a href="{{ route('lessons.create') }}" class="new-lesson-btn" style="display: inline-block; margin-top: 20px;">CREATE LESSON</a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;" id="lessons-grid">
                @foreach($lessons as $lesson)
                    <div class="lesson-card" data-lesson-id="{{ $lesson->id }}" data-title="{{ strtolower($lesson->title) }}" data-progress="{{ $lesson->progress_percent }}" data-bookmarked="{{ $lesson->is_bookmarked ? 'true' : 'false' }}" data-date="{{ $lesson->created_at->timestamp }}" style="border: 1px solid var(--border); background-color: var(--bg-secondary); border-radius: 16px; overflow: hidden;">
                        <div style="height: 4px; background: #22c55e; width: {{ $lesson->progress_percent }}%;"></div>
                        
                        <!-- Bookmark Button (Top Left) - No gray background -->
                        <div class="bookmark-btn">
                            <button onclick="toggleBookmark({{ $lesson->id }}, event)">
                                <span class="material-symbols-outlined" style="color: {{ $lesson->is_bookmarked ? '#eab308' : 'var(--text-muted)' }}; font-size: 22px;">
                                    {{ $lesson->is_bookmarked ? 'bookmark' : 'bookmark_border' }}
                                </span>
                            </button>
                        </div>
                        
                        <!-- Card Menu (Three Dots - Top Right) -->
                        <div class="card-menu">
                            <button onclick="toggleCardMenu(event, {{ $lesson->id }})" style="background: none; border: none; cursor: pointer; padding: 4px;">
                                <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 20px;">more_vert</span>
                            </button>
                            <div id="menu-{{ $lesson->id }}" class="card-menu-dropdown">
                                <a href="{{ route('lessons.show', $lesson->id) }}">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                    View Lesson
                                </a>
                                <button onclick="openEditModal({{ $lesson->id }}, '{{ addslashes($lesson->title) }}', '{{ addslashes($lesson->description ?? '') }}', '{{ addslashes($lesson->subject ?? '') }}')">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                                    Edit
                                </button>
                                <button onclick="renameLesson({{ $lesson->id }}, '{{ addslashes($lesson->title) }}')">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">drive_file_rename_outline</span>
                                    Rename
                                </button>
                                <form action="{{ route('lessons.destroy', $lesson->id) }}" method="POST" onsubmit="return confirm('Delete this lesson?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                        <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div style="padding: 20px; padding-top: 50px;">
                            <div style="margin: 12px 0 8px;">
                                <span style="font-size: 10px; color: #22c55e; background-color: rgba(34, 197, 94, 0.1); padding: 4px 10px; border-radius: 12px; font-family: 'Consolas', monospace;">
                                    {{ $lesson->subject ?? 'General' }}
                                </span>
                            </div>
                            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; font-family: 'Consolas', monospace;" id="title-{{ $lesson->id }}">{{ $lesson->title }}</h3>
                            <p style="font-size: 11px; color: var(--text-secondary); margin-bottom: 16px; font-family: 'Consolas', monospace; line-height: 1.4;">{{ Str::limit($lesson->description, 80) }}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span style="font-size: 10px; color: var(--text-muted); font-family: 'Consolas', monospace;">
                                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">description</span>
                                    {{ $lesson->total_contents }} sections
                                </span>
                                <span style="font-size: 10px; color: #22c55e; font-family: 'Consolas', monospace;">
                                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">check_circle</span>
                                    {{ $lesson->completed_count }}/{{ $lesson->total_contents }} done
                                </span>
                            </div>
                            <div style="height: 4px; background-color: var(--border); border-radius: 2px; margin-bottom: 16px;">
                                <div style="width: {{ $lesson->progress_percent }}%; height: 100%; background: #22c55e; border-radius: 2px;"></div>
                            </div>
                            <a href="{{ route('lessons.show', $lesson->id) }}" class="continue-btn">
                                CONTINUE
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Edit Lesson Modal -->
<div id="edit-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-secondary); border-radius: 16px; padding: 24px; max-width: 450px; width: 90%; border: 1px solid var(--border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">Edit Lesson</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="edit-form" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">TITLE</label>
                <input type="text" name="title" id="edit-title" required style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px; font-family: 'Consolas', monospace;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">SUBJECT</label>
                <input type="text" name="subject" id="edit-subject" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px; font-family: 'Consolas', monospace;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">DESCRIPTION</label>
                <textarea name="description" id="edit-description" rows="4" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px; font-family: 'Consolas', monospace;"></textarea>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; background: rgba(34, 197, 94, 0.1); border: 1.5px solid #22c55e; color: #22c55e; padding: 10px; border-radius: 8px; font-family: 'Consolas', monospace; font-size: 11px; font-weight: 600; cursor: pointer;">SAVE CHANGES</button>
                <button type="button" onclick="closeEditModal()" style="flex: 1; background: rgba(100, 100, 100, 0.1); border: 1.5px solid var(--text-muted); color: var(--text-muted); padding: 10px; border-radius: 8px; font-family: 'Consolas', monospace; font-size: 11px; cursor: pointer;">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<!-- Rename Modal -->
<div id="rename-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-secondary); border-radius: 16px; padding: 24px; max-width: 350px; width: 90%; border: 1px solid var(--border);">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; font-family: 'Consolas', monospace;">Rename Lesson</h3>
        <input type="text" id="rename-input" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px; font-family: 'Consolas', monospace; margin-bottom: 16px;">
        <div style="display: flex; gap: 12px;">
            <button onclick="confirmRename()" style="flex: 1; background: rgba(34, 197, 94, 0.1); border: 1.5px solid #22c55e; color: #22c55e; padding: 10px; border-radius: 8px; font-family: 'Consolas', monospace; font-size: 11px; font-weight: 600; cursor: pointer;">RENAME</button>
            <button onclick="closeRenameModal()" style="flex: 1; background: rgba(100, 100, 100, 0.1); border: 1.5px solid var(--text-muted); color: var(--text-muted); padding: 10px; border-radius: 8px; font-family: 'Consolas', monospace; font-size: 11px; cursor: pointer;">CANCEL</button>
        </div>
    </div>
</div>

<script>
    let currentFilter = 'all';
    let renameLessonId = null;
    let renameCurrentTitle = '';
    
    function updateThemeIcon() {
        const icon = document.getElementById('theme-icon');
        if (icon) {
            const isLight = document.body.classList.contains('light-mode');
            icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        }
    }
    
    function toggleCardMenu(event, lessonId) {
        event.stopPropagation();
        document.querySelectorAll('.card-menu-dropdown').forEach(menu => {
            if (menu.id !== `menu-${lessonId}`) {
                menu.classList.remove('show');
            }
        });
        const menu = document.getElementById(`menu-${lessonId}`);
        menu.classList.toggle('show');
    }
    
    document.addEventListener('click', function() {
        document.querySelectorAll('.card-menu-dropdown').forEach(menu => {
            menu.classList.remove('show');
        });
    });
    
    function filterLessons(filter) {
        currentFilter = filter;
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === filter) {
                btn.classList.add('active');
            }
        });
        
        applyFiltersAndSort();
    }
    
    function sortLessons() {
        applyFiltersAndSort();
    }
    
    function applyFiltersAndSort() {
        const cards = Array.from(document.querySelectorAll('.lesson-card'));
        const sortValue = document.getElementById('sort-select').value;
        
        let visibleCards = cards.filter(card => {
            let show = true;
            const progress = parseFloat(card.dataset.progress);
            const isBookmarked = card.dataset.bookmarked === 'true';
            
            if (currentFilter === 'in-progress' && (progress === 100 || progress === 0)) show = false;
            if (currentFilter === 'completed' && progress !== 100) show = false;
            if (currentFilter === 'bookmarked' && !isBookmarked) show = false;
            
            return show;
        });
        
        visibleCards.sort((a, b) => {
            switch(sortValue) {
                case 'date-desc': return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                case 'date-asc': return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                case 'title-asc': return a.dataset.title.localeCompare(b.dataset.title);
                case 'title-desc': return b.dataset.title.localeCompare(a.dataset.title);
                case 'progress-desc': return parseFloat(b.dataset.progress) - parseFloat(a.dataset.progress);
                case 'progress-asc': return parseFloat(a.dataset.progress) - parseFloat(b.dataset.progress);
                default: return 0;
            }
        });
        
        const grid = document.getElementById('lessons-grid');
        visibleCards.forEach(card => grid.appendChild(card));
        
        let totalProgress = 0;
        let completedCount = 0;
        let inProgressCount = 0;
        let bookmarkedCount = 0;
        
        visibleCards.forEach(card => {
            const progress = parseFloat(card.dataset.progress);
            const isBookmarked = card.dataset.bookmarked === 'true';
            totalProgress += progress;
            if (progress === 100) completedCount++;
            if (progress > 0 && progress < 100) inProgressCount++;
            if (isBookmarked) bookmarkedCount++;
        });
        
        const avgProgress = visibleCards.length > 0 ? Math.round(totalProgress / visibleCards.length) : 0;
        document.getElementById('overall-progress').textContent = avgProgress + '%';
        document.getElementById('progress-bar-fill').style.width = avgProgress + '%';
        document.getElementById('total-lessons').textContent = visibleCards.length;
        document.getElementById('completed-count-display').textContent = completedCount;
        document.getElementById('inprogress-count-display').textContent = inProgressCount;
        document.getElementById('bookmarked-count-display').textContent = bookmarkedCount;
    }
    
    function toggleBookmark(lessonId, event) {
        event.stopPropagation();
        
        fetch(`/lms/lessons/${lessonId}/bookmark`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const card = document.querySelector(`.lesson-card[data-lesson-id="${lessonId}"]`);
            const bookmarkIcon = card.querySelector('.bookmark-btn button span');
            
            card.dataset.bookmarked = data.bookmarked;
            if (bookmarkIcon) {
                bookmarkIcon.textContent = data.bookmarked ? 'bookmark' : 'bookmark_border';
                bookmarkIcon.style.color = data.bookmarked ? '#eab308' : 'var(--text-muted)';
            }
            
            if (currentFilter === 'bookmarked') {
                applyFiltersAndSort();
            }
        });
    }
    
    function openEditModal(id, title, description, subject) {
        document.getElementById('edit-form').action = `/lms/lessons/${id}`;
        document.getElementById('edit-title').value = title;
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-subject').value = subject;
        document.getElementById('edit-modal').style.display = 'flex';
    }
    
    function closeEditModal() {
        document.getElementById('edit-modal').style.display = 'none';
    }
    
    function renameLesson(id, currentTitle) {
        renameLessonId = id;
        renameCurrentTitle = currentTitle;
        document.getElementById('rename-input').value = currentTitle;
        document.getElementById('rename-modal').style.display = 'flex';
    }
    
    function closeRenameModal() {
        document.getElementById('rename-modal').style.display = 'none';
        renameLessonId = null;
    }
    
    function confirmRename() {
        const newTitle = document.getElementById('rename-input').value;
        if (!newTitle.trim()) return;
        
        fetch(`/lms/lessons/${renameLessonId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ title: newTitle })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`title-${renameLessonId}`).textContent = newTitle;
                const card = document.querySelector(`.lesson-card[data-lesson-id="${renameLessonId}"]`);
                card.dataset.title = newTitle.toLowerCase();
                closeRenameModal();
                showToast('Lesson renamed!', 'success');
                applyFiltersAndSort();
            }
        });
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.style.cssText = `position: fixed; bottom: 30px; right: 30px; padding: 10px 20px; border-radius: 40px; font-size: 12px; font-weight: 600; font-family: 'Consolas', monospace; z-index: 3000; background: rgba(34, 197, 94, 0.95); border: 1px solid #22c55e; color: #000000; animation: slideInRight 0.3s ease;`;
        if (type === 'error') {
            toast.style.background = 'rgba(239, 68, 68, 0.95)';
            toast.style.border = '1px solid #ef4444';
            toast.style.color = '#ffffff';
        }
        toast.innerHTML = `<span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">${type === 'success' ? 'check_circle' : 'error'}</span> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }
    
    const observer = new MutationObserver(() => updateThemeIcon());
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    
    updateThemeIcon();
</script>
@endsection