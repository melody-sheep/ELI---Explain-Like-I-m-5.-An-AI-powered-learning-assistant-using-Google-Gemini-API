@extends('layouts.app')

@section('content')
<script>
    // Simulate loading for realism
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Lesson content loaded from database');
    });
</script>
<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="{{ url('/demo/lessons') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary);">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO LESSONS</p>
            </div>
        </a>
    </div>

    <div style="padding: 20px;">
        <div style="margin-bottom: 24px;">
            <span style="font-size: 11px; color: var(--accent-green); background-color: var(--bg-tertiary); padding: 4px 8px; border-radius: 4px;">
                {{ $lesson->subject ?? 'General' }}
            </span>
            <h2 style="font-size: 24px; font-weight: 700; margin-top: 16px; color: var(--text-primary);">
                {{ $lesson->title }}
            </h2>
            <p style="font-size: 10px; color: var(--text-muted); margin-top: 8px;">
                Last updated: {{ date('F j, Y') }} • {{ rand(120, 450) }} views
            </p>
            <p style="font-size: 14px; color: var(--text-secondary); margin-top: 12px; line-height: 1.6;">
                {{ $lesson->description }}
            </p>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 20px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <span class="material-symbols-outlined" style="color: var(--accent);">menu_book</span>
                <h3 style="font-size: 14px; font-weight: 600; color: var(--text-primary);">LESSON CONTENTS</h3>
                <span style="font-size: 11px; color: var(--text-muted);">({{ count($lesson->contents) }} items)</span>
            </div>
            
            @foreach($lesson->contents as $index => $content)
                <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); margin-bottom: 16px; overflow: hidden;">
                    <div style="padding: 16px; border-bottom: 1px solid var(--border); background-color: var(--bg-tertiary);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 12px; font-weight: 600; color: var(--accent-green);">#{{ $index + 1 }}</span>
                            <span class="material-symbols-outlined" style="color: var(--accent); font-size: 20px;">
                                @if($content->content_type == 'text') description
                                @elseif($content->content_type == 'video') smart_display
                                @elseif($content->content_type == 'file') insert_drive_file
                                @else link
                                @endif
                            </span>
                            <span style="font-size: 16px; font-weight: 600; color: var(--text-primary);">{{ $content->title }}</span>
                            <span style="margin-left: auto; font-size: 10px; color: var(--text-muted); text-transform: uppercase;">
                                {{ $content->content_type }}
                            </span>
                        </div>
                    </div>
                    
                    <div style="padding: 20px;">
                        @if($content->content_type == 'text')
                            <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.8; white-space: pre-wrap; font-family: monospace;">
                                {{ $content->content }}
                            </div>
                        
                        @elseif($content->content_type == 'video')
                            <div style="margin-top: 8px;">
                                <a href="{{ $content->content }}" target="_blank" style="border: 2px solid var(--accent); background-color: transparent; color: var(--accent); padding: 12px 24px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border-radius: 4px;">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">play_circle</span>
                                    Watch Video Lesson
                                </a>
                                <p style="font-size: 11px; color: var(--text-muted); margin-top: 12px;">📺 Click to watch video tutorial</p>
                            </div>
                        
                        @elseif($content->content_type == 'file')
                            <div style="margin-top: 8px;">
                                <div style="border: 1px solid var(--border); background-color: var(--bg-tertiary); padding: 16px; border-radius: 4px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 32px;">picture_as_pdf</span>
                                        <div>
                                            <p style="font-size: 14px; font-weight: 600; color: var(--text-primary);">{{ $content->content }}</p>
                                            <p style="font-size: 11px; color: var(--text-muted);">PDF Document - Ready for download</p>
                                        </div>
                                        <button onclick="alert('📄 Demo file would download here. In full version, PDFs are stored and downloadable.')" style="margin-left: auto; border: 1px solid var(--accent-green); background-color: transparent; color: var(--accent-green); padding: 8px 16px; cursor: pointer; border-radius: 4px;">
                                            Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        
                        @elseif($content->content_type == 'link')
                            <div style="margin-top: 8px;">
                                <a href="{{ $content->content }}" target="_blank" style="border: 2px solid var(--accent-yellow); background-color: transparent; color: var(--accent-yellow); padding: 12px 24px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border-radius: 4px;">
                                    <span class="material-symbols-outlined" style="font-size: 20px;">open_in_new</span>
                                    Open Resource Link
                                </a>
                                <p style="font-size: 11px; color: var(--text-muted); margin-top: 12px;">🔗 External resource (opens in new tab)</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="margin-top: 32px; padding: 16px; background-color: var(--bg-tertiary); border: 1px solid var(--border); text-align: center;">
            <p style="font-size: 11px; color: var(--text-muted);">
                💡 <strong>Demo Content</strong> - This lesson content is pre-loaded and requires no API calls. Perfect for presentation!
            </p>
        </div>
    </div>
</div>

<div style="flex: 1; background-color: var(--bg-primary); display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; padding: 40px;">
        <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 48px;">check_circle</span>
        <h3 style="color: var(--text-primary); margin-top: 16px;">Content Displayed</h3>
        <p style="color: var(--text-muted); font-size: 13px; max-width: 300px;">
            Lesson content is shown in the left panel. Click any content section to view details.
        </p>
    </div>
</div>
@endsection