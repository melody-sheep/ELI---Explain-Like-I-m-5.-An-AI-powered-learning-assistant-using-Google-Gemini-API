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
    .preview-card {
        animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div style="width: 280px; display: flex; flex-direction: column; background-color: var(--bg-secondary); border-right: 1px solid var(--border);">
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
        <div style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); border-radius: 12px; padding: 16px;">
            <p style="font-size: 10px; color: var(--accent-yellow); margin-bottom: 8px;">💡 TIP</p>
            <p style="font-size: 11px; color: var(--text-muted); line-height: 1.5;">
                Upload PDFs, Word docs, or text files. AI will analyze the content and generate smart flashcards automatically.
            </p>
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

        @if($errors->any())
            <div style="border: 1px solid var(--accent-red); background-color: rgba(239, 68, 68, 0.1); padding: 14px; margin-bottom: 24px; border-radius: 8px;">
                @foreach($errors->all() as $error)
                    <p style="color: var(--accent-red); font-size: 12px;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

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
                    </div>
                    <button type="button" onclick="removeFile()" style="background: none; border: none; color: var(--accent-red); cursor: pointer;">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <!-- Generate Options -->
            <div style="background-color: var(--bg-secondary); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <h3 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px;">Generation Options</h3>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 11px; font-weight: 600; margin-bottom: 8px; color: var(--text-secondary);">NUMBER OF FLASHCARDS</label>
                    <input type="range" name="num_cards" id="num-cards" min="5" max="30" value="10" step="1" style="width: 100%;">
                    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
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

            <!-- Advanced Options -->
            <details style="margin-bottom: 24px;">
                <summary style="cursor: pointer; color: var(--text-muted); font-size: 12px; padding: 8px;">Advanced Options</summary>
                <div style="margin-top: 12px; padding: 16px; border: 1px solid var(--border); border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <input type="checkbox" name="include_examples">
                        <span style="font-size: 12px;">Include example sentences</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="difficulty_rating">
                        <span style="font-size: 12px;">Add difficulty rating to each card</span>
                    </label>
                </div>
            </details>

            <button type="submit" id="submit-btn" disabled style="width: 100%; border: none; background: linear-gradient(135deg, var(--accent-yellow), var(--accent-orange)); color: black; padding: 14px; cursor: pointer; font-size: 14px; font-weight: 600; border-radius: 8px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span class="material-symbols-outlined">auto_awesome</span>
                GENERATE FLASHCARDS
            </button>
        </form>

        <!-- Loading Indicator -->
        <div id="loading" style="display: none; text-align: center; margin-top: 24px;">
            <div style="display: inline-block; width: 40px; height: 40px; border: 3px solid var(--border); border-top-color: var(--accent-yellow); border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="color: var(--text-muted); margin-top: 12px;">Generating flashcards...</p>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
    const rangeInput = document.getElementById('num-cards');
    const cardsValue = document.getElementById('cards-value');
    
    rangeInput.addEventListener('input', function() {
        cardsValue.textContent = this.value + ' cards';
    });
    
    function previewFile(input) {
        const file = input.files[0];
        if (file) {
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
            document.getElementById('file-preview').style.display = 'block';
            document.getElementById('submit-btn').disabled = false;
            document.querySelector('.upload-area').style.borderColor = 'var(--accent-green)';
        }
    }
    
    function removeFile() {
        document.getElementById('file-input').value = '';
        document.getElementById('file-preview').style.display = 'none';
        document.getElementById('submit-btn').disabled = true;
        document.querySelector('.upload-area').style.borderColor = 'var(--border)';
    }
    
    // Drag and drop functionality
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
        if (file && file.type.match('application/pdf|application/msword|application/vnd.openxmlformats-officedocument.wordprocessingml.document|text/plain')) {
            document.getElementById('file-input').files = e.dataTransfer.files;
            previewFile(document.getElementById('file-input'));
        } else {
            alert('Please upload a valid file (PDF, DOC, DOCX, or TXT)');
        }
    });
    
    document.getElementById('upload-form').addEventListener('submit', () => {
        document.getElementById('loading').style.display = 'block';
        document.getElementById('submit-btn').disabled = true;
    });
</script>
@endsection