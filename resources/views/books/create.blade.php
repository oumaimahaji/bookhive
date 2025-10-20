@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-0">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card mb-0">
                <div class="card-header pb-0 text-center" style="background: #d63384; color: white;">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Book</h5>
                </div>
                <div class="card-body px-2 pt-1 pb-1">

                    {{-- ERROR DISPLAY --}}
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-2 py-1" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong class="small">Please correct the following errors:</strong>
                        </div>
                        <ul class="mb-0 mt-1 small" style="margin-left: 1rem;">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.7rem;"></button>
                    </div>
                    @endif

                    {{-- AI DUPLICATES ALERT --}}
                    @if(isset($duplicates) && !empty($duplicates))
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-robot me-2 fs-5"></i>
                            <h6 class="mb-0 fw-bold">AI - Potential Duplicates Detected</h6>
                        </div>
                        <p class="small mb-2 mt-1">Our artificial intelligence has detected similar books:</p>

                        @foreach($duplicates as $duplicate)
                        <div class="card mb-2 border-warning">
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-8">
                                        <h6 class="mb-1">{{ $duplicate['book']->titre }}</h6>
                                        <p class="small mb-1 text-muted">
                                            <strong>Author:</strong> {{ $duplicate['book']->auteur }}
                                            @if($duplicate['book']->category)
                                            | <strong>Category:</strong> {{ $duplicate['book']->category->nom }}
                                            @endif
                                        </p>
                                        <div class="small">
                                            @foreach($duplicate['reasons'] as $reason)
                                            <span class="badge bg-warning text-dark me-1">{{ $reason }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="fs-5 fw-bold text-danger">
                                            {{ $duplicate['score'] }}%
                                        </div>
                                        <small class="text-muted">Similarity score</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- CREATION FORM --}}
                    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" id="bookForm">
                        @csrf

                        <div class="row">
                            {{-- Left column --}}
                            <div class="col-md-6">
                                {{-- Title --}}
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="titre" value="{{ old('titre', $input['titre'] ?? '') }}"
                                        class="form-control form-control-sm @error('titre') is-invalid @enderror"
                                        placeholder="Enter book title"
                                        required
                                        maxlength="255"
                                        id="bookTitle">
                                    @error('titre')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted small">Maximum 255 characters</small>
                                </div>

                                {{-- Author --}}
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">Author <span class="text-danger">*</span></label>
                                    <input type="text" name="auteur" value="{{ old('auteur', $input['auteur'] ?? '') }}"
                                        class="form-control form-control-sm @error('auteur') is-invalid @enderror"
                                        placeholder="Enter author name"
                                        required
                                        maxlength="255">
                                    @error('auteur')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted small">Maximum 255 characters</small>
                                </div>

                                {{-- Category --}}
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control form-control-sm @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $input['category_id'] ?? '') == $category->id) ? 'selected' : '' }}>
                                            {{ $category->nom }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Type --}}
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">Type</label>
                                    <input type="text" name="type" value="{{ old('type', $input['type'] ?? '') }}"
                                        class="form-control form-control-sm @error('type') is-invalid @enderror"
                                        placeholder="Enter book type (e.g., Novel, Science Fiction, etc.)"
                                        maxlength="100">
                                    @error('type')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted small">Maximum 100 characters</small>
                                </div>
                            </div>

                            {{-- Right column --}}
                            <div class="col-md-6">
                                {{-- Cover Image --}}
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">Cover Image <span class="text-danger">*</span></label>
                                    <input type="file" name="cover_image"
                                        class="form-control form-control-sm @error('cover_image') is-invalid @enderror"
                                        accept="image/jpeg,image/jpg,image/png,image/webp"
                                        required
                                        id="coverImage">
                                    <small class="form-text text-muted small">Formats: JPG, PNG, WEBP - Max: 2MB</small>
                                    @error('cover_image')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-1">
                                        <small class="text-info" id="fileInfo"></small>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">Description</label>
                                    <textarea name="description"
                                        class="form-control form-control-sm @error('description') is-invalid @enderror"
                                        rows="3"
                                        placeholder="Enter book description"
                                        maxlength="500"
                                        id="bookDescription"
                                        oninput="updateCharCount()">{{ old('description', $input['description'] ?? '') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted small">
                                        Maximum 500 characters. Currently:
                                        <span id="charCount">{{ strlen(old('description', $input['description'] ?? '')) }}</span> characters
                                    </small>
                                </div>

                                {{-- PDF file (visible only for admin) --}}
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">Book PDF File</label>
                                    <input type="file" name="pdf"
                                        class="form-control form-control-sm @error('pdf') is-invalid @enderror"
                                        accept="application/pdf"
                                        id="pdfFile">
                                    <small class="form-text text-muted small">Accepted format: PDF only - Max: 10MB</small>
                                    @error('pdf')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                    <div class="mt-1">
                                        <small class="text-info" id="pdfFileInfo"></small>
                                    </div>
                                </div>

                                {{-- Validation checkbox --}}
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="is_valid" value="1"
                                        class="form-check-input @error('is_valid') is-invalid @enderror"
                                        id="is_valid"
                                        {{ old('is_valid') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="is_valid">Validate the book immediately</label>
                                    @error('is_valid')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Checkbox to force creation despite duplicates --}}
                        @if(isset($duplicates) && !empty($duplicates))
                        <div class="mt-3 p-3 border rounded bg-light">
                            <p class="small mb-2">
                                <strong class="text-warning">⚠️ Do you still want to continue?</strong><br>
                                If you are sure this is a different book, you can ignore this warning.
                            </p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="force_create" id="force_create" value="1">
                                <label class="form-check-label small fw-bold text-warning" for="force_create">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Force creation despite detected duplicates
                                </label>
                            </div>
                        </div>
                        @endif

                        {{-- Buttons --}}
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-sm px-4" id="submitBtn"
                                style="background-color: #d63384; color: white; border: none;">
                                <i class="fas fa-plus me-1"></i>
                                @if(isset($duplicates) && !empty($duplicates))
                                Confirm Creation
                                @else
                                Add Book
                                @endif
                            </button>
                            <a href="{{ route('books.index') }}" class="btn btn-secondary btn-sm px-4 ms-2">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // ✅ SOLUTION ULTIME - Script directement dans la page
    function updateCharCount() {
        const textarea = document.getElementById('bookDescription');
        const counter = document.getElementById('charCount');
        if (textarea && counter) {
            const count = textarea.value.length;
            counter.textContent = count;

            // Changer la couleur selon le nombre de caractères
            if (count > 450) {
                counter.style.color = 'red';
                counter.style.fontWeight = 'bold';
            } else if (count > 400) {
                counter.style.color = 'orange';
                counter.style.fontWeight = 'bold';
            } else {
                counter.style.color = '#6c757d';
                counter.style.fontWeight = 'normal';
            }
        }
    }

    // Initialiser au chargement
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Formulaire de création initialisé');
        updateCharCount(); // Initialiser le compteur

        // Gestion des fichiers image
        const coverImageInput = document.getElementById('coverImage');
        const fileInfo = document.getElementById('fileInfo');

        if (coverImageInput && fileInfo) {
            coverImageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    fileInfo.textContent = `Selected: ${file.name} (${fileSize} MB)`;
                    fileInfo.style.color = 'green';

                    if (file.size > 2 * 1024 * 1024) {
                        fileInfo.textContent = `File too large: ${fileSize} MB (max 2MB)`;
                        fileInfo.style.color = 'red';
                        this.value = '';
                    }
                } else {
                    fileInfo.textContent = '';
                }
            });
        }

        // Gestion des fichiers PDF
        const pdfFileInput = document.getElementById('pdfFile');
        const pdfFileInfo = document.getElementById('pdfFileInfo');

        if (pdfFileInput && pdfFileInfo) {
            pdfFileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    pdfFileInfo.textContent = `Selected: ${file.name} (${fileSize} MB)`;
                    pdfFileInfo.style.color = 'green';

                    if (file.type !== 'application/pdf') {
                        pdfFileInfo.textContent = 'Invalid file type. Please select a PDF file.';
                        pdfFileInfo.style.color = 'red';
                        this.value = '';
                    } else if (file.size > 10 * 1024 * 1024) {
                        pdfFileInfo.textContent = `File too large: ${fileSize} MB (max 10MB)`;
                        pdfFileInfo.style.color = 'red';
                        this.value = '';
                    }
                } else {
                    pdfFileInfo.textContent = '';
                }
            });
        }

        // Gestion du checkbox force create
        const forceCheckbox = document.getElementById('force_create');
        const submitBtn = document.getElementById('submitBtn');

        if (forceCheckbox && submitBtn) {
            forceCheckbox.addEventListener('change', function() {
                const hasDuplicates = <?php echo json_encode(isset($duplicates) && !empty($duplicates)); ?>;

                if (this.checked) {
                    submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Force Add Despite Warnings';
                    submitBtn.style.backgroundColor = '#ffc107';
                    submitBtn.style.color = '#000';
                } else {
                    const buttonText = hasDuplicates ? 'Confirm Creation' : 'Add Book';
                    submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i>' + buttonText;
                    submitBtn.style.backgroundColor = '#d63384';
                    submitBtn.style.color = 'white';
                }
            });
        }
    });

    // ✅ Correction des erreurs SVG
    window.addEventListener('error', function(e) {
        if (e.target && (e.target.tagName === 'svg' || e.target.tagName === 'path')) {
            e.preventDefault();
        }
    });

    // Filtre des erreurs console pour SVG
    const originalConsoleError = console.error;
    console.error = function(...args) {
        if (args[0] && typeof args[0] === 'string' &&
            (args[0].includes('attribute d:') || args[0].includes('<path>') ||
                args[0].includes('M39.198') || args[0].includes('L40.84,0.95') ||
                args[0].includes('N59_198') || args[0].includes('140.84.0.95'))) {
            return;
        }
        originalConsoleError.apply(console, args);
    };
