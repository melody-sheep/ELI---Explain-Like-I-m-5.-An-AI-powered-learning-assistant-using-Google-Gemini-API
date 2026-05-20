@extends('layouts.app')

@section('content')
<!-- Sidebar -->
<div class="w-80 bg-zinc-900 border-r border-zinc-800 flex flex-col h-full">
    <!-- Logo -->
    <div class="p-5 border-b border-zinc-800">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                <span class="material-symbols-outlined text-white text-2xl">psychology</span>
            </div>
            <div>
                <span class="text-xl font-bold text-white tracking-tight">ELI</span>
                <p class="text-xs text-gray-500">Explain Like I'm 5</p>
            </div>
        </div>
    </div>

    <!-- New Chat -->
    <div class="p-4">
        <button onclick="newConversation()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-600/20">
            <span class="material-symbols-outlined text-lg">edit_note</span>
            New Chat
        </button>
    </div>

    <!-- Conversations -->
    <div class="flex-1 overflow-y-auto px-3 space-y-2">
        <div class="px-3 py-2">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-gray-500 text-sm">history</span>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Recent Conversations</h3>
            </div>
        </div>
        <div id="conversations-list" class="space-y-1">
            <!-- Dynamic content -->
        </div>
    </div>

    <!-- User -->
    <div class="p-4 border-t border-zinc-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-gray-400">account_circle</span>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-white">Guest User</p>
                <p class="text-xs text-gray-500">AI Learning Assistant</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Area -->
