@extends('layouts.app')

@section('content')
<!-- Sidebar - Sharp edges, proper spacing -->
<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border);">

    <!-- Logo Section -->
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary);">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">face_4</span>
                </div>
                <div>
                    <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">ELI</span>
                    <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">EXPLAIN LIKE I'M 5</p>
                </div>
            </div>
            <button onclick="toggleTheme()" style="width: 32px; height: 32px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); cursor: pointer;">
                <span id="theme-icon" class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 18px;">dark_mode</span>
            </button>
        </div>
    </div>

    <!-- New Chat Button -->
    <div style="padding: 16px;">
        <button onclick="newConversation()" style="width: 100%; border: 1px solid var(--accent); background-color: transparent; color: var(--accent); font-family: monospace; font-size: 13px; font-weight: 600; padding: 10px; display: flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer;">
            <span class="material-symbols-outlined" style="font-size: 18px;">edit_note</span>
            NEW CHAT
        </button>
    </div>

    <!-- Quick Actions -->
    <div style="padding: 8px 16px;">
        <div style="border-top: 1px solid var(--border); padding-top: 16px;">
            <p style="font-size: 10px; font-weight: 600; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 12px;">QUICK ACTIONS</p>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <button onclick="quickAction('review')" style="width: 100%; border: 1px solid var(--accent-green); background-color: transparent; color: var(--accent-green); font-family: monospace; font-size: 11px; padding: 8px 12px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">menu_book</span>
                    Review Lessons
                </button>
                <button onclick="quickAction('flashcard')" style="width: 100%; border: 1px solid var(--accent-yellow); background-color: transparent; color: var(--accent-yellow); font-family: monospace; font-size: 11px; padding: 8px 12px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">auto_stories</span>
                    Flashcards
                </button>
                <button onclick="quickAction('quiz')" style="width: 100%; border: 1px solid var(--accent-purple); background-color: transparent; color: var(--accent-purple); font-family: monospace; font-size: 11px; padding: 8px 12px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">quiz</span>
                    Practice Quiz
                </button>
            </div>
        </div>
    </div>

    <!-- History -->
    <div style="flex: 1; overflow-y: auto; padding: 8px 16px;">
        <div style="border-top: 1px solid var(--border); padding-top: 16px;">
            <p style="font-size: 10px; font-weight: 600; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 12px;">RECENT</p>
            <div id="conversations-list" style="display: flex; flex-direction: column; gap: 6px;"></div>
        </div>
    </div>

    <!-- User -->
    <div style="padding: 12px 16px; border-top: 1px solid var(--border);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 28px; height: 28px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary);">
                <span class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 16px;">account_circle</span>
            </div>
            <div style="flex: 1;">
                <p style="font-size: 11px; font-weight: 500; color: var(--text-secondary);">GUEST</p>
                <p style="font-size: 9px; color: var(--text-muted);">AI Assistant</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Chat Area -->
<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary);">

    <!-- Header -->
    <div style="padding: 16px 24px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h1 style="font-size: 16px; font-weight: 700; font-family: monospace; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">smart_toy</span>
                    AI ASSISTANT
                </h1>
                <p style="font-size: 9px; font-family: monospace; color: var(--text-muted); margin-top: 4px;">POWERED BY GEMINI AI</p>
            </div>
            <button onclick="clearChat()" style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1px solid var(--accent-red); background-color: transparent; color: var(--accent-red); font-family: monospace; font-size: 10px; cursor: pointer;">
                <span class="material-symbols-outlined" style="font-size: 14px;">delete_sweep</span>
                CLEAR
            </button>
        </div>
    </div>

    <!-- Messages Area -->
    <div id="messages-area" style="flex: 1; overflow-y: auto; padding: 20px 24px;"></div>

    <!-- Input Area -->
    <div style="border-top: 1px solid var(--border); background-color: var(--bg-primary);">
        
        <!-- Mode Buttons -->
        <div style="padding: 12px 24px 0 24px;">
            <div style="display: flex; gap: 8px;">
                <button onclick="setMode('ask')" id="btn-ask" class="mode-btn" style="border: 1px solid var(--accent); background-color: var(--accent); color: white; font-family: monospace; font-size: 11px; font-weight: 600; padding: 6px 14px; cursor: pointer;">ASK</button>
                <button onclick="setMode('summarize')" id="btn-summarize" class="mode-btn" style="border: 1px solid var(--border); background-color: transparent; color: var(--text-secondary); font-family: monospace; font-size: 11px; padding: 6px 14px; cursor: pointer;">SUMMARIZE</button>
                <button onclick="setMode('eli5')" id="btn-eli5" class="mode-btn" style="border: 1px solid var(--border); background-color: transparent; color: var(--text-secondary); font-family: monospace; font-size: 11px; padding: 6px 14px; cursor: pointer;">ELI5</button>
                <button onclick="setMode('code')" id="btn-code" class="mode-btn" style="border: 1px solid var(--border); background-color: transparent; color: var(--text-secondary); font-family: monospace; font-size: 11px; padding: 6px 14px; cursor: pointer;">CODE</button>
            </div>
        </div>

        <!-- Input Field -->
        <div style="padding: 12px 24px 20px 24px;">
            <div style="display: flex; gap: 12px;">
                <textarea id="user-input" rows="1" style="flex: 1; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); font-family: monospace; font-size: 12px; padding: 10px 12px; resize: none; outline: none;" placeholder="Type your message..."></textarea>
                <button onclick="sendRequest()" id="submit-btn" style="border: 1px solid var(--accent); background-color: var(--accent); color: white; font-family: monospace; font-size: 12px; font-weight: 600; padding: 0 20px; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">send</span>
                    SEND
                </button>
            </div>
            <div style="text-align: right; margin-top: 6px;">
                <span id="char-count" style="font-size: 9px; font-family: monospace; color: var(--text-muted);">0</span>
            </div>
        </div>
    </div>
