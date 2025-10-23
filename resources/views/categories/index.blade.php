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

        @if(isset($editCategory))
        {{-- Edit Form --}}
        <div class="row" id="editCategorySection">
            <div class="col-12">
                <div class="card mb-4" style="border: 2px solid #d63384; background: #f8f9fa;">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center" style="background: #d63384; color: white;">
                        <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Category: {{ $editCategory->nom }}</h6>
                        @php
                        $cancelParams = request()->query();
                        unset($cancelParams['edit']);
                        @endphp
                        <a href="{{ route('categories.index', $cancelParams) }}" class="btn btn-outline-light btn-sm">
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

                        <form action="{{ route('categories.update', $editCategory->id) }}" method="POST" id="editCategoryForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                                        value="{{ old('nom', $editCategory->nom) }}"
                                        required
                                        maxlength="255"
                                        id="editCategoryName"
                                        placeholder="Enter category name">
                                    @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum 255 characters</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Enter category description (optional)"
                                        maxlength="500"
                                        rows="3"
                                        id="editCategoryDescription">{{ old('description', $editCategory->description) }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted float-end"><span id="charCount">0</span>/500 characters</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                                    <i class="fas fa-save me-1"></i>Update
                                </button>
                                @php
                                $cancelParams = request()->query();
                                unset($cancelParams['edit']);
                                @endphp
                                <a href="{{ route('categories.index', $cancelParams) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else

        {{-- Categories list with search and sort --}}
        <div class="row mb-4 mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-0">Category Management</h6>
                    <a href="{{ route('categories.create') }}" class="btn btn-sm" style="background-color: #d63384; color: white; border: none;">
                        <i class="fas fa-plus me-1"></i>New Category
                    </a>
                </div>
            </div>
        </div>

        {{-- Advanced Search Form --}}
        <div class="row mb-4">
            <div class="col-12">
                <form action="{{ route('categories.index') }}" method="GET" id="searchForm">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        {{-- Search bar by name --}}
                        <div style="flex: 2; min-width: 300px;">
                            <div class="mb-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Search by category name..."
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
                                    <option value="nom" {{ $sort_by == 'nom' ? 'selected' : '' }}>Name</option>
                                    <option value="books_count" {{ $sort_by == 'books_count' ? 'selected' : '' }}>Number of Books</option>
                                    <option value="created_at" {{ $sort_by == 'created_at' ? 'selected' : '' }}>Date Added</option>
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

                        {{-- Action buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                            @if(request()->hasAny(['search', 'sort_by', 'sort_order']))
                            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-refresh me-1"></i>Reset
                            </a>
                            @endif
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
                        <strong>{{ $categories->total() }}</strong> result(s) found
                        @if($search_query)
                        for "<strong>{{ $search_query }}</strong>"
                        @endif

                        <a href="{{ route('categories.index') }}" class="float-end text-decoration-none">
                            <small>Clear filters</small>
                        </a>
                    </small>
                </div>
            </div>
        </div>
        @endif

        {{-- Categories list --}}
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Categories List</h6>
                            <small class="text-muted">{{ $categories->total() }} total category(ies)</small>
                        </div>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Number of Books</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Added</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $category->nom }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $category->description ? Str::limit($category->description, 50) : 'No description' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">{{ $category->books_count }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs text-secondary">
                                                {{ $category->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- Edit Button --}}
                                                <a href="{{ route('categories.index', array_merge(request()->query(), ['edit' => $category->id])) }}"
                                                    class="btn btn-outline-info btn-xs px-2 edit-category-btn"
                                                    title="Edit"
                                                    data-category-name="{{ $category->nom }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- PDF Button: changed to button that JS intercepts to download without reload --}}
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-xs px-2 js-download-pdf"
                                                    data-url="{{ route('categories.pdf', $category->id) }}"
                                                    title="Download PDF"
                                                    aria-label="Download PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>

                                                {{-- Delete Button --}}
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs px-2"
                                                        onclick="return confirm('Are you sure you want to delete this category?')"
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

                            @if($categories->isEmpty())
                            <div class="text-center p-4">
                                <p class="text-muted mb-0">
                                    @if($search_query)
                                    No category found for "{{ $search_query }}". Try other keywords.
                                    @else
                                    No category found. <a href="{{ route('categories.create') }}">Create the first category</a>
                                    @endif
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CENTERED PAGINATION WITHOUT "SHOWING X TO Y OF Z RESULTS" --}}
        @if($categories->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex flex-column align-items-center">
                    {{-- Custom pagination without the "Showing" text --}}
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($categories->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $categories->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                                @if ($page == $categories->currentPage())
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
                            @if ($categories->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $categories->nextPageUrl() }}" rel="next">&raquo;</a>
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
        @endif
    </div>
</main>

<script>
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('searchForm').submit();
    }

    // Script principal avec gestion d'erreurs
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - initializing category management');

        // Vérifier si les éléments existent avant de les utiliser
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
                select.addEventListener('change', function() {
                    searchForm.submit();
                });
            });
        }

        // Script pour l'édition de catégorie avec vérifications de sécurité
        <?php if (isset($editCategory)): ?>
            console.log('Edit mode activated for category');

            // Attendre que tout soit chargé
            setTimeout(function() {
                const editSection = document.getElementById('editCategorySection');
                const descriptionInput = document.getElementById('editCategoryDescription');
                const charCount = document.getElementById('charCount');
                const editForm = document.getElementById('editCategoryForm');
                const nameInput = document.getElementById('editCategoryName');

                if (editSection) {
                    console.log('Edit section found, initializing...');

                    // Smooth scroll to edit form
                    setTimeout(() => {
                        editSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 100);

                    // FONCTION FIABLE pour le compteur de caractères
                    function initializeCharCounter() {
                        console.log('Initializing character counter...');

                        if (!descriptionInput || !charCount) {
                            console.error('Character counter elements not found');
                            return;
                        }

                        // Mettre à jour immédiatement avec la valeur actuelle
                        const initialLength = descriptionInput.value.length;
                        console.log('Initial description length:', initialLength);
                        charCount.textContent = initialLength;

                        // Appliquer le style initial
                        updateCharStyle(initialLength);

                        // Écouter tous les changements
                        descriptionInput.addEventListener('input', function() {
                            const length = this.value.length;
                            charCount.textContent = length;
                            updateCharStyle(length);

                            // Limiter à 500 caractères
                            if (length > 500) {
                                this.value = this.value.substring(0, 500);
                                charCount.textContent = 500;
                                updateCharStyle(500);
                            }
                        });

                        // Événements supplémentaires pour plus de fiabilité
                        descriptionInput.addEventListener('change', function() {
                            const length = this.value.length;
                            charCount.textContent = length;
                            updateCharStyle(length);
                        });

                        descriptionInput.addEventListener('paste', function() {
                            setTimeout(() => {
                                const length = this.value.length;
                                charCount.textContent = length;
                                updateCharStyle(length);
                            }, 0);
                        });

                        console.log('Character counter initialized successfully');
                    }

                    function updateCharStyle(length) {
                        if (length > 450) {
                            charCount.style.color = 'red';
                            charCount.style.fontWeight = 'bold';
                        } else if (length > 400) {
                            charCount.style.color = 'orange';
                            charCount.style.fontWeight = 'bold';
                        } else {
                            charCount.style.color = '#6c757d';
                            charCount.style.fontWeight = 'normal';
                        }
                    }

                    // Démarrer le compteur
                    initializeCharCounter();

                    // Double sécurité après un court délai
                    setTimeout(initializeCharCounter, 100);

                    // Validation du formulaire (seulement si les éléments existent)
                    if (editForm && nameInput) {
                        function validateName() {
                            const value = nameInput.value.trim();
                            if (value.length === 0) {
                                nameInput.classList.add('is-invalid');
                                nameInput.classList.remove('is-valid');
                                return false;
                            } else if (value.length > 255) {
                                nameInput.classList.add('is-invalid');
                                nameInput.classList.remove('is-valid');
                                return false;
                            } else {
                                nameInput.classList.remove('is-invalid');
                                nameInput.classList.add('is-valid');
                                return true;
                            }
                        }

                        function validateDescription() {
                            if (!descriptionInput) return true;
                            const value = descriptionInput.value;
                            if (value.length > 500) {
                                descriptionInput.classList.add('is-invalid');
                                descriptionInput.classList.remove('is-valid');
                                return false;
                            } else {
                                descriptionInput.classList.remove('is-invalid');
                                if (value.length > 0) {
                                    descriptionInput.classList.add('is-valid');
                                }
                                return true;
                            }
                        }

                        nameInput.addEventListener('input', validateName);
                        if (descriptionInput) {
                            descriptionInput.addEventListener('input', validateDescription);
                        }

                        editForm.addEventListener('submit', function(e) {
                            const isNameValid = validateName();
                            const isDescriptionValid = validateDescription();

                            if (!isNameValid || !isDescriptionValid) {
                                e.preventDefault();
                                let errorMessage = 'Please correct the following errors:\n';
                                if (!isNameValid) {
                                    if (nameInput.value.trim().length === 0) {
                                        errorMessage += '- Category name is required\n';
                                    } else if (nameInput.value.length > 255) {
                                        errorMessage += '- Name must not exceed 255 characters\n';
                                    }
                                }
                                if (!isDescriptionValid) {
                                    errorMessage += '- Description must not exceed 500 characters\n';
                                }
                                alert(errorMessage);
                            }
                        });

                        // Initial validation
                        validateName();
                        validateDescription();
                    }
                } else {
                    console.error('Edit section not found');
                }
            }, 100);
        <?php endif; ?>

        // Gestion des boutons d'édition avec vérification d'existence
        const editButtons = document.querySelectorAll('.edit-category-btn');
        if (editButtons.length > 0) {
            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Store which category we're editing for visual feedback
                    const categoryName = this.getAttribute('data-category-name');
                    sessionStorage.setItem('editingCategory', categoryName);
                });
            });
        }

        // Download PDF without reloading the page - intercept .js-download-pdf
        document.addEventListener('click', function (e) {
            const btn = e.target.closest && e.target.closest('.js-download-pdf');
            if (!btn) return;

            e.preventDefault();

            const url = btn.getAttribute('data-url');
            if (!url) {
                console.error('PDF URL missing on download button');
                return;
            }

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
                    console.error('Category PDF download failed', response.status, data);
                    alert('Impossible de télécharger le PDF. Statut: ' + response.status);
                    return;
                }

                const blob = await response.blob();
                const respContentType = response.headers.get('content-type') || '';
                if (!respContentType.includes('pdf') && blob.type && !blob.type.includes('pdf')) {
                    const text = await blob.text();
                    console.error('Expected PDF but received:', text);
                    alert('Erreur lors du téléchargement du PDF.');
                    return;
                }

                // Get filename from Content-Disposition if present
                let filename = 'category.pdf';
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
    });

    // Gestion des erreurs globales
    window.addEventListener('error', function(e) {
        console.error('Global error:', e.error);
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

    .pagination {
        margin-bottom: 0;
    }

    .justify-content-center {
        justify-content: center !important;
    }

    .text-center {
        text-align: center !important;
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

    #editCategorySection .card {
        animation: highlight-pulse 2s ease-in-out;
    }

    .is-valid {
        border-color: #198754 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    /* Style pour le compteur de caractères */
    #charCount {
        transition: all 0.3s ease;
        font-size: 0.75rem;
    }
</style>
@endsection