@extends('layouts.app')

@section('content')
<style>
    /* Animations */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideOut {
        from { width: 280px; }
        to { width: 0px; }
    }
    
    @keyframes slideInSidebar {
        from { width: 0px; }
        to { width: 280px; }
    }
    
    @keyframes buttonPress {
        0% { transform: scale(1); }
        50% { transform: scale(0.95); }
        100% { transform: scale(1); }
    }
    
    .message-animate {
        animation: fadeIn 0.3s ease;
    }
    
    .typing-dot {
        width: 8px;
        height: 8px;
        background-color: var(--accent);
        border-radius: 50%;
        animation: bounce 0.6s infinite;
    }
    
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    
    /* Button Press Effect */
    .btn-press {
        transition: all 0.1s ease;
        cursor: pointer;
    }
    
    .btn-press:active {
        transform: scale(0.96);
    }
    
    /* Hover Effects */
    .hover-scale {
        transition: all 0.2s ease;
    }
    
    .hover-scale:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .suggestion-chip {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .suggestion-chip:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, var(--accent), var(--accent-hover)) !important;
        color: white !important;
        border-color: transparent !important;
    }
    
    .quick-action-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .quick-action-btn:hover {
        transform: translateX(4px);
    }
    
    .mode-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .mode-btn:hover:not(.active-mode) {
        border-color: var(--accent) !important;
        color: var(--accent) !important;
        transform: translateY(-1px);
    }
    
    .active-mode {
        background: linear-gradient(135deg, var(--accent), var(--accent-hover)) !important;
        color: white !important;
        border: none !important;
    }
    
    .send-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .send-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    
    .theme-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .theme-btn:hover {
        transform: rotate(15deg);
        border-color: var(--accent) !important;
    }
    
    .collapse-btn {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .collapse-btn:hover {
        background-color: var(--accent) !important;
        color: white !important;
    }
    
    /* Scrollbar */
    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }
    
    .sidebar-scroll::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
    }
    
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 4px;
    }
    
    #messages-area::-webkit-scrollbar {
        width: 6px;
    }
    
    #messages-area::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
    }
    
    #messages-area::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 4px;
    }
    
    /* Sidebar Collapse Transition */
    .sidebar-collapsed {
        width: 60px !important;
        min-width: 60px !important;
    }
    
    .sidebar-collapsed .sidebar-content {
        opacity: 0;
        visibility: hidden;
        display: none;
    }
    
    .sidebar-collapsed .sidebar-icons-only {
        display: flex !important;
    }
    
    .sidebar-icons-only {
        display: none;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        padding: 16px 0;
    }
    
    /* Code styling */
    pre {
        background-color: var(--bg-tertiary);
        padding: 12px;
        border-radius: 8px;
        overflow-x: auto;
        font-size: 12px;
        border: 1px solid var(--border);
    }
    
    .input-focus:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        outline: none;
    }
    
    /* Responsive for mobile */
    @media (max-width: 768px) {
        .sidebar-responsive {
            position: fixed;
            z-index: 1000;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        }
        
        .sidebar-responsive.sidebar-collapsed {
            left: -280px;
        }
        
        .sidebar-responsive:not(.sidebar-collapsed) {
            left: 0;
        }
        
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        .mobile-menu-btn {
            display: flex !important;
        }
    }
    
    .mobile-menu-btn {
        display: none;
    }
</style>

<!-- Mobile Overlay -->
<div id="mobile-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 998;" onclick="toggleSidebar()"></div>