<div class="flex-1 flex flex-col bg-black">
    <!-- Header -->
    <div class="border-b border-zinc-800 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-500">smart_toy</span>
                    AI Assistant
                </h1>
                <p class="text-sm text-gray-400">Powered by Google Gemini AI</p>
            </div>
            <button onclick="clearChat()" class="flex items-center gap-2 px-3 py-2 text-sm bg-zinc-800 hover:bg-zinc-700 rounded-lg transition-all text-gray-300">
                <span class="material-symbols-outlined text-sm">delete_sweep</span>
                Clear Chat
            </button>
        </div>
    </div>

    <!-- Messages -->
    <div id="messages-area" class="flex-1 overflow-y-auto p-6 space-y-4">
        <div class="flex justify-center">
            <div class="bg-gradient-to-br from-zinc-900 to-zinc-900 rounded-2xl px-8 py-6 max-w-2xl text-center border border-zinc-800">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <span class="material-symbols-outlined text-white text-3xl">psychology</span>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Welcome to ELI</h3>
                <p class="text-gray-400 text-sm">Ask me anything! I explain complex topics in simple terms.</p>
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="border-t border-zinc-800 bg-zinc-900/50">
        <!-- Mode Selector -->
        <div class="px-6 pt-4">
            <div class="flex gap-2 justify-start">
                <button onclick="setMode('ask')" id="btn-ask" class="mode-btn flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all shadow-lg shadow-blue-600/20">
                    <span class="material-symbols-outlined text-base">help</span>
                    Ask
                </button>
                <button onclick="setMode('summarize')" id="btn-summarize" class="mode-btn flex items-center gap-2 bg-zinc-800 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-zinc-700">
                    <span class="material-symbols-outlined text-base">summarize</span>
                    Summarize
                </button>
                <button onclick="setMode('eli5')" id="btn-eli5" class="mode-btn flex items-center gap-2 bg-zinc-800 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-zinc-700">
                    <span class="material-symbols-outlined text-base">child_care</span>
                    ELI5
                </button>
                <button onclick="setMode('code')" id="btn-code" class="mode-btn flex items-center gap-2 bg-zinc-800 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-zinc-700">
                    <span class="material-symbols-outlined text-base">code</span>
                    Code
                </button>
            </div>
        </div>

        <!-- Input -->
        <div class="p-6 pt-3">
            <div class="flex gap-3 items-end">
                <div class="flex-1 relative">
                    <textarea id="user-input" rows="1" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none transition-all" placeholder="Type your message here..."></textarea>
                    <div class="absolute bottom-3 right-3">
                        <span id="char-count" class="text-xs text-gray-500">0</span>
                    </div>
                </div>
                <button onclick="sendRequest()" id="submit-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">send</span>
                    Send
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentMode = 'ask';
    let currentConversationId = null;
    let conversations = [];

    function setMode(mode) {
        currentMode = mode;
        document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-600/20');
            btn.classList.add('bg-zinc-800', 'text-gray-300');
        });
        const activeBtn = document.getElementById(`btn-${mode}`);
        activeBtn.classList.remove('bg-zinc-800', 'text-gray-300');
        activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-600/20');
    }

    document.getElementById('user-input').addEventListener('input', function() {
        document.getElementById('char-count').textContent = this.value.length;
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });

    document.getElementById('user-input').addEventListener('keydown', function(e) {
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
        document.getElementById('user-input').style.height = 'auto';

        const typingId = addTypingIndicator();

        let endpoint = '';
        let body = {};
        
        if (currentMode === 'ask') {
            endpoint = '/ask';
            body = { question: input };
        } else if (currentMode === 'summarize') {
            endpoint = '/summarize';
            body = { text: input };
        } else if (currentMode === 'eli5') {
            endpoint = '/eli5';
            body = { text: input };
        } else if (currentMode === 'code') {
            endpoint = '/explain-code';
            body = { code: input };
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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
        }
    }

    function addMessageToChat(role, content) {
        const messagesArea = document.getElementById('messages-area');
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'} animate-fadeIn`;
        
        const icon = role === 'user' ? 'account_circle' : 'smart_toy';
        const bgColor = role === 'user' ? 'bg-blue-600' : 'bg-zinc-800';
        
        messageDiv.innerHTML = `
            <div class="flex gap-3 max-w-3xl ${role === 'user' ? 'flex-row-reverse' : 'flex-row'}">
                <div class="w-8 h-8 ${role === 'user' ? 'bg-blue-500' : 'bg-zinc-700'} rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-sm">${icon}</span>
                </div>
                <div class="${bgColor} rounded-2xl px-4 py-3">
                    <div class="text-sm whitespace-pre-wrap leading-relaxed">${escapeHtml(content).replace(/\n/g, '<br>')}</div>
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
        typingDiv.className = 'flex justify-start animate-fadeIn';
        typingDiv.innerHTML = `
            <div class="flex gap-3">
                <div class="w-8 h-8 bg-zinc-700 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-sm">smart_toy</span>
                </div>
                <div class="bg-zinc-800 rounded-2xl px-4 py-3">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            </div>
        `;
        messagesArea.appendChild(typingDiv);
        messagesArea.scrollTop = messagesArea.scrollHeight;
        return id;
    }

    function removeTypingIndicator(id) {
        const element = document.getElementById(id);
        if (element) element.remove();
    }

    async function loadConversations() {
        try {
            const response = await fetch('/history', { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error();
            const history = await response.json();
            conversations = history;
            
            const container = document.getElementById('conversations-list');
            if (!history.length || history.error) {
                container.innerHTML = '<div class="text-center text-gray-500 text-sm py-8">No conversations yet</div>';
                return;
            }
            
            container.innerHTML = history.map(conv => `
                <div class="group relative">
                    <div onclick="loadConversation(${conv.id})" class="bg-zinc-800/50 hover:bg-zinc-800 rounded-xl p-3 cursor-pointer transition-all border border-transparent hover:border-zinc-700">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-sm text-gray-500">${getModeIcon(conv.mode)}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white truncate">${escapeHtml(conv.user_input.substring(0, 45))}${conv.user_input.length > 45 ? '...' : ''}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500">${new Date(conv.created_at).toLocaleDateString()}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button onclick="deleteConversation(${conv.id}, event)" class="absolute right-2 top-1/2 -translate-y-1/2 hidden group-hover:flex bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-lg transition-all">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            `).join('');
        } catch (error) { console.error(error); }
    }

    function getModeIcon(mode) {
        const icons = { ask: 'help', summarize: 'summarize', eli5: 'child_care', code: 'code' };
        return icons[mode] || 'chat';
    }

    async function deleteConversation(id, event) {
        event.stopPropagation();
        if (!confirm('Delete this conversation?')) return;
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            await fetch(`/history/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken } });
            loadConversations();
            if (currentConversationId === id) newConversation();
        } catch (error) { alert('Error deleting conversation'); }
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
        document.getElementById('messages-area').innerHTML = '';
        currentConversationId = null;
    }

    function newConversation() {
        clearChat();
        const messagesArea = document.getElementById('messages-area');
        messagesArea.innerHTML = `
            <div class="flex justify-center">
                <div class="bg-gradient-to-br from-zinc-900 to-zinc-900 rounded-2xl px-8 py-6 max-w-2xl text-center border border-zinc-800">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="material-symbols-outlined text-white text-3xl">psychology</span>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">New Conversation</h3>
                    <p class="text-gray-400 text-sm">Ask me anything! I explain complex topics in simple terms.</p>
                </div>
            </div>
        `;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    loadConversations();

    // Animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
        .animate-bounce { animation: bounce 0.6s infinite; }
    `;
    document.head.appendChild(style);
</script>
@endsection