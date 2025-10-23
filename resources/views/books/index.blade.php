@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid" style="padding-top: 0;">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <span class="alert-text">{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(isset($editBook))
        {{-- Edit Form - NOW AT THE TOP --}}
        <div class="row" id="editBookSection">
            <div class="col-12">
                <div class="card mb-4" style="border: 2px solid #d63384; background: #f8f9fa;">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center" style="background: #d63384; color: white;">
                        <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Book: {{ $editBook->titre }}</h6>
                        @php
                        $cancelParams = request()->query();
                        unset($cancelParams['edit']);
                        @endphp
                        <a href="{{ route('books.index', $cancelParams) }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-times me-1"></i>Close
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- Error Messages --}}
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('books.update', $editBook->id) }}" method="POST" enctype="multipart/form-data" id="editBookForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="titre" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror"
                                        value="{{ old('titre', $editBook->titre) }}"
                                        required
                                        maxlength="255"
                                        placeholder="Enter book title">
                                    @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="auteur" class="form-label">Author <span class="text-danger">*</span></label>
                                    <input type="text" name="auteur" class="form-control @error('auteur') is-invalid @enderror"
                                        value="{{ old('auteur', $editBook->auteur) }}"
                                        required
                                        maxlength="255"
                                        placeholder="Enter author name">
                                    @error('auteur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- Select a category --</option>
                                        @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $editBook->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nom }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="3"
                                        maxlength="500"
                                        placeholder="Enter book description"
                                        id="editDescription">{{ old('description', $editBook->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted float-end"><span id="charCount">0</span>/500 characters</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cover Image</label>
                                    <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror"
                                        accept="image/*">
                                    <small class="form-text text-muted">Formats: JPG, PNG, WEBP - Max: 2MB</small>
                                    @error('cover_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if($editBook->cover_image)
                                    @php
                                    $imageUrl = asset('storage/' . $editBook->cover_image);
                                    @endphp
                                    <div class="mt-2">
                                        <p class="mb-0 small">Current cover:</p>
                                        <img src="{{ $imageUrl }}"
                                            alt="Current cover"
                                            class="img-thumbnail mt-1"
                                            style="max-width: 100px; max-height: 100px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="alert alert-warning p-2 small mt-1 d-none">
                                            ⚠️ Image not loaded: {{ $editBook->cover_image }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_valid" value="1" class="form-check-input" id="is_valid"
                                            {{ old('is_valid', $editBook->is_valid) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_valid">Valid</label>
                                    </div>
                                </div>
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Update PDF file (admin only)</label>
                                    <input type="file" name="pdf" class="form-control @error('pdf') is-invalid @enderror"
                                        accept="application/pdf">
                                    <small class="form-text text-muted">Maximum size: 10MB. Accepted format: PDF only.</small>
                                    @error('pdf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if($editBook->pdf_path)
                                    <div class="mt-2">
                                        <p class="mb-0 small">Current PDF:</p>
                                        <button type="button"
                                            class="btn btn-outline-primary btn-xs js-download-pdf"
                                            data-url="{{ route('books.download', $editBook->id) }}"
                                            title="Download PDF">
                                            <i class="fas fa-download me-1"></i>Download PDF
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                                    <i class="fas fa-save me-1"></i>Update
                                </button>
                                @php
                                $cancelParams = request()->query();
                                unset($cancelParams['edit']);
                                @endphp
                                <a href="{{ route('books.index', $cancelParams) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Book list with search and sort --}}
        <div class="row mb-4 mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Book Collection</h6>
                    <a href="{{ route('books.create') }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                        <i class="fas fa-plus me-1"></i>Add Book
                    </a>
                </div>
            </div>
        </div>

        {{-- Simplified Search Form --}}
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('books.index') }}" method="GET" id="searchForm">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        {{-- Search bar by title and author only --}}
                        <div style="flex: 2; min-width: 300px;">
                            <div class="mb-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by title or author..."
                                        value="{{ $search_query ?? '' }}"
                                        id="searchInput">
                                    @if($search_query)
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Sort by --}}
                        <div style="flex: 1; min-width: 150px;">
                            <div class="mb-2">
                                <select name="sort_by" class="form-control form-control-sm">
                                    <option value="titre" {{ $sort_by == 'titre' ? 'selected' : '' }}>Title</option>
                                    <option value="auteur" {{ $sort_by == 'auteur' ? 'selected' : '' }}>Author</option>
                                    <option value="created_at" {{ $sort_by == 'created_at' ? 'selected' : '' }}>Date added</option>
                                    <option value="is_valid" {{ $sort_by == 'is_valid' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>
                        </div>

                        {{-- Sort order --}}
                        <div style="flex: 1; min-width: 120px;">
                            <div class="mb-2">
                                <select name="sort_order" class="form-control form-control-sm">
                                    <option value="asc" {{ $sort_order == 'asc' ? 'selected' : '' }}>Ascending (A-Z)</option>
                                    <option value="desc" {{ $sort_order == 'desc' ? 'selected' : '' }}>Descending (Z-A)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Search results --}}
        @if($search_query || request()->has('sort_by') || request()->has('sort_order'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info py-2 mb-0">
                    <small>
                        <strong>{{ $books->total() }}</strong> result(s) found
                        @if($search_query)
                        for "<strong>{{ $search_query }}</strong>"
                        @endif
                       
                        
                        <a href="{{ route('books.index') }}" class="float-end text-decoration-none">
                            <small>Clear filters</small>
                        </a>
                    </small>
                </div>
            </div>
        </div>
        @endif

        {{-- Book list --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>All Books</h6>
                            <small class="text-muted">{{ $books->total() }} total book(s)</small>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cover</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Title</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Author</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($books as $book)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                @if($book->cover_image)
                                                @php
                                                // CORRECTION : Vérification et construction correcte de l'URL de l'image
                                                $coverImagePath = $book->cover_image;

                                                // Si le chemin commence déjà par 'storage/', on l'utilise directement
                                                if (strpos($coverImagePath, 'storage/') === 0) {
                                                $imageUrl = asset($coverImagePath);
                                                } else {
                                                // Sinon, on ajoute 'storage/' devant
                                                $imageUrl = asset('storage/' . $coverImagePath);
                                                }
                                                @endphp

                                                {{-- Image avec gestion d'erreur améliorée --}}
                                                <div class="position-relative">
                                                    <img src="{{ $imageUrl }}"
                                                        alt="{{ $book->titre }}"
                                                        class="rounded"
                                                        style="width: 50px; height: 65px; object-fit: cover;"
                                                        onerror="handleImageError(this)">

                                                    {{-- Fallback si l'image ne charge pas --}}
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center d-none position-relative book-cover-fallback"
                                                        style="width: 50px; height: 65px;">
                                                        <i class="fas fa-book text-muted"></i>
                                                        <small class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 6px;">
                                                            ⚠️
                                                        </small>
                                                    </div>
                                                </div>
                                                @else
                                                {{-- Si aucune image --}}
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 65px;">
                                                    <i class="fas fa-book text-muted"></i>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $book->titre }}</h6>
                                                @if($book->description)
                                                <p class="text-xs text-secondary mb-0">{{ Str::limit($book->description, 30) }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $book->auteur }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs text-secondary">{{ $book->category->nom ?? 'No category' }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm {{ $book->is_valid ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                {{ $book->is_valid ? 'Valid' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Edit Button --}}
                                                <a href="{{ route('books.index', array_merge(request()->query(), ['edit' => $book->id])) }}"
                                                    class="btn btn-outline-info btn-xs px-2 edit-btn"
                                                    title="Edit"
                                                    data-book-title="{{ $book->titre }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- PDF Button --}}
                                                @if($book->pdf_path)
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-xs px-2 js-download-pdf"
                                                    data-url="{{ route('books.download', $book->id) }}"
                                                    title="Download PDF"
                                                    aria-label="Download PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                                @endif

                                                {{-- Delete Button --}}
                                                <form action="{{ route('books.destroy', $book->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs px-2"
                                                        onclick="return confirm('Are you sure you want to delete this book?')"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            @if($books->isEmpty())
                            <div class="text-center p-4">
                                <p class="text-muted mb-0">
                                    @if($search_query)
                                    No books found for "{{ $search_query }}". Try other keywords.
                                    @else
                                    No books found. <a href="{{ route('books.create') }}">Create the first book</a>
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CUSTOM PAGINATION WITHOUT "SHOWING X TO Y OF Z RESULTS" --}}
        @if($books->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex flex-column align-items-center">
                    {{-- Custom pagination without the "Showing" text --}}
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($books->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $books->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($books->getUrlRange(1, $books->lastPage()) as $page => $url)
                                @if ($page == $books->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($books->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $books->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</main>

<script>
    // CORRECTION DES ERREURS SVG
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

    // FONCTION POUR GÉRER LES ERREURS D'IMAGE
    function handleImageError(imgElement) {
        console.log('Image loading error for:', imgElement.src);

        // Masquer l'image défectueuse
        imgElement.style.display = 'none';

        // Afficher le fallback
        const fallback = imgElement.nextElementSibling;
        if (fallback && fallback.classList.contains('book-cover-fallback')) {
            fallback.classList.remove('d-none');
            fallback.classList.add('d-flex');
        }
    }

    // FONCTIONS PRINCIPALES
    function clearSearch() {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        if (searchInput && searchForm) {
            searchInput.value = '';
            searchForm.submit();
        }
    }

    function initializeBookPage() {
        try {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');

            if (searchInput && searchForm) {
                // Real-time search (after 800ms pause)
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (this.value.length === 0 || this.value.length >= 2) {
                            searchForm.submit();
                        }
                    }, 800);
                });

                // Automatic select submission (sorting)
                const selects = document.querySelectorAll('select[name="sort_by"], select[name="sort_order"]');
                selects.forEach(select => {
                    if (select) {
                        select.addEventListener('change', function() {
                            searchForm.submit();
                        });
                    }
                });
            }

            // Script pour le formulaire d'édition
            <?php if (isset($editBook)): ?>
                const editSection = document.getElementById('editBookSection');
                if (editSection) {
                    // Smooth scroll to edit form
                    setTimeout(() => {
                        editSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 100);

                    // Character counter for description
                    const descriptionField = document.getElementById('editDescription');
                    const charCount = document.getElementById('charCount');

                    if (descriptionField && charCount) {
                        // Initial count
                        charCount.textContent = descriptionField.value.length;

                        // Update count on input
                        descriptionField.addEventListener('input', function() {
                            const length = this.value.length;
                            charCount.textContent = length;

                            if (length > 450) {
                                charCount.style.color = 'red';
                                charCount.style.fontWeight = 'bold';
                            } else {
                                charCount.style.color = '';
                                charCount.style.fontWeight = '';
                            }
                        });
                    }
                }
            <?php endif; ?>

            // Add click handlers for edit buttons
            const editButtons = document.querySelectorAll('.edit-btn');
            if (editButtons.length > 0) {
                editButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const bookTitle = this.getAttribute('data-book-title');
                        if (bookTitle) {
                            sessionStorage.setItem('editingBook', bookTitle);
                        }
                    });
                });
            }

            // Vérifier et corriger automatiquement les images qui n'ont pas chargé
            setTimeout(() => {
                const images = document.querySelectorAll('td img[src*="storage/"]');
                images.forEach(img => {
                    if (img.complete && img.naturalHeight === 0) {
                        handleImageError(img);
                    }
                });
            }, 500);

        } catch (error) {
            console.warn('Book page initialization error:', error);
        }
    }

    // INITIALISATION
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeBookPage);
    } else {
        setTimeout(initializeBookPage, 100);
    }

    /**
     * DOWNLOAD PDF WITHOUT PAGE RELOAD
     * Intercept buttons with class "js-download-pdf" and data-url attribute.
     */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest && e.target.closest('.js-download-pdf');
        if (!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('data-url');
        if (!url) {
            console.error('PDF URL missing on download button');
            return;
        }

        // Optional: give feedback
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(async (response) => {
            if (!response.ok) {
                const contentType = response.headers.get('content-type') || '';
                let data;
                try {
                    if (contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        data = await response.text();
                    }
                } catch (err) {
                    data = null;
                }
                console.error('Download failed', response.status, data);
                alert('Impossible de télécharger le PDF. Statut: ' + response.status);
                return;
            }

            const blob = await response.blob();

            // If the server returned HTML (e.g. login page), blob.type may be text/html
            const respContentType = response.headers.get('content-type') || '';
            if (!respContentType.includes('pdf') && blob.type && !blob.type.includes('pdf')) {
                const text = await blob.text();
                console.error('Expected PDF but received:', text);
                alert('Erreur lors du téléchargement du PDF.');
                return;
            }

            // Get filename from Content-Disposition if present
            let filename = 'download.pdf';
            const disposition = response.headers.get('content-disposition');
            if (disposition) {
                let match = disposition.match(/filename\*=UTF-8''([^;]+)/i);
                if (match && match[1]) {
                    filename = decodeURIComponent(match[1].replace(/['"]/g, ''));
                } else {
                    match = disposition.match(/filename="?([^"]+)"?/i);
                    if (match && match[1]) {
                        filename = match[1];
                    }
                }
            } else {
                // fallback: try to use last segment of URL
                try {
                    const urlObj = new URL(url, window.location.origin);
                    const parts = urlObj.pathname.split('/');
                    if (parts.length) {
                        const last = parts[parts.length - 1];
                        if (last) filename = last;
                    }
                } catch (e) {
                    // ignore
                }
            }

            const blobUrl = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = blobUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();

            setTimeout(() => {
                URL.revokeObjectURL(blobUrl);
                a.remove();
            }, 1000);
        }).catch((err) => {
            console.error('Fetch download error', err);
            alert('Erreur réseau lors du téléchargement du PDF.');
        }).finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });
</script>

<style>
    .page-link {
        color: #d63384;
        border-color: #dee2e6;
    }

    .page-item.active .page-link {
        background-color: #d63384;
        border-color: #d63384;
    }

    .page-link:hover {
        color: #a52766;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Highlight animation for edit section */
    @keyframes highlight-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(214, 51, 132, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(214, 51, 132, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(214, 51, 132, 0);
        }
    }

    #editBookSection .card {
        animation: highlight-pulse 2s ease-in-out;
    }

    /* CORRECTION DES ERREURS SVG */
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

    /* Styles pour les images de couverture */
    .book-cover-fallback {
        border: 1px solid #dee2e6;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
</style>
@endsection