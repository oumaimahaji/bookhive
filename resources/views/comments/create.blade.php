@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Comment</h5>
                    <a href="{{ route('comments.index') }}" class="btn bg-gradient-primary btn-sm mb-0">
                        <i class="fas fa-arrow-left me-1"></i>Back to Comments
                    </a>
                </div>
                <div class="card-body px-4 pt-4 pb-2">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('comments.store') }}" method="POST" id="commentForm">
                        @csrf
                        
                        <!-- User Selection - Pré-sélectionné avec l'admin connecté -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-control" required id="userSelect">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                            {{ auth()->id() == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-success">
                                    <i class="fas fa-check-circle me-1"></i>Connected admin: <strong>{{ auth()->user()->name }}</strong>
                                </div>
                            </div>

                            <!-- Post Selection avec Recherche Avancée -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Post <span class="text-danger">*</span></label>
                                
                                <!-- Barre de recherche compacte -->
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="fas fa-search text-muted small"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="postSearch" 
                                           placeholder="Search by title, content, or author..." 
                                           style="font-size: 0.875rem;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSearch" title="Clear search">
                                        <i class="fas fa-times small"></i>
                                    </button>
                                </div>
                                
                                <!-- Select compact -->
                                <select name="post_id" class="form-control form-control-sm" required id="postSelect" size="4">
                                    <option value="">-- Select a post --</option>
                                    @foreach($posts as $post)
                                        <option value="{{ $post->id }}" 
                                                data-title="{{ strtolower($post->titre) }}"
                                                data-content="{{ strtolower($post->contenu) }}"
                                                data-user="{{ strtolower($post->user->name ?? '') }}"
                                                data-fulltitle="{{ $post->titre }}"
                                                data-fullcontent="{{ $post->contenu }}"
                                                data-fulluser="{{ $post->user->name ?? '' }}"
                                                {{ old('post_id') == $post->id ? 'selected' : '' }}>
                                            [{{ \Carbon\Carbon::parse($post->date)->format('M d') }}] 
                                            {{ Str::limit($post->titre, 35) }} 
                                        </option>
                                    @endforeach
                                </select>
                                
                                <!-- Informations détaillées du post sélectionné -->
                                <div id="selectedPostInfo" class="mt-1 p-2 bg-light rounded small d-none">
                                    <div class="row">
                                        <div class="col-12">
                                            <strong class="text-primary" id="postTitle"></strong>
                                        </div>
                                        <div class="col-12 mt-1">
                                            <span class="text-muted" id="postAuthor"></span>
                                        </div>
                                        <div class="col-12 mt-1">
                                            <span class="text-muted" id="postContent"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-text text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <span id="postCount">{{ $posts->count() }}</span> posts available
                                    <span id="filteredCount" class="d-none"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Date - Forcée à aujourd'hui -->
                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" 
                                   class="form-control" required id="dateInput"
                                   readonly
                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                            <div class="form-text text-muted small">
                                <i class="fas fa-calendar-day me-1"></i>Today's date - Automatic
                            </div>
                        </div>

                        <!-- Content with Validation -->
                        <div class="mb-3">
                            <label class="form-label">Comment Content <span class="text-danger">*</span></label>
                            <textarea name="contenu" class="form-control" rows="5" 
                                      placeholder="Enter comment content (minimum 5 characters, maximum 1000 characters)" 
                                      required minlength="5" maxlength="1000"
                                      id="contentTextarea">{{ old('contenu') }}</textarea>
                            <div class="form-text">
                                <span id="contentCount">0</span>/1000 characters
                                <span id="contentStatus" class="ms-2"></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('comments.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn bg-gradient-success btn-sm" id="submitBtn">
                                <i class="fas fa-save me-1"></i>Add Comment
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control.is-valid {
    border-color: #28a745;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }

#dateInput {
    cursor: not-allowed;
}

#postSelect {
    min-height: 80px;
    font-size: 0.875rem;
}

#postSelect option {
    padding: 4px 8px;
    border-bottom: 1px solid #f8f9fa;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#selectedPostInfo {
    border-left: 3px solid #007bff;
    font-size: 0.8rem;
}

.search-match {
    background-color: #d1ecf1;
    font-weight: bold;
    padding: 1px 2px;
    border-radius: 2px;
    font-size: 0.8rem;
}

.input-group-sm .form-control {
    font-size: 0.875rem;
}