<!-- Sidebar - COLLAPSIBLE -->
<div id="sidebar" style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); height: 100vh; overflow: hidden; transition: width 0.3s ease; position: relative; z-index: 1000;">
    
    <!-- Full Sidebar Content -->
    <div class="sidebar-content" style="display: flex; flex-direction: column; height: 100%; overflow: hidden;">
        
        <!-- Logo Section - Compact -->
        <div style="padding: 16px 16px; border-bottom: 1px solid var(--border); flex-shrink: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); border-radius: 10px;">
                        <span class="material-symbols-outlined" style="color: white; font-size: 20px;">smart_toy</span>
                    </div>
                    <div>
                        <span style="font-size: 16px; font-weight: 800; color: var(--text-primary);">ELI</span>
                        <p style="font-size: 8px; letter-spacing: 0.5px; color: var(--accent); font-weight: 600;">EXPLAIN LIKE I'M 5</p>
                    </div>
                </div>
                <div style="display: flex; gap: 6px;">
                    <button onclick="toggleTheme()" class="theme-btn" style="width: 32px; height: 32px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); cursor: pointer; border-radius: 8px;">
                        <span id="theme-icon" class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 18px;">dark_mode</span>
                    </button>
                    <button onclick="toggleSidebar()" class="collapse-btn" style="width: 32px; height: 32px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); cursor: pointer; border-radius: 8px;">
                        <span class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 18px;">chevron_left</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- New Chat Button - Compact -->
        <div style="padding: 12px 16px; flex-shrink: 0;">
            <button onclick="newConversation()" class="btn-press hover-scale" style="width: 100%; border: none; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); color: white; font-family: monospace; font-size: 12px; font-weight: 600; padding: 8px; display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; border-radius: 8px;">
                <span class="material-symbols-outlined" style="font-size: 16px;">edit_note</span>
                NEW CHAT
            </button>
        </div>

        <!-- Stats Dashboard - Compact -->
        <div style="padding: 0 16px 12px 16px; flex-shrink: 0;">
            <div style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 10px; padding: 12px; border: 1px solid var(--border);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 24px;">local_fire_department</span>
                    <div>
                        <p style="font-size: 9px; color: var(--text-muted);">Learning Streak</p>
                        <p style="font-size: 22px; font-weight: 700; color: var(--text-primary); line-height: 1;" id="streak-count">0</p>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <div style="text-align: center; padding: 6px; background-color: var(--bg-primary); border-radius: 8px;">
                        <p style="font-size: 18px; font-weight: 700; color: var(--accent-green); margin-bottom: 2px;" id="lessons-done">0</p>
                        <p style="font-size: 9px; color: var(--text-muted);">Lessons</p>
                    </div>
                    <div style="text-align: center; padding: 6px; background-color: var(--bg-primary); border-radius: 8px;">
                        <p style="font-size: 18px; font-weight: 700; color: var(--accent-yellow); margin-bottom: 2px;" id="flashcards-mastered">0</p>
                        <p style="font-size: 9px; color: var(--text-muted);">Flashcards</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions - Compact -->
        <div style="padding: 4px 16px; flex-shrink: 0;">
            <div style="border-top: 1px solid var(--border); padding-top: 10px;">
                <p style="font-size: 9px; font-weight: 600; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 8px;">QUICK ACTIONS</p>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <a href="{{ url('/lms/lessons') }}" class="quick-action-btn" style="width: 100%; border: 1px solid var(--accent-green); background-color: transparent; color: var(--accent-green); font-family: monospace; font-size: 11px; padding: 6px 10px; display: flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none; border-radius: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">menu_book</span>
                        Review Lessons
                    </a>
                    <a href="{{ url('/lms/flashcards') }}" class="quick-action-btn" style="width: 100%; border: 1px solid var(--accent-yellow); background-color: transparent; color: var(--accent-yellow); font-family: monospace; font-size: 11px; padding: 6px 10px; display: flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none; border-radius: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">auto_stories</span>
                        Flashcards
                    </a>
                    <a href="{{ url('/lms/quizzes') }}" class="quick-action-btn" style="width: 100%; border: 1px solid var(--accent-purple); background-color: transparent; color: var(--accent-purple); font-family: monospace; font-size: 11px; padding: 6px 10px; display: flex; align-items: center; gap: 8px; cursor: pointer; text-decoration: none; border-radius: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 16px;">quiz</span>
                        Practice Quiz
                    </a>
                </div>
            </div>
        </div>

        <!-- Daily Quote - Compact -->
        <div style="padding: 10px 16px; flex-shrink: 0;">
            <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(168, 85, 247, 0.1)); border-radius: 8px; padding: 8px 10px; border-left: 3px solid var(--accent);">
                <p style="font-size: 9px; color: var(--accent); margin-bottom: 4px;">✨ DAILY INSPIRATION</p>
                <p id="daily-quote" style="font-size: 10px; color: var(--text-secondary); line-height: 1.4;">"The expert in anything was once a beginner."</p>
            </div>
        </div>

        <!-- Recent Conversations - SCROLLABLE AREA -->
        <div class="sidebar-scroll" style="flex: 1; overflow-y: auto; padding: 8px 16px 12px 16px; min-height: 0;">
            <div style="border-top: 1px solid var(--border); padding-top: 10px;">
                <p style="font-size: 9px; font-weight: 600; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 8px;">RECENT CONVERSATIONS</p>
                <div id="conversations-list" style="display: flex; flex-direction: column; gap: 6px;"></div>
            </div>
        </div>

        <!-- User Info - Compact at bottom -->
        <div style="padding: 10px 16px; border-top: 1px solid var(--border); flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--accent), var(--accent-purple)); border-radius: 8px;">
                    <span class="material-symbols-outlined" style="color: white; font-size: 18px;">account_circle</span>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 11px; font-weight: 600; color: var(--text-primary);">Guest User</p>
                    <p style="font-size: 8px; color: var(--text-muted);">AI Learning Assistant</p>
                </div>
                <button onclick="clearAllHistory()" class="btn-press" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">more_vert</span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Icons Only View (When Collapsed) -->
    <div class="sidebar-icons-only" style="display: none; flex-direction: column; align-items: center; height: 100%; padding: 16px 0;">
        <div style="margin-bottom: 20px;">
            <div style="width: 40px; height: 40px; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); border-radius: 10px;">
                <span class="material-symbols-outlined" style="color: white; font-size: 22px;">smart_toy</span>
            </div>
        </div>
        
        <button onclick="newConversation()" class="btn-press" style="width: 40px; height: 40px; border: 1px solid var(--accent); background: linear-gradient(135deg, var(--accent), var(--accent-hover)); color: white; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <span class="material-symbols-outlined" style="font-size: 20px;">edit_note</span>
        </button>
        
        <a href="{{ url('/lms/lessons') }}" style="width: 40px; height: 40px; border: 1px solid var(--accent-green); background-color: transparent; color: var(--accent-green); border-radius: 10px; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
            <span class="material-symbols-outlined" style="font-size: 20px;">menu_book</span>
        </a>
        
        <a href="{{ url('/lms/flashcards') }}" style="width: 40px; height: 40px; border: 1px solid var(--accent-yellow); background-color: transparent; color: var(--accent-yellow); border-radius: 10px; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
            <span class="material-symbols-outlined" style="font-size: 20px;">auto_stories</span>
        </a>
        
        <a href="{{ url('/lms/quizzes') }}" style="width: 40px; height: 40px; border: 1px solid var(--accent-purple); background-color: transparent; color: var(--accent-purple); border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
            <span class="material-symbols-outlined" style="font-size: 20px;">quiz</span>
        </a>
        
        <div style="flex: 1;"></div>
        
        <button onclick="toggleTheme()" class="theme-btn" style="width: 40px; height: 40px; border: 1px solid var(--border); background-color: var(--bg-primary); border-radius: 10px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <span class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 20px;">dark_mode</span>
        </button>
        
        <button onclick="toggleSidebar()" class="collapse-btn" style="width: 40px; height: 40px; border: 1px solid var(--border); background-color: var(--bg-primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <span class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 20px;">chevron_right</span>
        </button>
    </div>
