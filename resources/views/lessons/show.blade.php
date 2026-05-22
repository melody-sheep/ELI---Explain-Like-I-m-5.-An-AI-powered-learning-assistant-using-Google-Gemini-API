@extends('layouts.app')

@section('content')
<style>
    .content-item {
        transition: all 0.2s ease;
        cursor: pointer;
        border-radius: 12px;
    }
    .content-item:hover {
        transform: translateX(4px);
        border-color: var(--accent-green);
    }
    .content-item.completed {
        opacity: 0.7;
    }
    .theme-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .theme-btn:hover {
        transform: rotate(15deg);
        border-color: var(--accent) !important;
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
    
    /* NEW BUTTON DESIGN - Green with stroke and light background */
    .btn-primary {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: rgba(34, 197, 94, 0.2) !important;
        transform: scale(1.02);
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
    
    .btn-success {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-success:hover {
        background: rgba(34, 197, 94, 0.2) !important;
        transform: scale(1.02);
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
    
    /* ADD SECTION button - same green stroke design */
    .add-section-btn {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 10px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .add-section-btn:hover {
        background: rgba(34, 197, 94, 0.2) !important;
        transform: scale(1.02);
    }
    
    /* Empty state button - centered properly */
    .empty-state-btn {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0 auto;
    }
    .empty-state-btn:hover {
        background: rgba(34, 197, 94, 0.2) !important;
        transform: scale(1.02);
    }
    
    /* Light mode - same design works */
    body.light-mode .btn-primary,
    body.light-mode .btn-success,
    body.light-mode .add-section-btn,
    body.light-mode .empty-state-btn {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1.5px solid #22c55e !important;
        color: #22c55e !important;
    }
    
    body.light-mode .btn-secondary {
        background: rgba(100, 100, 100, 0.1) !important;
        border: 1.5px solid #666666 !important;
        color: #666666 !important;
    }
    
    .content-type-select {
        background-color: var(--bg-tertiary);
        border: 1px solid var(--border);
        color: var(--text-primary);
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        font-family: 'Consolas', monospace;
        width: 100%;
        cursor: pointer;
    }
    .content-type-select option {
        background-color: var(--bg-secondary);
        padding: 8px;
    }
    
    /* Center empty state */
    .empty-state-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <a href="{{ route('lessons.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
                <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
                </div>
                <div>
                    <span style="font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">BACK</span>
                    <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted); font-family: 'Consolas', monospace;">TO LESSONS</p>
                </div>
            </a>
            <button onclick="toggleTheme()" class="theme-btn" style="width: 36px; height: 36px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); cursor: pointer; border-radius: 8px;">
                <span id="theme-icon" class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 20px;">dark_mode</span>
            </button>
        </div>
    </div>

    <div style="padding: 20px;">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 10px; color: var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 4px 10px; border-radius: 12px; font-family: 'Consolas', monospace;">
                {{ $lesson->subject ?? 'General' }}
            </span>
            <h2 style="font-size: 18px; font-weight: 700; margin-top: 16px; color: var(--text-primary); font-family: 'Consolas', monospace;">{{ $lesson->title }}</h2>
            <p style="font-size: 11px; color: var(--text-secondary); margin-top: 8px; line-height: 1.5; font-family: 'Consolas', monospace;">{{ Str::limit($lesson->description, 120) }}</p>
        </div>

        <div style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 10px; color: var(--text-muted); font-family: 'Consolas', monospace;">Lesson Progress</span>
                <span style="font-size: 10px; color: var(--accent-green); font-family: 'Consolas', monospace;" id="progress-percent">{{ $progressPercent }}%</span>
            </div>
            <div style="height: 4px; background-color: var(--border); border-radius: 2px; overflow: hidden;">
                <div id="progress-fill" style="height: 100%; background: linear-gradient(90deg, var(--accent-green), var(--accent)); width: {{ $progressPercent }}%; border-radius: 2px;"></div>
            </div>
            <p style="font-size: 9px; color: var(--text-muted); margin-top: 8px; font-family: 'Consolas', monospace;" id="progress-text">{{ $completedContents }} of {{ $totalContents }} sections completed</p>
        </div>

        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <p style="font-size: 10px; font-weight: 600; color: var(--text-muted); font-family: 'Consolas', monospace; letter-spacing: 0.5px;">CONTENTS</p>
                <button onclick="openAddContent()" class="add-section-btn">
                    <span class="material-symbols-outlined" style="font-size: 14px;">add</span>
                    ADD SECTION
                </button>
            </div>
            <div id="contents-list" style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($lesson->contents as $index => $content)
                    <div class="content-item {{ $content->is_completed ? 'completed' : '' }}" data-content-id="{{ $content->id }}" data-index="{{ $index }}" onclick="scrollToContent({{ $index }})" style="border: 1px solid var(--border); padding: 12px; background-color: var(--bg-tertiary); cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-symbols-outlined" style="color: {{ $content->is_completed ? 'var(--accent-green)' : 'var(--text-muted)' }}; font-size: 18px;">
                                {{ $content->is_completed ? 'check_circle' : 'radio_button_unchecked' }}
                            </span>
                            <span class="material-symbols-outlined" style="color: var(--accent); font-size: 18px;">
                                @if($content->content_type == 'text') description
                                @elseif($content->content_type == 'video') smart_display
                                @elseif($content->content_type == 'file') insert_drive_file
                                @else link
                                @endif
                            </span>
                            <div style="flex: 1;">
                                <p style="font-size: 12px; font-weight: 500; color: var(--text-primary); font-family: 'Consolas', monospace;">{{ $content->title }}</p>
                                <p style="font-size: 8px; color: var(--text-muted); text-transform: uppercase; font-family: 'Consolas', monospace;">{{ $content->content_type }}</p>
                            </div>
                            <button onclick="event.stopPropagation(); deleteContent({{ $lesson->id }}, {{ $content->id }})" style="background: none; border: none; cursor: pointer;">
                                <span class="material-symbols-outlined" style="color: var(--accent-red); font-size: 18px;">delete</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 800px; margin: 0 auto; padding: 24px; width: 100%;">
        @if(session('success'))
            <div style="border: 1px solid var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 12px; margin-bottom: 20px; border-radius: 8px; color: var(--accent-green); display: flex; align-items: center; gap: 10px; font-family: 'Consolas', monospace; font-size: 12px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="border: 1px solid var(--accent-red); background-color: rgba(239, 68, 68, 0.1); padding: 12px; margin-bottom: 20px; border-radius: 8px; color: var(--accent-red); display: flex; align-items: center; gap: 10px; font-family: 'Consolas', monospace; font-size: 12px;">
                <span class="material-symbols-outlined">error</span>
                {{ session('error') }}
            </div>
        @endif

        @if($lesson->contents->isEmpty())
            <div class="empty-state-container" style="text-align: center; padding: 80px 40px; border: 1px dashed var(--border); border-radius: 16px;">
                <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 64px; margin-bottom: 20px;">add_circle</span>
                <p style="color: var(--text-muted); margin-bottom: 24px; font-size: 14px; font-family: 'Consolas', monospace;">No content yet. Add your first section!</p>
                <button onclick="openAddContent()" class="empty-state-btn">
                    <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
                    ADD CONTENT
                </button>
            </div>
        @else
            @foreach($lesson->contents as $index => $content)
                <div id="content-{{ $index }}" class="content-display" style="margin-bottom: 48px; scroll-margin-top: 80px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--border);">
                        <span class="material-symbols-outlined" style="color: var(--accent); font-size: 28px;">
                            @if($content->content_type == 'text') description
                            @elseif($content->content_type == 'video') smart_display
                            @elseif($content->content_type == 'file') insert_drive_file
                            @else link
                            @endif
                        </span>
                        <div style="flex: 1;">
                            <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">{{ $content->title }}</h2>
                            <p style="font-size: 10px; color: var(--text-muted); font-family: 'Consolas', monospace;">Section {{ $index + 1 }} of {{ count($lesson->contents) }}</p>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="toggleComplete({{ $content->id }}, {{ $index }})" style="background: none; border: none; cursor: pointer;">
                                <span class="material-symbols-outlined" style="color: {{ $content->is_completed ? '#22c55e' : 'var(--text-muted)' }}; font-size: 28px;">
                                    {{ $content->is_completed ? 'check_circle' : 'radio_button_unchecked' }}
                                </span>
                            </button>
                            <button onclick="deleteContent({{ $lesson->id }}, {{ $content->id }})" style="background: none; border: none; cursor: pointer;">
                                <span class="material-symbols-outlined" style="color: #ef4444; font-size: 20px;">delete</span>
                            </button>
                        </div>
                    </div>

                    @if($content->content_type == 'text')
                        <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.8; white-space: pre-wrap; font-family: 'Georgia', serif;">
                            {{ $content->content }}
                        </div>
                    @endif

                    @if($content->content_type == 'video')
                        @php
                            $videoUrl = $content->content;
                            $embedUrl = $videoUrl;
                            if (strpos($videoUrl, 'youtube.com/watch?v=') !== false) {
                                $videoId = substr($videoUrl, strpos($videoUrl, 'v=') + 2);
                                $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                            } elseif (strpos($videoUrl, 'youtu.be/') !== false) {
                                $videoId = substr($videoUrl, strrpos($videoUrl, '/') + 1);
                                $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                            }
                        @endphp
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
                            <iframe src="{{ $embedUrl }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                        </div>
                    @endif

                    @if($content->content_type == 'file')
                        @php
                            $fileName = basename($content->content);
                            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isPdf = $fileExt === 'pdf';
                            $fileUrl = asset('storage/' . $content->content);
                        @endphp
                        <div style="border: 1px solid var(--border); background-color: var(--bg-tertiary); border-radius: 12px; overflow: hidden;">
                            <div style="padding: 20px;">
                                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                                    <span class="material-symbols-outlined" style="color: #22c55e; font-size: 48px;">picture_as_pdf</span>
                                    <div style="flex: 1;">
                                        <p style="font-size: 14px; font-weight: 600; color: var(--text-primary); font-family: 'Consolas', monospace;">{{ $fileName }}</p>
                                        <p style="font-size: 10px; color: var(--text-muted); font-family: 'Consolas', monospace;">{{ strtoupper($fileExt) }} file</p>
                                    </div>
                                    <div style="display: flex; gap: 8px;">
                                        @if($isPdf)
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn-primary" style="padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 11px;">
                                                <span class="material-symbols-outlined" style="font-size: 16px;">visibility</span>
                                                VIEW
                                            </a>
                                        @endif
                                        <a href="{{ $fileUrl }}" download class="btn-secondary" style="padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 11px;">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">download</span>
                                            DOWNLOAD
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div style="border-top: 1px solid var(--border); padding: 12px 20px; background-color: var(--bg-secondary);">
                                <textarea id="file-note-{{ $content->id }}" placeholder="Add notes about this file..." style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 8px; border-radius: 8px; font-size: 10px; font-family: 'Consolas', monospace; resize: vertical; min-height: 50px;" onblur="saveFileNote({{ $content->id }})">{{ Session::get('file_note_' . (Auth::id() ?? session('guest_id')) . '_' . $content->id, '') }}</textarea>
                            </div>
                        </div>
                    @endif

                    @if($content->content_type == 'link')
                        <div style="border: 1px solid var(--border); background-color: var(--bg-tertiary); padding: 24px; border-radius: 12px; text-align: center;">
                            <span class="material-symbols-outlined" style="color: #eab308; font-size: 48px;">link</span>
                            <p style="font-size: 13px; color: var(--text-secondary); margin: 16px 0; font-family: 'Consolas', monospace;">External Resource Link</p>
                            <a href="{{ $content->content }}" target="_blank" class="btn-primary" style="padding: 10px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 11px;">
                                <span class="material-symbols-outlined" style="font-size: 16px;">open_in_new</span>
                                Visit Website
                            </a>
                        </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border);">
                        @if($index > 0)
                            <button onclick="scrollToContent({{ $index - 1 }})" class="btn-secondary" style="padding: 8px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; font-size: 11px;">
                                <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
                                PREVIOUS
                            </button>
                        @else
                            <div></div>
                        @endif
                        
                        @if($index < count($lesson->contents) - 1)
                            <button onclick="scrollToContent({{ $index + 1 }})" class="btn-primary" style="padding: 8px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; font-size: 11px;">
                                NEXT
                                <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
                            </button>
                        @else
                            <button onclick="checkAndShowCompletion()" class="btn-success" style="padding: 8px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; font-size: 11px;">
                                <span class="material-symbols-outlined" style="font-size: 16px;">celebration</span>
                                COMPLETE
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Add Content Modal -->
<div id="add-content-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-secondary); border-radius: 16px; padding: 24px; max-width: 500px; width: 90%; border: 1px solid var(--border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">Add New Section</h3>
            <button onclick="closeAddContent()" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('lessons.add-content', $lesson->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">TITLE</label>
                <input type="text" name="title" required style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px; font-family: 'Consolas', monospace;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">CONTENT TYPE</label>
                <select name="content_type" id="content-type-select" class="content-type-select">
                    <option value="text">📝 Text</option>
                    <option value="video">🎬 Video</option>
                    <option value="file">📎 File</option>
                    <option value="link">🔗 Link</option>
                </select>
            </div>
            <div id="text-input-area" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">CONTENT</label>
                <textarea name="content" rows="6" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px; font-family: 'Consolas', monospace;"></textarea>
            </div>
            <div id="file-input-area" style="display: none; margin-bottom: 16px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">FILE</label>
                <input type="file" name="file" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px;">
            </div>
            <div id="link-input-area" style="display: none; margin-bottom: 16px;">
                <label style="display: block; font-size: 10px; font-weight: 600; margin-bottom: 6px; color: var(--text-secondary); font-family: 'Consolas', monospace;">URL</label>
                <input type="url" name="content" placeholder="https://..." style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px; font-size: 12px;">
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn-success" style="flex: 1; padding: 10px; border-radius: 8px; font-size: 11px; font-weight: 600;">ADD SECTION</button>
                <button type="button" onclick="closeAddContent()" class="btn-secondary" style="flex: 1; padding: 10px; border-radius: 8px; font-size: 11px;">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<script>
    let completedContents = {};
    let totalContents = {{ $totalContents }};
    let lessonId = {{ $lesson->id }};
    
    @foreach($lesson->contents as $content)
        completedContents[{{ $content->id }}] = {{ $content->is_completed ? 'true' : 'false' }};
    @endforeach
    
    function updateThemeIcon() {
        const icon = document.getElementById('theme-icon');
        if (icon) {
            const isLight = document.body.classList.contains('light-mode');
            icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        }
    }
    
    function updateProgress() {
        let completedCount = Object.values(completedContents).filter(v => v === true).length;
        let percent = totalContents > 0 ? Math.round((completedCount / totalContents) * 100) : 0;
        
        let progressFill = document.getElementById('progress-fill');
        let progressPercent = document.getElementById('progress-percent');
        let progressText = document.getElementById('progress-text');
        
        if (progressFill) progressFill.style.width = percent + '%';
        if (progressPercent) progressPercent.textContent = percent + '%';
        if (progressText) progressText.textContent = completedCount + ' of ' + totalContents + ' sections completed';
        
        document.querySelectorAll('.content-item').forEach((item) => {
            const contentId = parseInt(item.dataset.contentId);
            const iconSpan = item.querySelector('.material-symbols-outlined:first-child');
            if (completedContents[contentId]) {
                if (iconSpan) {
                    iconSpan.textContent = 'check_circle';
                    iconSpan.style.color = '#22c55e';
                }
                item.classList.add('completed');
            } else {
                if (iconSpan) {
                    iconSpan.textContent = 'radio_button_unchecked';
                    iconSpan.style.color = 'var(--text-muted)';
                }
                item.classList.remove('completed');
            }
        });
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        
        if (type === 'success') {
            toast.style.backgroundColor = 'rgba(34, 197, 94, 0.95)';
            toast.style.color = '#000000';
            toast.style.border = '1px solid #22c55e';
        } else if (type === 'error') {
            toast.style.backgroundColor = 'rgba(239, 68, 68, 0.95)';
            toast.style.color = '#ffffff';
            toast.style.border = '1px solid #ef4444';
        } else if (type === 'info') {
            toast.style.backgroundColor = 'rgba(59, 130, 246, 0.95)';
            toast.style.color = '#ffffff';
            toast.style.border = '1px solid #3b82f6';
        }
        
        toast.innerHTML = '<span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">' + 
            (type === 'success' ? 'check_circle' : (type === 'error' ? 'error' : 'info')) + 
            '</span> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }

    
    function toggleComplete(contentId, index) {
        const newState = !completedContents[contentId];
        
        fetch('/lms/lessons/' + lessonId + '/content/' + contentId + '/complete', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ completed: newState })
        })
        .then(response => response.json())
        .then(data => {
            completedContents[contentId] = newState;
            updateProgress();
            
            const btn = document.querySelector('#content-' + index + ' button span');
            if (btn) {
                btn.textContent = newState ? 'check_circle' : 'radio_button_unchecked';
                btn.style.color = newState ? '#22c55e' : 'var(--text-muted)';
            }
            
            showToast(newState ? 'Section completed! 🎉' : 'Section marked as incomplete');
            
            if (data.is_complete) {
                showCompletionModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating progress', 'error');
        });
    }
    
    function showCompletionModal() {
        const completedCount = Object.values(completedContents).filter(v => v === true).length;
        const percent = Math.round((completedCount / totalContents) * 100);
        
        const modalHtml = `
            <div class="modal-overlay" onclick="closeModal()">
                <div class="completion-modal" onclick="event.stopPropagation()">
                    <span class="material-symbols-outlined" style="color: #22c55e; font-size: 56px;">celebration</span>
                    <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 12px 0 6px; font-family: 'Consolas', monospace;">LESSON COMPLETE!</h2>
                    <p style="color: var(--text-muted); margin-bottom: 20px; font-size: 11px; font-family: 'Consolas', monospace;">Great job mastering this lesson!</p>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px;">
                        <div style="text-align: center; padding: 10px; background: var(--bg-tertiary); border-radius: 10px;">
                            <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">menu_book</span>
                            <p style="font-size: 20px; font-weight: 700; color: var(--text-primary); font-family: 'Consolas', monospace;">${totalContents}</p>
                            <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">TOTAL SECTIONS</p>
                        </div>
                        <div style="text-align: center; padding: 10px; background: var(--bg-tertiary); border-radius: 10px;">
                            <span class="material-symbols-outlined" style="color: #22c55e; font-size: 22px;">check_circle</span>
                            <p style="font-size: 20px; font-weight: 700; color: #22c55e; font-family: 'Consolas', monospace;">${completedCount}</p>
                            <p style="font-size: 9px; color: var(--text-muted); font-family: 'Consolas', monospace;">COMPLETED</p>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px; padding: 12px; background: rgba(34, 197, 94, 0.1); border: 1.5px solid #22c55e; border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #22c55e; font-size: 11px; font-family: 'Consolas', monospace;">PROGRESS</span>
                            <span style="font-size: 22px; font-weight: bold; color: #22c55e; font-family: 'Consolas', monospace;">${percent}%</span>
                        </div>
                        <div style="height: 4px; background: rgba(34, 197, 94, 0.2); border-radius: 2px; margin-top: 8px;">
                            <div style="width: ${percent}%; height: 100%; background: #22c55e; border-radius: 2px;"></div>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 12px;">
                        <a href="{{ route('lessons.index') }}" class="btn-primary" style="flex: 1; padding: 10px; border-radius: 8px; font-size: 11px; text-align: center; text-decoration: none;">
                            BACK TO LESSONS
                        </a>
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
    
    function checkAndShowCompletion() {
        const completedCount = Object.values(completedContents).filter(v => v === true).length;
        if (completedCount === totalContents && totalContents > 0) {
            showCompletionModal();
        } else {
            showToast('Complete ' + (totalContents - completedCount) + ' more section(s) to finish!', 'error');
        }
    }
    
    function scrollToContent(index) {
        const element = document.getElementById('content-' + index);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        document.querySelectorAll('.content-item').forEach((item, i) => {
            if (i === index) {
                item.style.borderColor = '#22c55e';
                item.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
            } else {
                item.style.borderColor = 'var(--border)';
                item.style.backgroundColor = 'var(--bg-tertiary)';
            }
        });
        
        setTimeout(() => {
            document.querySelectorAll('.content-item').forEach(item => {
                item.style.borderColor = 'var(--border)';
                item.style.backgroundColor = 'var(--bg-tertiary)';
            });
        }, 2000);
    }
    
    function openAddContent() {
        const modal = document.getElementById('add-content-modal');
        if (modal) {
            modal.style.display = 'flex';
        } else {
            console.error('Modal not found');
            showToast('Error opening modal', 'error');
        }
    }
    
    function closeAddContent() {
        const modal = document.getElementById('add-content-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    function deleteContent(lessonId, contentId) {
        if (confirm('Delete this section? This action cannot be undone.')) {
            showToast('Deleting section...', 'info');

            fetch('/lms/lessons/' + lessonId + '/content/' + contentId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Section deleted! Refreshing...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 500);
                } else {
                    showToast('Error deleting section', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error deleting section', 'error');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            });
        }
    }



    
    function saveFileNote(contentId) {
        const note = document.getElementById('file-note-' + contentId).value;
        
        fetch('/lms/lessons/' + lessonId + '/file-note/' + contentId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ note: note })
        }).then(() => {
            showToast('File note saved!', 'success');
        }).catch(error => {
            console.error('Error:', error);
        });
    }
    
    const contentTypeSelect = document.getElementById('content-type-select');
    if (contentTypeSelect) {
        contentTypeSelect.addEventListener('change', function(e) {
            const type = e.target.value;
            const textArea = document.getElementById('text-input-area');
            const fileArea = document.getElementById('file-input-area');
            const linkArea = document.getElementById('link-input-area');
            
            if (textArea) textArea.style.display = type === 'text' ? 'block' : 'none';
            if (fileArea) fileArea.style.display = type === 'file' ? 'block' : 'none';
            if (linkArea) linkArea.style.display = type === 'link' ? 'block' : 'none';
        });
    }
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('add-content-modal');
            if (modal && modal.style.display === 'flex') {
                closeAddContent();
            }
        }
    });
    
    const observer = new MutationObserver(() => updateThemeIcon());
    observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    
    updateProgress();
    updateThemeIcon();
</script>
@endsection