</script>

<style>
    /* ✅ Styles du compteur de caractères */
    #charCount {
        font-size: 0.75rem;
        font-weight: normal;
        display: inline-block;
        min-width: 40px;
        text-align: center;
        padding: 2px 8px;
        border-radius: 4px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
        margin-left: 5px;
    }

    /* ✅ Correction des erreurs SVG */
    svg:not([width]):not([height]) {
        display: none !important;
    }

    svg[width="0"],
    svg[height="0"],
    svg[style*="display: none"],
    svg[style*="width: 0"],
    svg[style*="height: 0"] {
        display: none !important;
    }

    path[d="M39.198"],
    path[d*="L40.84,0.95"],
    path[d*="140.84.0.95"],
    path[d*="N59_198"] {
        display: none !important;
    }

    /* ✅ Styles existants */
    .card-header {
        background: linear-gradient(45deg, #d63384, #e91e63) !important;
        border-bottom: none;
    }

    .form-control:focus {
        border-color: #d63384;
        box-shadow: 0 0 0 0.2rem rgba(214, 51, 132, 0.25);
    }

    .btn-primary {
        background-color: #d63384;
        border-color: #d63384;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #b02a6b;
        border-color: #b02a6b;
        transform: translateY(-1px);
    }

    .form-control-sm {
        border-radius: 0.375rem;
    }

    .alert {
        border: none;
        border-radius: 0.5rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .row {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }

        .col-md-6 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
    }
</style>

@endsection