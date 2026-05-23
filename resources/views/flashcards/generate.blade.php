@extends('layouts.app')

@section('content')
<style>
    .upload-area {
        border: 2px dashed var(--border);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .upload-area:hover, .upload-area.drag-over {
        border-color: var(--accent-yellow);
        background-color: rgba(234, 179, 8, 0.05);
    }
    .setting-card {
        transition: all 0.2s ease;
    }
    .setting-card:hover {
        border-color: var(--accent-yellow);
    }
    
    /* Error and success message styles */
    .alert-error {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--accent-red);
        color: var(--accent-red);
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .alert-success {
        background-color: rgba(34, 197, 94, 0.1);
        border: 1px solid var(--accent-green);
        color: var(--accent-green);
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .alert-warning {
        background-color: rgba(234, 179, 8, 0.1);
        border: 1px solid var(--accent-yellow);
        color: var(--accent-yellow);
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div style="width: 320px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border); overflow-y: auto;">
    <div style="padding: 20px; border-bottom: 1px solid var(--border);">
        <a href="{{ route('flashcards.index') }}" style="text-decoration: none; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border: 1px solid var(--border-light); display: flex; align-items: center; justify-content: center; background-color: var(--bg-primary); border-radius: 8px;">
                <span class="material-symbols-outlined" style="color: var(--accent); font-size: 22px;">arrow_back</span>
            </div>
            <div>
                <span style="font-size: 18px; font-weight: 700; color: var(--text-primary);">BACK</span>
                <p style="font-size: 9px; letter-spacing: 1px; color: var(--text-muted);">TO FLASHCARDS</p>
            </div>
        </a>
    </div>

    <div style="padding: 20px;">
        <div style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 12px; padding: 20px; border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 32px;">auto_awesome</span>
                <div>
                    <p style="font-size: 11px; color: var(--text-muted);">AI Generation</p>
                    <p style="font-size: 14px; font-weight: 600; color: var(--text-primary);">Smart Flashcards</p>
                </div>
            </div>
            <p style="font-size: 11px; color: var(--text-secondary); line-height: 1.5;">Upload PDF, DOCX, or TXT files. AI will analyze content and generate smart flashcards automatically.</p>
        </div>
    </div>
</div>

<div style="flex: 1; display: flex; flex-direction: column; background-color: var(--bg-primary); overflow-y: auto;">
    <div style="max-width: 600px; margin: 40px auto; padding: 24px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 48px;">auto_awesome</span>
            <h1 style="font-size: 28px; font-weight: 700; margin-top: 16px; color: var(--text-primary);">Generate Flashcards</h1>
            <p style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">Upload any document and our AI will create smart flashcards</p>
        </div>

        <!-- Display validation errors -->
        @if($errors->any())
            <div class="alert-error">
                <span class="material-symbols-outlined">error</span>
                <div style="flex: 1;">
                    <strong style="display: block; margin-bottom: 4px;">Validation Error:</strong>
                    @foreach($errors->all() as $error)
                        <p style="font-size: 12px; margin: 2px 0;">• {{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Display session error messages -->
        @if(session('error'))
            <div class="alert-error" id="error-message">
                <span class="material-symbols-outlined">error</span>
                <div style="flex: 1;">
                    <strong style="display: block; margin-bottom: 4px;">Error:</strong>
                    <p style="font-size: 13px; margin: 0;">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        @endif

        <!-- Display session success messages -->
        @if(session('success'))
            <div class="alert-success" id="success-message">
                <span class="material-symbols-outlined">check_circle</span>
                <div style="flex: 1;">
                    <strong style="display: block; margin-bottom: 4px;">Success:</strong>
                    <p style="font-size: 13px; margin: 0;">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer;">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        @endif

        <!-- Warning message for common issues -->
        <div class="alert-warning" style="display: none;" id="warning-message">
            <span class="material-symbols-outlined">warning</span>
            <div style="flex: 1;">
                <strong style="display: block; margin-bottom: 4px;">Notice:</strong>
                <p style="font-size: 13px; margin: 0;" id="warning-text"></p>
            </div>
            <button onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: inherit; cursor: pointer;">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="upload-form" action="{{ route('flashcards.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- File Upload Area -->
            <div class="upload-area" id="upload-area" onclick="document.getElementById('file-input').click()" style="padding: 60px 40px; text-align: center; border-radius: 16px; margin-bottom: 24px;">
                <span class="material-symbols-outlined" style="color: var(--accent-yellow); font-size: 64px;">cloud_upload</span>
                <p style="font-size: 14px; color: var(--text-secondary); margin-top: 12px;">Click or drag file to upload</p>
                <p style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">Supports: PDF, DOC, DOCX, TXT (Max 10MB)</p>
                <input type="file" name="file" id="file-input" accept=".pdf,.doc,.docx,.txt" style="display: none;" onchange="previewFile(this)">
            </div>

            <!-- File Preview -->
            <div id="file-preview" style="display: none; border: 1px solid var(--accent-green); background-color: rgba(34, 197, 94, 0.1); padding: 16px; border-radius: 12px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="material-symbols-outlined" style="color: var(--accent-green); font-size: 32px;">description</span>
                    <div style="flex: 1;">
                        <p id="file-name" style="font-size: 13px; color: var(--text-primary); font-weight: 500;"></p>
                        <p id="file-size" style="font-size: 10px; color: var(--text-muted);"></p>
                        <p id="file-warning" style="font-size: 10px; color: var(--accent-yellow); display: none;"></p>
                    </div>
                    <button type="button" onclick="removeFile()" style="background: none; border: none; color: var(--accent-red); cursor: pointer;">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <!-- Generation Options -->
            <div style="background-color: var(--bg-secondary); border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px;">Generation Options</h3>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">NUMBER OF FLASHCARDS</label>
                    <input type="range" name="num_cards" id="num-cards" min="5" max="30" value="10" step="1" style="width: 100%;">
                    <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                        <span style="font-size: 10px; color: var(--text-muted);">5</span>
                        <span id="cards-value" style="font-size: 11px; color: var(--accent-yellow);">10 cards</span>
                        <span style="font-size: 10px; color: var(--text-muted);">30</span>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">ASSIGN TO LESSON (OPTIONAL)</label>
                    <select name="lesson_id" style="width: 100%; background-color: var(--bg-tertiary); border: 1px solid var(--border); color: var(--text-primary); padding: 10px; border-radius: 8px;">
                        <option value="">No lesson (standalone)</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" id="submit-btn" disabled style="width: 100%; border: none; background: linear-gradient(135deg, var(--accent-yellow), var(--accent-orange)); color: black; padding: 14px; cursor: pointer; font-size: 14px; font-weight: 600; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span class="material-symbols-outlined">auto_awesome</span>
                GENERATE FLASHCARDS
            </button>
        </form>

        <!-- Loading Indicator -->
        <div id="loading" style="display: none; text-align: center; margin-top: 24px;">
            <div style="display: inline-block; width: 40px; height: 40px; border: 3px solid var(--border); border-top-color: var(--accent-yellow); border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p id="loading-message" style="color: var(--text-muted); margin-top: 12px;">Processing file and generating flashcards...</p>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">This may take 30-60 seconds depending on file size</p>
        </div>

        <!-- Debug Info (Hidden by default, shown on error) -->
        <div id="debug-info" style="display: none; margin-top: 24px; padding: 16px; background-color: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; font-family: monospace; font-size: 11px;">
            <strong style="color: var(--text-primary);">Debug Information:</strong>
            <pre id="debug-content" style="margin-top: 8px; color: var(--text-secondary); overflow-x: auto;"></pre>
        </div>
    </div>
</div>

<script>
    // Range input display
    const rangeInput = document.getElementById('num-cards');
    const cardsValue = document.getElementById('cards-value');
    
    rangeInput.addEventListener('input', function() {
        cardsValue.textContent = this.value + ' cards';
    });
    
    // File preview function
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            // Validate file type
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
            const fileExt = file.name.split('.').pop().toLowerCase();
            
            if (!['pdf', 'doc', 'docx', 'txt'].includes(fileExt)) {
                alert('❌ Invalid file type. Please upload PDF, DOC, DOCX, or TXT files only.');
                removeFile();
                return false;
            }
            
            // Validate file size (10MB = 10485760 bytes)
            if (file.size > 10485760) {
                alert('❌ File too large. Maximum size is 10MB. Your file is ' + (file.size / 1048576).toFixed(2) + 'MB');
                removeFile();
                return false;
            }
            
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
            
            // Show warning for large files
            if (file.size > 5242880) { // >5MB
                const warningEl = document.getElementById('file-warning');
                warningEl.textContent = '⚠️ Large file detected. Processing may take longer than usual.';
                warningEl.style.display = 'block';
            } else {
                document.getElementById('file-warning').style.display = 'none';
            }
            
            document.getElementById('file-preview').style.display = 'block';
            document.getElementById('submit-btn').disabled = false;
            document.querySelector('.upload-area').style.borderColor = 'var(--accent-green)';
            
            // Hide any previous error messages
            const errorMsg = document.getElementById('error-message');
            if (errorMsg) errorMsg.style.display = 'none';
            
            console.log('File selected:', file.name, 'Size:', (file.size / 1024).toFixed(2), 'KB', 'Type:', file.type);
        }
    }
    
    function removeFile() {
        document.getElementById('file-input').value = '';
        document.getElementById('file-preview').style.display = 'none';
        document.getElementById('submit-btn').disabled = true;
        document.querySelector('.upload-area').style.borderColor = 'var(--border)';
        document.getElementById('file-warning').style.display = 'none';
    }
    
    // Drag and drop
    const uploadArea = document.getElementById('upload-area');
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file && ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'].includes(file.type)) {
            document.getElementById('file-input').files = e.dataTransfer.files;
            previewFile(document.getElementById('file-input'));
        } else {
            alert('❌ Please upload a valid PDF, DOC, DOCX, or TXT file.');
        }
    });
    
    // Form submission with enhanced error handling
    document.getElementById('upload-form').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('file-input');
        
        // Validate file exists
        if (!fileInput.files || !fileInput.files[0]) {
            e.preventDefault();
            alert('❌ Please select a file first!');
            return false;
        }
        
        const file = fileInput.files[0];
        const fileName = file.name;
        const fileSize = file.size;
        const fileExt = fileName.split('.').pop().toLowerCase();
        
        console.log('=== FORM SUBMISSION STARTED ===');
        console.log('File Name:', fileName);
        console.log('File Size:', (fileSize / 1024).toFixed(2), 'KB');
        console.log('File Type:', file.type);
        console.log('File Extension:', fileExt);
        
        // Final validation checks
        if (!['pdf', 'doc', 'docx', 'txt'].includes(fileExt)) {
            e.preventDefault();
            alert('❌ Invalid file type. Please upload PDF, DOC, DOCX, or TXT files only.');
            return false;
        }
        
        if (fileSize > 10485760) {
            e.preventDefault();
            alert('❌ File too large. Maximum size is 10MB.');
            return false;
        }
        
        // Show loading with detailed message
        document.getElementById('loading').style.display = 'block';
        document.getElementById('loading-message').innerHTML = '📄 Processing file: ' + fileName + '<br><small>Extracting text and generating flashcards...</small>';
        document.getElementById('submit-btn').disabled = true;
        
        // Set timeout to show if request is taking too long
        let timeoutId = setTimeout(function() {
            const loadingMsg = document.getElementById('loading-message');
            if (loadingMsg && loadingMsg.innerHTML.indexOf('still processing') === -1) {
                loadingMsg.innerHTML = '⏳ Still processing... This is taking longer than expected.<br><small>Please wait, the file may be large or the AI service may be busy.</small>';
            }
        }, 30000); // Show warning after 30 seconds
        
        // Store timeout ID to clear it later
        window.formTimeoutId = timeoutId;
        
        // Log submission
        console.log('Form submitted successfully, waiting for response...');
    });
    
    // Check for server-side errors passed from PHP
    document.addEventListener('DOMContentLoaded', function() {
        // Log any existing error messages
        const errorDiv = document.getElementById('error-message');
        if (errorDiv && errorDiv.style.display !== 'none') {
            console.error('Server-side error detected:', errorDiv.innerText);
            
            // Show debug info
            showDebugInfo(errorDiv.innerText);
        }
        
        // Log success messages
        const successDiv = document.getElementById('success-message');
        if (successDiv && successDiv.style.display !== 'none') {
            console.log('Success message:', successDiv.innerText);
        }
        
        // Check if there was a previous submission attempt
        const formData = sessionStorage.getItem('lastFlashcardAttempt');
        if (formData) {
            console.log('Previous attempt found:', formData);
            sessionStorage.removeItem('lastFlashcardAttempt');
        }
    });
    
    // Function to show debug information
    function showDebugInfo(errorMessage) {
        const debugDiv = document.getElementById('debug-info');
        const debugContent = document.getElementById('debug-content');
        
        const debugData = {
            timestamp: new Date().toISOString(),
            error: errorMessage,
            userAgent: navigator.userAgent,
            url: window.location.href,
            localStorage: {
                available: typeof Storage !== 'undefined'
            }
        };
        
        debugContent.textContent = JSON.stringify(debugData, null, 2);
        debugDiv.style.display = 'block';
    }
    
    // Clear timeout when page unloads
    window.addEventListener('beforeunload', function() {
        if (window.formTimeoutId) {
            clearTimeout(window.formTimeoutId);
        }
    });
    
    // Function to manually test API connection (for debugging)
    window.testAPIConnection = function() {
        console.log('Testing API connection...');
        fetch('/test')
            .then(response => response.json())
            .then(data => {
                console.log('API Test Result:', data);
                alert('API Test: ' + JSON.stringify(data, null, 2));
            })
            .catch(error => {
                console.error('API Test Failed:', error);
                alert('API Test Failed: ' + error.message);
            });
    };
    
    // Display helpful tip for first-time users
    setTimeout(function() {
        if (!localStorage.getItem('flashcardTipShown')) {
            const warningDiv = document.getElementById('warning-message');
            document.getElementById('warning-text').innerHTML = '💡 Tip: For best results, use text-based PDFs (not scanned images). The AI works best with clear, well-structured text content.';
            warningDiv.style.display = 'flex';
            localStorage.setItem('flashcardTipShown', 'true');
            
            // Auto-hide after 10 seconds
            setTimeout(function() {
                warningDiv.style.display = 'none';
            }, 10000);
        }
    }, 2000);
</script>
@endsection