#postContent {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const form = document.getElementById('commentForm');
    const userSelect = document.getElementById('userSelect');
    const postSelect = document.getElementById('postSelect');
    const postSearch = document.getElementById('postSearch');
    const clearSearch = document.getElementById('clearSearch');
    const dateInput = document.getElementById('dateInput');
    const contentTextarea = document.getElementById('contentTextarea');
    const contentCount = document.getElementById('contentCount');
    const contentStatus = document.getElementById('contentStatus');
    const submitBtn = document.getElementById('submitBtn');
    const postCount = document.getElementById('postCount');
    const filteredCount = document.getElementById('filteredCount');
    const selectedPostInfo = document.getElementById('selectedPostInfo');
    const postTitle = document.getElementById('postTitle');
    const postAuthor = document.getElementById('postAuthor');
    const postContent = document.getElementById('postContent');

    // Store original posts for filtering
    const originalOptions = Array.from(postSelect.options);

    // Set user to current admin and date to today by default
    userSelect.value = '{{ auth()->id() }}';
    dateInput.value = new Date().toISOString().split('T')[0];

    // Initialize counters and validation
    updateContentCount();
    validateAll();
    updateSelectedPostInfo();

    // Event listeners
    postSelect.addEventListener('change', function() {
        validatePost();
        updateSelectedPostInfo();
        updateSubmitButton();
    });
    
    contentTextarea.addEventListener('input', function() {
        updateContentCount();
        validateContent();
        updateSubmitButton();
    });

    // Recherche en temps réel avec délai pour performance
    let searchTimeout;
    postSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterPosts(this.value.toLowerCase());
        }, 300);
    });

    // Effacer la recherche
    clearSearch.addEventListener('click', function() {
        postSearch.value = '';
        filterPosts('');
        postSearch.focus();
    });

    // Raccourci clavier pour la recherche
    postSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterPosts('');
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            // Sélectionner le premier résultat si disponible
            const firstOption = postSelect.querySelector('option:not([value=""])');
            if (firstOption) {
                postSelect.value = firstOption.value;
                updateSelectedPostInfo();
                validatePost();
                updateSubmitButton();
            }
        }
    });

    // Empêcher la modification de la date
    dateInput.addEventListener('focus', function(e) {
        e.preventDefault();
        this.blur();
    });

    dateInput.addEventListener('keydown', function(e) {
        e.preventDefault();
    });

    // Fonction de filtrage des posts avec recherche multi-critères
    function filterPosts(searchTerm) {
        let visibleCount = 0;
        
        // Réinitialiser les options
        postSelect.innerHTML = '';
        const defaultOption = new Option('-- Select a post --', '');
        postSelect.add(defaultOption);
        
        // Filtrer et ajouter les options correspondantes
        originalOptions.forEach(option => {
            if (option.value === '') return;
            
            const title = option.getAttribute('data-title') || '';
            const content = option.getAttribute('data-content') || '';
            const user = option.getAttribute('data-user') || '';
            
            // Recherche dans tous les champs
            const matchesSearch = !searchTerm || 
                                title.includes(searchTerm) || 
                                content.includes(searchTerm) || 
                                user.includes(searchTerm);
            
            if (matchesSearch) {
                // Créer une version enrichie du texte avec highlight
                let optionText = option.text;
                let displayText = optionText;
                
                if (searchTerm) {
                    // Highlight dans le titre
                    const titleRegex = new RegExp(`(${searchTerm})`, 'gi');
                    const fullTitle = option.getAttribute('data-fulltitle') || '';
                    if (fullTitle.toLowerCase().includes(searchTerm)) {
                        displayText = displayText.replace(titleRegex, '<span class="search-match">$1</span>');
                    }
                    
                    // Vérifier aussi dans le contenu et auteur pour l'highlight
                    const fullContent = option.getAttribute('data-fullcontent') || '';
                    const fullUser = option.getAttribute('data-fulluser') || '';
                    
                    if (fullContent.toLowerCase().includes(searchTerm) || fullUser.toLowerCase().includes(searchTerm)) {
                        // Ajouter un indicateur que la recherche a matché ailleurs
                        displayText += ' <span class="text-success">✓</span>';
                    }
                }
                
                const newOption = new Option(option.text, option.value);
                newOption.setAttribute('data-title', option.getAttribute('data-title'));
                newOption.setAttribute('data-content', option.getAttribute('data-content'));
                newOption.setAttribute('data-user', option.getAttribute('data-user'));
                newOption.setAttribute('data-fulltitle', option.getAttribute('data-fulltitle'));
                newOption.setAttribute('data-fullcontent', option.getAttribute('data-fullcontent'));
                newOption.setAttribute('data-fulluser', option.getAttribute('data-fulluser'));
                newOption.innerHTML = displayText;
                
                postSelect.add(newOption);
                visibleCount++;
            }
        });
        
        // Mettre à jour le compteur avec style
        if (searchTerm) {
            postCount.classList.add('d-none');
            filteredCount.classList.remove('d-none');
            const countClass = visibleCount === 0 ? 'text-danger' : 'text-success';
            const countIcon = visibleCount === 0 ? 'fa-times' : 'fa-check';
            filteredCount.innerHTML = `<i class="fas ${countIcon} me-1"></i><span class="${countClass}">${visibleCount}</span> posts match "${searchTerm}"`;
        } else {
            postCount.classList.remove('d-none');
            filteredCount.classList.add('d-none');
        }
        
        // Conserver la sélection actuelle si elle existe toujours
        const currentSelected = postSelect.querySelector(`option[value="${postSelect.value}"]`);
        if (!currentSelected && postSelect.value) {
            postSelect.value = '';
            updateSelectedPostInfo();
        }
        
        validatePost();
        updateSubmitButton();
    }

    // Mettre à jour les informations détaillées du post sélectionné
    function updateSelectedPostInfo() {
        const selectedOption = postSelect.options[postSelect.selectedIndex];
        
        if (postSelect.value && selectedOption && selectedOption.value !== '') {
            const fullTitle = selectedOption.getAttribute('data-fulltitle') || selectedOption.text.replace(/\[.*?\]\s*/, '').replace(/<[^>]*>/g, '').trim();
            const fullUser = selectedOption.getAttribute('data-fulluser') || 'Unknown author';
            const fullContent = selectedOption.getAttribute('data-fullcontent') || 'No content available';
            const postDate = selectedOption.text.match(/\[(.*?)\]/)?.[1] || 'Unknown date';
            
            postTitle.textContent = fullTitle;
            postAuthor.textContent = `By ${fullUser} | ${postDate}`;
            postContent.textContent = fullContent.length > 120 ? fullContent.substring(0, 120) + '...' : fullContent;
            
            selectedPostInfo.classList.remove('d-none');
        } else {
            selectedPostInfo.classList.add('d-none');
        }
    }

    // Update content character count
    function updateContentCount() {
        const length = contentTextarea.value.length;
        contentCount.textContent = length;
        
        if (length < 5) {
            contentCount.className = 'text-danger';
            contentStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
        } else if (length < 10) {
            contentCount.className = 'text-warning';
            contentStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Short</span>';
        } else if (length > 800) {
            contentCount.className = 'text-warning';
            contentStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Long</span>';
        } else if (length > 950) {
            contentCount.className = 'text-danger';
            contentStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Too long</span>';
        } else {
            contentCount.className = 'text-success';
            contentStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Good</span>';
        }
    }

    // Validation functions
    function validatePost() {
        const isValid = postSelect.value !== '';
        
        if (!isValid) {
            postSelect.classList.add('is-invalid');
            postSelect.classList.remove('is-valid');
        } else {
            postSelect.classList.remove('is-invalid');
            postSelect.classList.add('is-valid');
        }
        
        return isValid;
    }

    function validateContent() {
        const length = contentTextarea.value.length;
        const isValid = length >= 5 && length <= 1000;
        
        if (!isValid) {
            contentTextarea.classList.add('is-invalid');
            contentTextarea.classList.remove('is-valid');
        } else {
            contentTextarea.classList.remove('is-invalid');
            contentTextarea.classList.add('is-valid');
        }
        
        return isValid;
    }

    function validateAll() {
        validatePost();
        validateContent();
        updateSubmitButton();
    }

    function updateSubmitButton() {
        const isPostValid = postSelect.value !== '';
        const isContentValid = validateContent();
        
        const isFormValid = isPostValid && isContentValid;
        
        submitBtn.disabled = !isFormValid;
        
        if (isFormValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('bg-gradient-success');
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Add Comment ✓';
        } else {
            submitBtn.classList.remove('bg-gradient-success');
            submitBtn.classList.add('btn-secondary');
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Add Comment';
        }
    }

    // Prevent form submission if invalid
    form.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            if (!postSelect.value) {
                alert('Please select a post before submitting.');
                postSearch.focus();
            } else if (contentTextarea.value.length < 5) {
                alert('Comment content must be at least 5 characters long.');
                contentTextarea.focus();
            }
        }
    });

    // Initial validation
    validateAll();

    // Focus sur la barre de recherche au chargement
    setTimeout(() => {
        postSearch.focus();
    }, 100);
});
</script>
@endsection