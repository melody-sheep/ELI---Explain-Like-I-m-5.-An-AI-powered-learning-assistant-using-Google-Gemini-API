@extends('layouts.app')

@section('content')
<div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
    <!-- Mode Selector -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <button onclick="setMode('ask')" id="btn-ask" class="mode-btn bg-orange-500 text-white p-3 rounded-xl font-semibold transition-all hover:shadow-lg">
            📝 Ask Question
        </button>
        <button onclick="setMode('summarize')" id="btn-summarize" class="mode-btn bg-gray-200 text-gray-700 p-3 rounded-xl font-semibold transition-all hover:shadow-lg">
            📄 Summarize Text
        </button>
        <button onclick="setMode('eli5')" id="btn-eli5" class="mode-btn bg-gray-200 text-gray-700 p-3 rounded-xl font-semibold transition-all hover:shadow-lg">
            🧸 ELI5 Mode
        </button>
        <button onclick="setMode('code')" id="btn-code" class="mode-btn bg-gray-200 text-gray-700 p-3 rounded-xl font-semibold transition-all hover:shadow-lg">
            💻 Explain Code
        </button>
    </div>

    <!-- Input Area -->
    <div class="mb-4">
        <label id="input-label" class="block text-gray-700 font-semibold mb-2">Your Question:</label>
        <textarea id="user-input" rows="5" class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-orange-400" placeholder="Type your question, code, or text here..."></textarea>
        <p class="text-right text-sm text-gray-500 mt-1"><span id="char-count">0</span>/5000 characters</p>
    </div>

    <!-- Submit Button -->
    <button onclick="sendRequest()" id="submit-btn" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white p-3 rounded-xl font-bold text-lg transition-all hover:shadow-lg hover:scale-[1.02]">
        🚀 Get Explanation
    </button>

    <!-- Loading Spinner -->
    <div id="loading" class="hidden text-center py-8">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
        <p class="text-gray-500 mt-2">AI is thinking...</p>
    </div>

    <!-- Response Area -->
    <div id="response-area" class="hidden mt-6">
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-200">
            <h3 class="font-bold text-lg text-green-700 mb-3">🤖 AI Response:</h3>
            <div id="response-text" class="text-gray-700 leading-relaxed whitespace-pre-wrap"></div>
            <div class="flex gap-3 mt-4">
                <button onclick="copyResponse()" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600">📋 Copy</button>
                <button onclick="clearAll()" class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-600">🗑️ Clear</button>
            </div>
        </div>
    </div>
</div>

<!-- History Section -->
<div class="bg-white rounded-2xl shadow-xl p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">📜 Recent History</h2>
    <div id="history-list" class="space-y-3 max-h-96 overflow-y-auto">
        <p class="text-gray-500 text-center">Loading history...</p>
    </div>
</div>

<script>
    let currentMode = 'ask';

    function setMode(mode) {
        currentMode = mode;
        document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.classList.remove('bg-orange-500', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        const activeBtn = document.getElementById(`btn-${mode}`);
        activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
        activeBtn.classList.add('bg-orange-500', 'text-white');
        
        const labels = { ask: 'Your Question:', summarize: 'Text to Summarize:', eli5: 'Complex Text to Simplify:', code: 'Code to Explain:' };
        document.getElementById('input-label').textContent = labels[mode];
        document.getElementById('user-input').placeholder = mode === 'code' ? 'Paste your code here...' : 'Type your text here...';
    }

    document.getElementById('user-input').addEventListener('input', function() {
        document.getElementById('char-count').textContent = this.value.length;
    });

    async function sendRequest() {
        const input = document.getElementById('user-input').value;
        if (!input.trim()) { 
            alert('Please enter something!'); 
            return; 
        }

        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('response-area').classList.add('hidden');
        document.getElementById('submit-btn').disabled = true;

        // Determine the endpoint and body key
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
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken 
                },
                body: JSON.stringify(body)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP ${response.status}`);
            }
            
            const data = await response.json();
            const answer = data.answer || data.summary || data.explanation || JSON.stringify(data);
            document.getElementById('response-text').innerHTML = answer.replace(/\n/g, '<br>');
            document.getElementById('response-area').classList.remove('hidden');
            loadHistory();
            document.getElementById('user-input').value = '';
            document.getElementById('char-count').textContent = '0';
        } catch (error) { 
            console.error('Error:', error);
            alert('Error: ' + error.message); 
        } finally { 
            document.getElementById('loading').classList.add('hidden'); 
            document.getElementById('submit-btn').disabled = false; 
        }
    }

    async function loadHistory() {
        try {
            const response = await fetch('/history', {
                headers: { 'Accept': 'application/json' }
            });
            
            if (!response.ok) {
                throw new Error('Failed to load history');
            }
            
            const history = await response.json();
            const container = document.getElementById('history-list');
            
            if (!history.length || (history.error && !Array.isArray(history))) { 
                container.innerHTML = '<p class="text-gray-500 text-center">No history yet. Ask something!</p>'; 
                return; 
            }
            
            container.innerHTML = history.map(h => `
                <div class="border-l-4 border-orange-400 bg-gray-50 p-3 rounded-lg">
                    <div class="font-semibold text-orange-600">${h.mode?.toUpperCase() || 'Unknown'}</div>
                    <div class="text-sm text-gray-600 mt-1">${(h.user_input || '').substring(0, 100)}${(h.user_input || '').length > 100 ? '...' : ''}</div>
                    <div class="text-xs text-gray-400 mt-1">${h.created_at ? new Date(h.created_at).toLocaleString() : 'Unknown date'}</div>
                </div>
            `).join('');
        } catch (error) { 
            console.error('Error loading history:', error);
            document.getElementById('history-list').innerHTML = '<p class="text-gray-500 text-center">Error loading history</p>';
        }
    }

    function copyResponse() { 
        const text = document.getElementById('response-text').innerText || document.getElementById('response-text').textContent;
        navigator.clipboard.writeText(text); 
        alert('Copied to clipboard!'); 
    }
    
    function clearAll() { 
        document.getElementById('user-input').value = ''; 
        document.getElementById('response-area').classList.add('hidden'); 
        document.getElementById('char-count').textContent = '0'; 
    }

    // Load history on page load
    loadHistory();
</script>
@endsection