</div>

<!-- Mobile Menu Button (visible on small screens) -->
<button onclick="toggleSidebar()" class="mobile-menu-btn" style="position: fixed; bottom: 20px; left: 20px; width: 48px; height: 48px; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); border: none; border-radius: 50%; color: white; cursor: pointer; z-index: 997; box-shadow: 0 4px 12px rgba(0,0,0,0.3); display: none; align-items: center; justify-content: center;">
    <span class="material-symbols-outlined" style="font-size: 24px;">menu</span>
</button>

<!-- Main Chat Area -->
<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); height: 100vh; overflow: hidden;">

    <!-- Header - Compact -->
    <div style="padding: 12px 24px; border-bottom: 1px solid var(--border); background-color: var(--bg-primary); flex-shrink: 0;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 24px;">auto_awesome</span>
                    <div>
                        <h1 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">AI Assistant</h1>
                        <p style="font-size: 9px; color: var(--text-muted);">Powered by Google Gemini AI</p>
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="exportChat()" class="btn-press" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); font-family: monospace; font-size: 10px; cursor: pointer; border-radius: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 14px;">download</span>
                    Export
                </button>
                <button onclick="clearChat()" class="btn-press" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px; border: 1px solid var(--accent-red); background-color: transparent; color: var(--accent-red); font-family: monospace; font-size: 10px; cursor: pointer; border-radius: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 14px;">delete_sweep</span>
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Messages Area - SCROLLABLE -->
    <div id="messages-area" style="flex: 1; overflow-y: auto; padding: 20px 24px; min-height: 0;">
        <!-- Welcome message will be injected here -->
    </div>

    <!-- Smart Suggestions - Compact -->
    <div id="suggestions-area" style="padding: 0 24px 8px 24px; flex-shrink: 0;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;" id="suggestions-list"></div>
    </div>

    <!-- Input Area - Compact at bottom -->
    <div style="border-top: 1px solid var(--border); background-color: var(--bg-primary); flex-shrink: 0;">
        
        <!-- Mode Buttons - Compact -->
        <div style="padding: 12px 24px 0 24px;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button onclick="setMode('ask')" id="btn-ask" class="mode-btn active-mode" style="border: none; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); color: white; font-family: monospace; font-size: 11px; font-weight: 600; padding: 6px 14px; cursor: pointer; border-radius: 20px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">help</span> ASK
                </button>
                <button onclick="setMode('summarize')" id="btn-summarize" class="mode-btn" style="border: 1px solid var(--border); background-color: transparent; color: var(--text-secondary); font-family: monospace; font-size: 11px; padding: 6px 14px; cursor: pointer; border-radius: 20px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">summarize</span> SUMMARIZE
                </button>
                <button onclick="setMode('eli5')" id="btn-eli5" class="mode-btn" style="border: 1px solid var(--border); background-color: transparent; color: var(--text-secondary); font-family: monospace; font-size: 11px; padding: 6px 14px; cursor: pointer; border-radius: 20px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">child_care</span> ELI5
                </button>
                <button onclick="setMode('code')" id="btn-code" class="mode-btn" style="border: 1px solid var(--border); background-color: transparent; color: var(--text-secondary); font-family: monospace; font-size: 11px; padding: 6px 14px; cursor: pointer; border-radius: 20px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">code</span> CODE
                </button>
            </div>
        </div>

        <!-- Input Field with Voice - FIXED ALIGNMENT -->
        <div style="padding: 12px 24px 16px 24px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="flex: 1; position: relative;">
                    <textarea id="user-input" rows="1" class="input-focus" style="width: 100%; background-color: var(--bg-tertiary); border: 2px solid var(--border); color: var(--text-primary); font-family: monospace; font-size: 12px; padding: 12px 40px 12px 14px; resize: none; outline: none; border-radius: 14px; transition: all 0.2s;" placeholder="Type your message... (Enter to send)"></textarea>
                    <div style="position: absolute; right: 10px; bottom: 50%; transform: translateY(50%);">
                        <button onclick="startVoiceInput()" class="btn-press" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 4px;">
                            <span class="material-symbols-outlined" style="font-size: 20px;">mic</span>
                        </button>
                    </div>
                </div>
                <button onclick="sendRequest()" id="submit-btn" class="send-btn" style="border: none; background: linear-gradient(135deg, var(--accent), var(--accent-hover)); color: white; font-family: monospace; font-size: 12px; font-weight: 600; padding: 12px 24px; cursor: pointer; border-radius: 14px; display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">send</span>
                    SEND
                </button>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 8px; padding: 0 4px;">
                <span id="char-count" style="font-size: 9px; font-family: monospace; color: var(--text-muted);">0 characters</span>
                <span id="mode-indicator" style="font-size: 9px; font-family: monospace; color: var(--accent);">Mode: ASK</span>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<script>
    // ==================== CONFIGURATION ====================
    let currentMode = 'ask';
    let currentConversationId = null;
    let conversations = [];
    let recognition = null;
    
    // ==================== SIDEBAR COLLAPSE ====================
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
        const mobileOverlay = document.getElementById('mobile-overlay');
        const isMobile = window.innerWidth <= 768;
        
        if (isCollapsed) {
            sidebar.classList.remove('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', 'false');
            if (isMobile) {
                sidebar.classList.add('sidebar-responsive');
                mobileOverlay.style.display = 'block';
            }
        } else {
            sidebar.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', 'true');
            if (isMobile) {
                mobileOverlay.style.display = 'none';
            }
        }
        
        // Adjust main content margin
        const mainContent = document.querySelector('#main-content');
        if (mainContent) {
            if (isCollapsed) {
                mainContent.style.marginLeft = '0';
            } else {
                mainContent.style.marginLeft = '0';
            }
        }
    }
    
    // Load sidebar state
    function loadSidebarState() {
        const savedState = localStorage.getItem('sidebar_collapsed');
        const sidebar = document.getElementById('sidebar');
        const isMobile = window.innerWidth <= 768;
        
        if (savedState === 'true') {
            sidebar.classList.add('sidebar-collapsed');
        }
        
        if (isMobile) {
            sidebar.classList.add('sidebar-responsive');
            if (savedState === 'true') {
                sidebar.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
            }
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', () => {
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');
        const isMobile = window.innerWidth <= 768;
        const savedState = localStorage.getItem('sidebar_collapsed');
        
        if (isMobile) {
            sidebar.classList.add('sidebar-responsive');
            if (savedState === 'true') {
                sidebar.classList.add('sidebar-collapsed');
                if (mobileOverlay) mobileOverlay.style.display = 'none';
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                if (mobileOverlay) mobileOverlay.style.display = 'block';
            }
        } else {
            sidebar.classList.remove('sidebar-responsive');
            if (mobileOverlay) mobileOverlay.style.display = 'none';
            if (savedState === 'true') {
                sidebar.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
            }
        }
    });
    
    // ==================== THEME ====================
    function toggleTheme() {
        const body = document.body;
        const icon = document.getElementById('theme-icon');
        body.classList.toggle('light-mode');
        const isLight = body.classList.contains('light-mode');
        icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        localStorage.setItem('eli_theme', isLight ? 'light' : 'dark');
        showToast('Theme changed to ' + (isLight ? 'Light' : 'Dark') + ' mode', 'info');
        setMode(currentMode);
        updateSuggestions();
        loadConversations();
    }
    
    // ==================== STATS ====================
    function loadStats() {
        const streak = localStorage.getItem('learning_streak') || 0;
        document.getElementById('streak-count').textContent = streak;
        
        const lessonsDone = localStorage.getItem('lessons_completed') || 0;
        document.getElementById('lessons-done').textContent = lessonsDone;
        
        const flashcardsMastered = localStorage.getItem('flashcards_mastered') || 0;
        document.getElementById('flashcards-mastered').textContent = flashcardsMastered;
    }
    
    function updateStreak() {
        const lastActive = localStorage.getItem('last_active_date');
        const today = new Date().toDateString();
        let streak = parseInt(localStorage.getItem('learning_streak') || 0);
        
        if (lastActive !== today) {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            
            if (lastActive === yesterday.toDateString()) {
                streak++;
            } else {
                streak = 1;
            }
            
            localStorage.setItem('learning_streak', streak);
            localStorage.setItem('last_active_date', today);
            document.getElementById('streak-count').textContent = streak;
        }
    }
    
    // ==================== DAILY QUOTE ====================
    const quotes = [
        "The expert in anything was once a beginner.",
        "Learning is a treasure that will follow its owner everywhere.",
        "The beautiful thing about learning is that no one can take it away from you.",
        "Education is the most powerful weapon to change the world.",
        "The more you learn, the more places you'll go.",
        "Knowledge is power. Information is liberating.",
        "Every expert was once a beginner.",
        "Learning never exhausts the mind."
    ];
    
    function loadDailyQuote() {
        const today = new Date().toDateString();
        let quoteIndex = localStorage.getItem('quote_index');
        const lastQuoteDate = localStorage.getItem('quote_date');
        
        if (lastQuoteDate !== today) {
            quoteIndex = Math.floor(Math.random() * quotes.length);
            localStorage.setItem('quote_index', quoteIndex);
            localStorage.setItem('quote_date', today);
        }
        
        document.getElementById('daily-quote').textContent = '"' + quotes[quoteIndex || 0] + '"';
    }
    
    // ==================== SMART SUGGESTIONS ====================
    const suggestions = {
        ask: [
            "What is artificial intelligence?",
            "Explain quantum computing simply",
            "How do neural networks work?",
            "Difference between SQL and NoSQL?"
        ],
        summarize: [
            "Summarize the key points of machine learning",
            "Give me a brief summary of cloud computing"
        ],
        eli5: [
            "Explain blockchain like I'm 5",
            "What is cryptocurrency? (simple explanation)",
            "How does the internet work?"
        ],
        code: [
            "Explain this code: console.log('Hello')",
            "What is a function in programming?",
            "Explain async/await in JavaScript"
        ]
    };
    
    function updateSuggestions() {
        const container = document.getElementById('suggestions-list');
        const currentSuggestions = suggestions[currentMode];
        
        container.innerHTML = currentSuggestions.map(suggestion => `
            <button onclick="useSuggestion('${escapeHtml(suggestion).replace(/'/g, "\\'")}')" class="suggestion-chip" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); color: var(--text-secondary); border-radius: 20px; cursor: pointer; font-size: 10px;">
                ${escapeHtml(suggestion.length > 35 ? suggestion.substring(0, 35) + '...' : suggestion)}
            </button>
        `).join('');
    }
    
    function useSuggestion(text) {
        document.getElementById('user-input').value = text;
        document.getElementById('char-count').textContent = text.length + ' characters';
        document.getElementById('user-input').focus();
        sendRequest();
    }
    
    // ==================== MODE MANAGEMENT ====================
    function setMode(mode) {
        currentMode = mode;
        
        document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.classList.remove('active-mode');
            btn.style.background = 'transparent';
            btn.style.border = '1px solid var(--border)';
            btn.style.color = 'var(--text-secondary)';
        });
        
        const activeBtn = document.getElementById(`btn-${mode}`);
        activeBtn.classList.add('active-mode');
        activeBtn.style.background = 'linear-gradient(135deg, var(--accent), var(--accent-hover))';
        activeBtn.style.color = 'white';
        activeBtn.style.border = 'none';
        
        document.getElementById('mode-indicator').textContent = `Mode: ${mode.toUpperCase()}`;
        updateSuggestions();
    }
    
    // ==================== CHAT FUNCTIONALITY ====================
    function addWelcomeMessage() {
        const messagesArea = document.getElementById('messages-area');
        messagesArea.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 100%; min-height: 350px;">
                <div style="text-align: center; max-width: 400px;">
                    <div style="width: 70px; height: 70px; border: 2px solid var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; background: linear-gradient(135deg, var(--accent), var(--accent-purple)); border-radius: 20px;">
                        <span class="material-symbols-outlined" style="color: white; font-size: 40px;">auto_awesome</span>
                    </div>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: var(--text-primary);">Welcome to ELI</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 20px;">Your AI-powered learning assistant that makes complex topics simple.</p>
                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                        <button onclick="useSuggestion('What is machine learning?')" class="suggestion-chip" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); border-radius: 20px; cursor: pointer; font-size: 11px;">🤖 ML Basics</button>
                        <button onclick="useSuggestion('Explain neural networks simply')" class="suggestion-chip" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); border-radius: 20px; cursor: pointer; font-size: 11px;">🧠 Neural Nets</button>
                        <button onclick="useSuggestion('What is Python?')" class="suggestion-chip" style="padding: 6px 12px; border: 1px solid var(--border); background-color: var(--bg-tertiary); border-radius: 20px; cursor: pointer; font-size: 11px;">🐍 Python</button>
                    </div>
                </div>
            </div>
        `;
    }
    
    function addMessageToChat(role, content, isHtml = false) {
        const messagesArea = document.getElementById('messages-area');
        
        if (messagesArea.children.length === 1 && messagesArea.children[0].innerHTML.includes('Welcome to ELI') && role) {
            messagesArea.innerHTML = '';
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-animate';
        messageDiv.style.display = 'flex';
        messageDiv.style.justifyContent = role === 'user' ? 'flex-end' : 'flex-start';
        messageDiv.style.marginBottom = '20px';
        
        const icon = role === 'user' ? 'account_circle' : 'smart_toy';
        const bgColor = role === 'user' ? 'var(--accent)' : 'var(--bg-tertiary)';
        const textColor = role === 'user' ? 'white' : 'var(--text-primary)';
        
        let formattedContent = content;
        if (!isHtml && role === 'ai') {
            formattedContent = formatAIResponse(content);
        } else if (!isHtml && role === 'user') {
            formattedContent = escapeHtml(content);
        }
        
        messageDiv.innerHTML = `
            <div style="display: flex; gap: 10px; max-width: 85%; ${role === 'user' ? 'flex-direction: row-reverse;' : 'flex-direction: row;'}">
                <div style="width: 32px; height: 32px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: linear-gradient(135deg, ${role === 'user' ? 'var(--accent), var(--accent-hover)' : 'var(--bg-primary), var(--bg-tertiary)'}); border-radius: 8px;">
                    <span class="material-symbols-outlined" style="color: ${role === 'user' ? 'white' : 'var(--accent)'}; font-size: 18px;">${icon}</span>
                </div>
                <div style="background-color: ${bgColor}; padding: 10px 14px; border-radius: ${role === 'user' ? '16px 16px 4px 16px' : '16px 16px 16px 4px'}; max-width: 100%;">
                    <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 13px; line-height: 1.5; color: ${textColor}; word-wrap: break-word;">${formattedContent}</div>
                    ${role === 'ai' ? `
                        <div style="display: flex; gap: 10px; margin-top: 10px; justify-content: flex-end;">
                            <button onclick="copyToClipboard('${escapeHtml(content).replace(/'/g, "\\'")}')" class="btn-press" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 10px;">📋 Copy</button>
                            <button onclick="regenerateResponse()" class="btn-press" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 10px;">🔄 Regenerate</button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        messagesArea.appendChild(messageDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    function formatAIResponse(text) {
        let formatted = escapeHtml(text);
        formatted = formatted.replace(/```(\w*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>');
        formatted = formatted.replace(/`([^`]+)`/g, '<code style="background-color: var(--bg-primary); padding: 2px 5px; border-radius: 4px;">$1</code>');
        formatted = formatted.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\*([^*]+)\*/g, '<em>$1</em>');
        formatted = formatted.replace(/\n/g, '<br>');
        return formatted;
    }
    
    function addTypingIndicator() {
        const messagesArea = document.getElementById('messages-area');
        const id = 'typing-' + Date.now();
        const typingDiv = document.createElement('div');
        typingDiv.id = id;
        typingDiv.style.display = 'flex';
        typingDiv.style.justifyContent = 'flex-start';
        typingDiv.style.marginBottom = '16px';
        typingDiv.innerHTML = `
            <div style="display: flex; gap: 10px;">
                <div style="width: 32px; height: 32px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--bg-primary), var(--bg-tertiary)); border-radius: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 18px;">smart_toy</span>
                </div>
                <div style="background-color: var(--bg-tertiary); padding: 12px 16px; border-radius: 16px;">
                    <div style="display: flex; gap: 6px;">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>
        `;
        messagesArea.appendChild(typingDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
        return id;
    }
    
    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }
    
    async function sendRequest() {
        const input = document.getElementById('user-input').value;
        if (!input.trim()) {
            showToast('Please enter a message', 'warning');
            return;
        }
        
        addMessageToChat('user', input);
        document.getElementById('user-input').value = '';
        document.getElementById('char-count').textContent = '0 characters';
        updateStreak();
        
        const typingId = addTypingIndicator();
        
        let endpoint = '', body = {};
        if (currentMode === 'ask') { endpoint = '/ask'; body = { question: input }; }
        else if (currentMode === 'summarize') { endpoint = '/summarize'; body = { text: input }; }
        else if (currentMode === 'eli5') { endpoint = '/eli5'; body = { text: input }; }
        else if (currentMode === 'code') { endpoint = '/explain-code'; body = { code: input }; }
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(body)
            });
            
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            const answer = data.answer || data.summary || data.explanation || JSON.stringify(data);
            
            removeTypingIndicator(typingId);
            addMessageToChat('ai', answer);
            loadConversations();
            
        } catch (error) {
            removeTypingIndicator(typingId);
            addMessageToChat('ai', '❌ Error: ' + error.message);
            showToast('Failed to get response from AI', 'error');
        }
    }
    
    function regenerateResponse() {
        showToast('Regenerating response...', 'info');
        sendRequest();
    }
    
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        showToast('Copied to clipboard!', 'success');
    }
    
    // ==================== CONVERSATION HISTORY ====================
    async function loadConversations() {
        try {
            const res = await fetch('/history', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error();
            const history = await res.json();
            conversations = history;
            
            const container = document.getElementById('conversations-list');
            if (!history.length || history.error) {
                container.innerHTML = '<div style="text-align: center; font-size: 10px; padding: 16px; color: var(--text-muted);">No conversations yet</div>';
                return;
            }
            
            container.innerHTML = history.slice(0, 15).map(conv => `
                <div class="conversation-item" style="position: relative; display: flex; flex-direction: column;">
                    <div onclick="loadConversation(${conv.id})" style="border: 1px solid var(--border); background-color: var(--bg-primary); padding: 8px 10px; cursor: pointer; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: ${getModeColor(conv.mode)}; font-size: 16px;">${getModeIcon(conv.mode)}</span>
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-size: 11px; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(conv.user_input.substring(0, 35))}${conv.user_input.length > 35 ? '...' : ''}</p>
                                <p style="font-size: 8px; color: var(--text-muted); margin-top: 2px;">${new Date(conv.created_at).toLocaleString()}</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="deleteConversation(${conv.id}, event)" class="btn-press" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); display: none; border: 1px solid var(--accent-red); background-color: var(--bg-primary); color: var(--accent-red); border-radius: 4px; padding: 2px 6px; cursor: pointer; font-size: 9px;">DEL</button>
                </div>
            `).join('');
            
            document.querySelectorAll('#conversations-list > div').forEach(el => {
                el.addEventListener('mouseenter', () => { const btn = el.querySelector('button'); if (btn) btn.style.display = 'block'; });
                el.addEventListener('mouseleave', () => { const btn = el.querySelector('button'); if (btn) btn.style.display = 'none'; });
            });
            
        } catch(e) { console.error(e); }
    }
    
    function getModeIcon(mode) {
        const icons = { ask: 'help', summarize: 'summarize', eli5: 'child_care', code: 'code' };
        return icons[mode] || 'chat';
    }
    
    function getModeColor(mode) {
        const colors = { ask: 'var(--accent)', summarize: 'var(--accent-green)', eli5: 'var(--accent-yellow)', code: 'var(--accent-purple)' };
        return colors[mode] || 'var(--text-muted)';
    }
    
    async function deleteConversation(id, event) {
        event.stopPropagation();
        if (!confirm('Delete this conversation?')) return;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        await fetch(`/history/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken } });
        loadConversations();
        if (currentConversationId === id) newConversation();
        showToast('Conversation deleted', 'success');
    }
    
    function loadConversation(id) {
        const conv = conversations.find(c => c.id === id);
        if (conv) {
            clearChat();
            addMessageToChat('user', conv.user_input);
            addMessageToChat('ai', conv.ai_response);
            currentConversationId = id;
            setMode(conv.mode);
            showToast('Loaded conversation', 'info');
        }
    }
    
    function clearChat() {
        const messagesArea = document.getElementById('messages-area');
        messagesArea.innerHTML = '';
        currentConversationId = null;
        addWelcomeMessage();
    }
    
    function newConversation() {
        clearChat();
        currentConversationId = null;
        showToast('New conversation started', 'success');
    }
    
    function clearAllHistory() {
        if (confirm('Delete ALL conversation history? This cannot be undone.')) {
            showToast('Feature coming soon', 'info');
        }
    }
    
    // ==================== EXPORT CHAT ====================
    function exportChat() {
        const messages = document.querySelectorAll('#messages-area .message-animate');
        let exportText = `ELI Chat Export\nDate: ${new Date().toLocaleString()}\nMode: ${currentMode.toUpperCase()}\n\n`;
        
        messages.forEach(msg => {
            const role = msg.querySelector('[style*="flex-end"]') ? 'User' : 'ELI';
            const contentDiv = msg.querySelector('[style*="background-color"]');
            if (contentDiv) {
                const content = contentDiv.querySelector('div:first-child')?.innerText || '';
                exportText += `\n[${role}]:\n${content}\n${'='.repeat(50)}\n`;
            }
        });
        
        const blob = new Blob([exportText], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `eli-chat-${new Date().toISOString().slice(0, 19)}.txt`;
        a.click();
        URL.revokeObjectURL(url);
        showToast('Chat exported successfully', 'success');
    }
    
    // ==================== VOICE INPUT ====================
    function setupVoiceRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (SpeechRecognition) {
            recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';
            
            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                document.getElementById('user-input').value = transcript;
                document.getElementById('char-count').textContent = transcript.length + ' characters';
                showToast('Voice input received!', 'success');
            };
            
            recognition.onerror = (event) => {
                showToast('Voice recognition error: ' + event.error, 'error');
            };
        }
    }
    
    function startVoiceInput() {
        if (recognition) {
            recognition.start();
            showToast('Listening... Speak now', 'info');
        } else {
            showToast('Voice input not supported in this browser', 'error');
        }
    }
    
    // ==================== UI HELPERS ====================
    function setupAutoResize() {
        const textarea = document.getElementById('user-input');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            document.getElementById('char-count').textContent = this.value.length + ' characters';
        });
        
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendRequest();
            }
        });
        
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 100) + 'px';
    }
    
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        
        const colors = {
            success: 'var(--accent-green)',
            error: 'var(--accent-red)',
            warning: 'var(--accent-yellow)',
            info: 'var(--accent)'
        };
        
        toast.innerHTML = `
            <div style="background-color: var(--bg-secondary); border-left: 3px solid ${colors[type]}; padding: 10px 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); margin-bottom: 8px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="color: ${colors[type]}; font-size: 18px;">${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}</span>
                    <span style="font-size: 12px; color: var(--text-primary);">${message}</span>
                </div>
            </div>
        `;
        
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // ==================== INITIALIZATION ====================
    document.addEventListener('DOMContentLoaded', () => {
        loadSidebarState();
        setMode('ask');
        addWelcomeMessage();
        loadConversations();
        loadStats();
        loadDailyQuote();
        setupAutoResize();
        setupVoiceRecognition();
        
        // Check for Web Speech API support
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            const micBtn = document.querySelector('[onclick="startVoiceInput()"]');
            if (micBtn) {
                micBtn.style.opacity = '0.5';
                micBtn.style.cursor = 'not-allowed';
            }
        }
    });
</script>
@endsection