</div>

<script>
    let currentMode = 'ask';
    let currentConversationId = null;
    let conversations = [];

    function toggleTheme() {
        const body = document.body;
        const icon = document.getElementById('theme-icon');
        body.classList.toggle('light-mode');
        const isLight = body.classList.contains('light-mode');
        icon.textContent = isLight ? 'light_mode' : 'dark_mode';
        localStorage.setItem('eli_theme', isLight ? 'light' : 'dark');
        refreshAllComponents();
    }

    function refreshAllComponents() {
        loadConversations();
        setMode(currentMode);
    }

    function setMode(mode) {
        currentMode = mode;
        document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.style.border = '1px solid var(--border)';
            btn.style.backgroundColor = 'transparent';
            btn.style.color = 'var(--text-secondary)';
        });
        const activeBtn = document.getElementById(`btn-${mode}`);
        if (activeBtn) {
            activeBtn.style.border = '1px solid var(--accent)';
            activeBtn.style.backgroundColor = 'var(--accent)';
            activeBtn.style.color = 'white';
        }
    }

    function quickAction(action) {
        let message = '';
        if (action === 'review') message = 'Help me review my lessons. What should I focus on?';
        if (action === 'flashcard') message = 'Generate flashcards from my lessons.';
        if (action === 'quiz') message = 'Create a practice quiz for me.';
        document.getElementById('user-input').value = message;
        document.getElementById('char-count').textContent = message.length;
        sendRequest();
    }

    const textarea = document.getElementById('user-input');
    textarea.addEventListener('input', function() {
        document.getElementById('char-count').textContent = this.value.length;
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendRequest();
        }
    });

    async function sendRequest() {
        const input = document.getElementById('user-input').value;
        if (!input.trim()) return;

        addMessageToChat('user', input);
        document.getElementById('user-input').value = '';
        document.getElementById('char-count').textContent = '0';
        textarea.style.height = 'auto';

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
            addMessageToChat('ai', 'ERROR: ' + error.message);
        }
    }

    function addMessageToChat(role, content) {
        const messagesArea = document.getElementById('messages-area');
        const messageDiv = document.createElement('div');
        messageDiv.style.display = 'flex';
        messageDiv.style.justifyContent = role === 'user' ? 'flex-end' : 'flex-start';
        messageDiv.style.marginBottom = '20px';
        
        const icon = role === 'user' ? 'account_circle' : 'smart_toy';
        const borderColor = role === 'user' ? 'var(--accent)' : 'var(--border)';
        const bgColor = role === 'user' ? 'var(--accent)' : 'var(--bg-tertiary)';
        const textColor = role === 'user' ? 'white' : 'var(--text-primary)';
        
        messageDiv.innerHTML = `
            <div style="display: flex; gap: 12px; max-width: 80%; ${role === 'user' ? 'flex-direction: row-reverse;' : 'flex-direction: row;'}">
                <div style="width: 32px; height: 32px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; background-color: var(--bg-primary);">
                    <span class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 18px;">${icon}</span>
                </div>
                <div style="border: 1px solid ${borderColor}; background-color: ${bgColor}; padding: 10px 14px;">
                    <div style="font-family: monospace; font-size: 12px; white-space: pre-wrap; line-height: 1.5; color: ${textColor};">${escapeHtml(content).replace(/\n/g, '<br>')}</div>
                </div>
            </div>
        `;
        messagesArea.appendChild(messageDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function addTypingIndicator() {
        const messagesArea = document.getElementById('messages-area');
        const id = 'typing-' + Date.now();
        const typingDiv = document.createElement('div');
        typingDiv.id = id;
        typingDiv.style.display = 'flex';
        typingDiv.style.justifyContent = 'flex-start';
        typingDiv.style.marginBottom = '20px';
        typingDiv.innerHTML = `
            <div style="display: flex; gap: 12px;">
                <div style="width: 32px; height: 32px; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary);">
                    <span class="material-symbols-outlined" style="color: var(--text-secondary); font-size: 18px;">smart_toy</span>
                </div>
                <div style="border: 1px solid var(--border); background-color: var(--bg-tertiary); padding: 10px 14px;">
                    <div style="display: flex; gap: 4px;">
                        <div style="width: 6px; height: 6px; background-color: var(--accent); animation: bounce 0.6s infinite;"></div>
                        <div style="width: 6px; height: 6px; background-color: var(--accent); animation: bounce 0.6s infinite 0.2s;"></div>
                        <div style="width: 6px; height: 6px; background-color: var(--accent); animation: bounce 0.6s infinite 0.4s;"></div>
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

    async function loadConversations() {
        try {
            const res = await fetch('/history', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error();
            const history = await res.json();
            conversations = history;
            const container = document.getElementById('conversations-list');
            if (!history.length || history.error) {
                container.innerHTML = '<div style="text-align: center; font-size: 10px; font-family: monospace; padding: 20px; color: var(--text-muted);">NO CONVERSATIONS</div>';
                return;
            }
            container.innerHTML = history.map(conv => `
                <div style="position: relative; display: flex; flex-direction: column;">
                    <div onclick="loadConversation(${conv.id})" style="border: 1px solid var(--border); background-color: var(--bg-primary); padding: 8px 10px; cursor: pointer;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-outlined" style="color: var(--text-muted); font-size: 14px;">${getModeIcon(conv.mode)}</span>
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-size: 11px; font-family: monospace; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(conv.user_input.substring(0, 35))}${conv.user_input.length > 35 ? '...' : ''}</p>
                                <p style="font-size: 9px; font-family: monospace; color: var(--text-muted); margin-top: 4px;">${new Date(conv.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="deleteConversation(${conv.id}, event)" style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); display: none; border: 1px solid var(--accent-red); background-color: var(--bg-primary); color: var(--accent-red); font-family: monospace; font-size: 9px; padding: 2px 6px; cursor: pointer;">DEL</button>
                </div>
            `).join('');
            document.querySelectorAll('#conversations-list .group').forEach(el => {
                el.addEventListener('mouseenter', () => { el.querySelector('button').style.display = 'block'; });
                el.addEventListener('mouseleave', () => { el.querySelector('button').style.display = 'none'; });
            });
        } catch(e) { console.error(e); }
    }

    function getModeIcon(mode) {
        const icons = { ask: 'help', summarize: 'summarize', eli5: 'child_care', code: 'code' };
        return icons[mode] || 'chat';
    }

    async function deleteConversation(id, event) {
        event.stopPropagation();
        if (!confirm('DELETE?')) return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        await fetch(`/history/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken } });
        loadConversations();
        if (currentConversationId === id) newConversation();
    }

    function loadConversation(id) {
        const conv = conversations.find(c => c.id === id);
        if (conv) {
            clearChat();
            addMessageToChat('user', conv.user_input);
            addMessageToChat('ai', conv.ai_response);
            currentConversationId = id;
        }
    }

    function clearChat() {
        const messagesArea = document.getElementById('messages-area');
        messagesArea.innerHTML = '';
        currentConversationId = null;
        addWelcomeMessage();
    }

    function addWelcomeMessage() {
        const messagesArea = document.getElementById('messages-area');
        messagesArea.innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 100%; min-height: 300px;">
                <div style="border: 1px solid var(--border); background-color: var(--bg-secondary); padding: 32px 40px; text-align: center;">
                    <div style="width: 64px; height: 64px; border: 1px solid var(--accent); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; background-color: var(--bg-primary);">
                        <span class="material-symbols-outlined" style="color: var(--accent); font-size: 36px;">face_4</span>
                    </div>
                    <h3 style="font-size: 14px; font-family: monospace; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">WELCOME TO ELI</h3>
                    <p style="font-size: 11px; font-family: monospace; color: var(--text-muted);">Ask me anything. I make complex topics simple.</p>
                </div>
            </div>
        `;
    }

    function newConversation() { clearChat(); currentConversationId = null; }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize
    setMode('ask');
    addWelcomeMessage();
    loadConversations();

    // Animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
    `;
    document.head.appendChild(style);
</script>
@endsection