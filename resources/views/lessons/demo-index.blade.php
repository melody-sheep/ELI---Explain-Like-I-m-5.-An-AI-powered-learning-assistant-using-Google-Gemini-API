@extends('layouts.app')

@section('content')
<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border);">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="/" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary);">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO HOME</p>
            </div>
        </a>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 20px; font-weight: 700; font-family: monospace; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 28px;">school</span>
                    DEMO LESSONS
                </h1>
                <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Click any card to view lesson content (No API required)</p>
            </div>
            <div style="border: 1px solid var(--accent-green); background-color: var(--bg-tertiary); padding: 8px 16px;">
                <span style="color: var(--accent-green); font-size: 11px;">✓ Demo Mode</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
            @foreach($lessons as $lesson)
                <a href="{{ url('/demo/lessons/' . $lesson->id) }}" style="text-decoration: none; display: block;">
                    <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); overflow: hidden; transition: all 0.2s; cursor: pointer;">
                        <div style="padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                <span style="font-size: 10px; color: var(--accent-green); background-color: var(--bg-tertiary); padding: 4px 8px; border-radius: 4px;">
                                    {{ $lesson->subject ?? 'General' }}
                                </span>
                                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 20px;">
                                    click
                                </span>
                            </div>
                            
                            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px;">
                                {{ $lesson->title }}
                            </h3>
                            
                            <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.5; margin-bottom: 16px;">
                                {{ $lesson->description }}
                            </p>
                            
                            <div style="display: flex; gap: 16px; border-top: 1px solid var(--border); padding-top: 12px; margin-top: 8px;">
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 16px;">description</span>
                                    <span style="font-size: 11px; color: var(--text-muted);">{{ count($lesson->contents) }} sections</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 16px;">visibility</span>
                                    <span style="font-size: 11px; color: var(--text-muted);">Click to view</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div style="margin-top: 32px; padding: 16px; border: 1px solid var(--border); background-color: var(--bg-secondary); text-align: center;">
            <p style="font-size: 11px; color: var(--text-muted);">
                📚 <strong>Demo Mode</strong> - 3 sample lessons with complete content. No API calls needed. Perfect for presentation!
            </p>
        </div>
    </div>
</div>
@endsection