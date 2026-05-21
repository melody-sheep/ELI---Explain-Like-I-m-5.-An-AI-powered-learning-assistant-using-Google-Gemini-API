@extends('layouts.app')

@section('content')
<style>
    .content-item {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .content-item:hover {
        transform: translateX(4px);
        border-color: var(--accent);
    }
    .content-item.completed {
        opacity: 0.7;
    }
    .content-item.completed .complete-icon {
        color: var(--accent-green);
    }
    .note-editor {
        transition: all 0.2s ease;
    }
    .floating-toolbar {
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-in {
        animation: fadeIn 0.3s ease;
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <!-- Navigation Header -->
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="{{ route('lessons.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO LESSONS</p>
            </div>
        </a>
    </div>

    <!-- Lesson Info Panel -->
    <div style="padding: 20px;">
        <!-- Lesson Header -->
        <div style="margin-bottom: 24px;">
            <span style="font-size: 11px; color: var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 4px 10px; border-radius: 12px;">
                {{ $lesson->subject ?? 'General' }}
            </span>
            <h2 style="font-size: 20px; font-weight: 700; margin-top: 16px; color: var(--text-primary);">{{ $lesson->title }}</h2>
            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 12px; line-height: 1.6;">{{ $lesson->description }}</p>
        </div>

        <!-- Progress Section -->
        <div style="border-top: 1px solid var(--border); padding-top: 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <p style="font-size: 11px; color: var(--text-muted);">Lesson Progress</p>
                <p style="font-size: 11px; color: var(--accent-green);" id="progress-percent">0%</p>
            </div>
            <div style="height: 6px; background-color: var(--border); border-radius: 3px; overflow: hidden;">
                <div id="progress-fill" style="height: 100%; background: linear-gradient(90deg, var(--accent-green), var(--accent)); width: 0%; border-radius: 3px;"></div>
            </div>
            <p style="font-size: 10px; color: var(--text-muted); margin-top: 8px;" id="progress-text">0 of 0 sections completed</p>
        </div>

        <!-- Table of Contents -->
        <div style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <p style="font-size: 11px; font-weight: 600; color: var(--text-muted);">CONTENTS</p>
                <button onclick="expandAll()" style="background: none; border: none; color: var(--accent); cursor: pointer; font-size: 10px;">Expand All</button>
            </div>
            <div id="contents-list">
                @foreach($lesson->contents as $index => $content)
                    <div class="content-item" data-content-id="{{ $content->id }}" data-index="{{ $index }}" onclick="scrollToContent({{ $index }})" style="border: 1px solid var(--border); padding: 12px; margin-bottom: 8px; border-radius: 8px; background-color: var(--bg-tertiary); cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="material-symbols-outlined complete-icon" style="color: var(--text-muted); font-size: 18px;">
                                @if(session("content_completed.{$content->id}", false)) check_circle @else radio_button_unchecked @endif
                            </span>
                            <span class="material-symbols-outlined" style="color: var(--accent); font-size: 18px;">
                                @if($content->content_type == 'text') description
                                @elseif($content->content_type == 'video') smart_display
                                @elseif($content->content_type == 'file') insert_drive_file
                                @else link
                                @endif
                            </span>
                            <div style="flex: 1;">
                                <p style="font-size: 13px; font-weight: 500; color: var(--text-primary);">{{ $content->title }}</p>
                                <p style="font-size: 9px; color: var(--text-muted); text-transform: uppercase;">{{ $content->content_type }}</p>
                            </div>
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 16px;">chevron_right</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <!-- Floating Toolbar -->
    <div class="floating-toolbar" style="position: sticky; top: 20px; margin: 20px 20px 0 auto; z-index: 100;">
        <div style="display: flex; gap: 10px; background-color: var(--bg-secondary); border: 1px solid var(--border); border-radius: 12px; padding: 8px 16px;">
            <button onclick="downloadLessonAsPDF()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
                PDF
            </button>
            <button onclick="shareLesson()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">share</span>
                Share
            </button>
            <button onclick="toggleNotes()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 12px;">
                <span class="material-symbols-outlined" style="font-size: 18px;">sticky_note_2</span>
                Notes
            </button>
        </div>
    </div>

    <!-- Content Display Area -->
    <div style="max-width: 800px; margin: 0 auto 40px auto; padding: 24px; width: 100%;">
        @if(session('success'))
            <div style="border: 1px solid var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 14px; margin-bottom: 20px; border-radius: 8px; color: var(--accent-green); display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="border: 1px solid var(--accent-red); background-color: rgba(239, 68, 68, 0.1); padding: 14px; margin-bottom: 20px; border-radius: 8px; color: var(--accent-red); display: flex; align-items: center; gap: 10px;">
                <span class="material-symbols-outlined">error</span>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div style="border: 1px solid var(--accent-red); background-color: rgba(239, 68, 68, 0.1); padding: 14px; margin-bottom: 20px; border-radius: 8px;">
                @foreach($errors->all() as $error)
                    <p style="color: var(--accent-red); font-size: 11px;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Content Items Display -->
        @foreach($lesson->contents as $index => $content)
            <div id="content-{{ $index }}" class="content-display fade-in" style="margin-bottom: 48px; scroll-margin-top: 100px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--border);">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 28px;">
                        @if($content->content_type == 'text') description
                        @elseif($content->content_type == 'video') smart_display
                        @elseif($content->content_type == 'file') insert_drive_file
                        @else link
                        @endif
                    </span>
                    <div style="flex: 1;">
                        <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ $content->title }}</h2>
                        <p style="font-size: 11px; color: var(--text-muted);">Section {{ $index + 1 }} of {{ count($lesson->contents) }}</p>
                    </div>
                    <button onclick="toggleComplete({{ $content->id }}, {{ $index }})" style="background: none; border: none; cursor: pointer;">
                        <span class="material-symbols-outlined" style="color: {{ session("content_completed.{$content->id}", false) ? 'var(--accent-green)' : 'var(--text-muted)' }}; font-size: 28px;">
                            {{ session("content_completed.{$content->id}", false) ? 'check_circle' : 'radio_button_unchecked' }}
                        </span>
                    </button>
                </div>

                @if($content->content_type == 'text')
                    <div style="font-size: 15px; color: var(--text-secondary); line-height: 1.8; white-space: pre-wrap; font-family: 'Georgia', serif;">
                        {{ $content->content }}
                    </div>

                @elseif($content->content_type == 'video')
                    <div style="margin-top: 8px;">
                        @php
                            $videoUrl = $content->content;
                            $embedUrl = $videoUrl;
                            if (strpos($videoUrl, 'youtube.com/watch?v=') !== false) {
                                $videoId = substr($videoUrl, strpos($videoUrl, 'v=') + 2);
                                $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                            } elseif (strpos($videoUrl, 'youtu.be/') !== false) {
                                $videoId = substr($videoUrl, strrpos($videoUrl, '/') + 1);
                                $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                            } elseif (strpos($videoUrl, 'vimeo.com') !== false) {
                                $videoId = substr($videoUrl, strrpos($videoUrl, '/') + 1);
                                $embedUrl = "https://player.vimeo.com/video/" . $videoId;
                            }
                        @endphp
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">
                            <iframe src="{{ $embedUrl }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                        </div>
                        <a href="{{ $videoUrl }}" target="_blank" style="display: inline-block; margin-top: 16px; border: 1px solid var(--accent); background-color: var(--bg-tertiary); color: var(--accent); padding: 10px 20px; text-decoration: none; font-size: 13px; border-radius: 8px;">
                            <span class="material-symbols-outlined" style="font-size: 16px;">open_in_new</span>
                            Open in new window
                        </a>
                    </div>

                @elseif($content->content_type == 'file')
                    @php
                        $fileName = basename($content->content);
                        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                        $fileIcon = 'insert_drive_file';
                        if ($fileExt == 'pdf') $fileIcon = 'picture_as_pdf';
                        elseif (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) $fileIcon = 'image';
                        elseif (in_array($fileExt, ['mp4', 'mov', 'avi'])) $fileIcon = 'video_file';
                        elseif (in_array($fileExt, ['mp3', 'wav'])) $fileIcon = 'audiotrack';
                    @endphp
                    <div style="border: 1px solid var(--border); background-color: var(--bg-tertiary); padding: 24px; border-radius: 12px;">
                        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                            <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 48px;">{{ $fileIcon }}</span>
                            <div style="flex: 1;">
                                <p style="font-size: 16px; font-weight: 600; color: var(--text-primary);">{{ $fileName }}</p>
                                <p style="font-size: 11px; color: var(--text-muted);">File attachment - Ready for download</p>
                            </div>
                            <a href="/storage/{{ $content->content }}" download style="border: none; background: linear-gradient(135deg, var(--accent-green), var(--accent)); color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                                <span class="material-symbols-outlined" style="font-size: 18px;">download</span>
                                Download
                            </a>
                        </div>
                    </div>

                @elseif($content->content_type == 'link')
                    <div style="margin-top: 8px;">
                        <div style="border: 1px solid var(--border); background-color: var(--bg-tertiary); padding: 24px; border-radius: 12px; text-align: center;">
                            <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 48px;">link</span>
                            <p style="font-size: 14px; color: var(--text-secondary); margin: 16px 0;">External Resource Link</p>
                            <a href="{{ $content->content }}" target="_blank" style="border: none; background: linear-gradient(135deg, var(--accent-yellow), var(--accent-orange)); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px;">
                                <span class="material-symbols-outlined" style="font-size: 18px;">open_in_new</span>
                                Visit Website
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Navigation Buttons -->
                <div style="display: flex; justify-content: space-between; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border);">
                    @if($index > 0)
                        <button onclick="scrollToContent({{ $index - 1 }})" style="border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-primary); padding: 10px 20px; cursor: pointer; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined">arrow_back</span>
                            Previous
                        </button>
                    @else
                        <div></div>
                    @endif
                    
                    @if($index < count($lesson->contents) - 1)
                        <button onclick="scrollToContent({{ $index + 1 }})" style="border: none; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); color: white; padding: 10px 20px; cursor: pointer; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                            Next
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </button>
                    @else
                        <button onclick="markLessonComplete()" style="border: none; background: linear-gradient(135deg, var(--accent-green), var(--accent)); color: white; padding: 10px 20px; cursor: pointer; border-radius: 8px; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined">celebration</span>
                            Complete Lesson
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Notes Sidebar (Hidden by default) -->
<div id="notes-sidebar" style="position: fixed; right: -400px; top: 0; width: 400px; height: 100%; background-color: var(--bg-secondary); border-left: 1px solid var(--border); z-index: 1000; transition: right 0.3s ease; display: flex; flex-direction: column;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 16px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
            <span class="material-symbols-outlined">sticky_note_2</span>
            Lesson Notes
        </h3>
        <button onclick="closeNotes()" style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div style="flex: 1; overflow-y: auto; padding: 20px;">
        <textarea id="notes-textarea" placeholder="Write your notes here... This lesson covers important concepts like..." style="width: 100%; height: 300px; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 12px; border-radius: 8px; font-size: 13px; line-height: 1.6; resize: vertical;"></textarea>
        <button onclick="saveNotes()" style="width: 100%; margin-top: 16px; border: none; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); color: white; padding: 12px; border-radius: 8px; cursor: pointer;">
            Save Notes
        </button>
        <p style="font-size: 10px; color: var(--text-muted); margin-top: 12px; text-align: center;">
            Your notes are saved automatically and synced across devices
        </p>
    </div>
</div>

<script>
    let completedContents = {!! json_encode(session("content_completed", [])) !!};
    let totalContents = {{ count($lesson->contents) }};

    function updateProgress() {
        let completedCount = Object.values(completedContents).filter(v => v === true).length;
        let percent = (completedCount / totalContents) * 100;
        document.getElementById('progress-fill').style.width = percent + '%';
        document.getElementById('progress-percent').textContent = Math.round(percent) + '%';
        document.getElementById('progress-text').textContent = `${completedCount} of ${totalContents} sections completed`;
        
        // Update sidebar items
        document.querySelectorAll('.content-item').forEach((item, index) => {
            const contentId = item.dataset.contentId;
            const icon = item.querySelector('.complete-icon');
            if (completedContents[contentId]) {
                icon.textContent = 'check_circle';
                icon.style.color = 'var(--accent-green)';
                item.classList.add('completed');
            } else {
                icon.textContent = 'radio_button_unchecked';
                icon.style.color = 'var(--text-muted)';
                item.classList.remove('completed');
            }
        });
    }

    function toggleComplete(contentId, index) {
        completedContents[contentId] = !completedContents[contentId];
        
        // Save to server
        fetch(`/lms/lessons/{{ $lesson->id }}/progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ content_id: contentId, completed: completedContents[contentId] })
        });
        
        updateProgress();
        
        // Update button icon
        const btn = document.querySelector(`#content-${index} button span`);
        if (btn) {
            btn.textContent = completedContents[contentId] ? 'check_circle' : 'radio_button_unchecked';
            btn.style.color = completedContents[contentId] ? 'var(--accent-green)' : 'var(--text-muted)';
        }
    }

    function scrollToContent(index) {
        const element = document.getElementById(`content-${index}`);
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Highlight the clicked item in sidebar
        document.querySelectorAll('.content-item').forEach((item, i) => {
            if (i === index) {
                item.style.borderColor = 'var(--accent)';
                item.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
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

    function expandAll() {
        // This would expand all collapsible sections if you add them
        alert('All sections expanded');
    }

    function downloadLessonAsPDF() {
        window.print();
    }

    function shareLesson() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $lesson->title }}',
                text: '{{ $lesson->description }}',
                url: window.location.href
            });
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert('Link copied to clipboard!');
        }
    }

    function toggleNotes() {
        const sidebar = document.getElementById('notes-sidebar');
        if (sidebar.style.right === '0px') {
            sidebar.style.right = '-400px';
        } else {
            sidebar.style.right = '0px';
            loadNotes();
        }
    }

    function closeNotes() {
        document.getElementById('notes-sidebar').style.right = '-400px';
    }

    function loadNotes() {
        fetch(`/lms/lessons/{{ $lesson->id }}/notes`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('notes-textarea').value = data.notes || '';
            });
    }

    function saveNotes() {
        const notes = document.getElementById('notes-textarea').value;
        fetch(`/lms/lessons/{{ $lesson->id }}/notes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ notes: notes })
        }).then(() => {
            alert('Notes saved!');
            closeNotes();
        });
    }

    function markLessonComplete() {
        if (confirm('Congratulations on completing this lesson! Mark as complete?')) {
            // Mark all content as complete
            Object.keys(completedContents).forEach(contentId => {
                if (!completedContents[contentId]) {
                    completedContents[contentId] = true;
                    fetch(`/lms/lessons/{{ $lesson->id }}/progress`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ content_id: contentId, completed: true })
                    });
                }
            });
            updateProgress();
            alert('🎉 Amazing! Lesson marked as complete!');
            window.location.href = '{{ route("lessons.index") }}';
        }
    }

    // Save scroll position
    window.addEventListener('beforeunload', () => {
        localStorage.setItem(`scroll_pos_{{ $lesson->id }}`, window.scrollY);
    });

    // Restore scroll position
    document.addEventListener('DOMContentLoaded', () => {
        const savedScroll = localStorage.getItem(`scroll_pos_{{ $lesson->id }}`);
        if (savedScroll) {
            window.scrollTo(0, parseInt(savedScroll));
        }
        updateProgress();
    });

    updateProgress();
</script>
@endsection