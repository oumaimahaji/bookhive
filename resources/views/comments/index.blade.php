@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-2">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="toggleSearchBtn">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
            <div>
                <a href="{{ route('comments.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Add New Comment
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Advanced Search Bar - CACHÉ PAR DÉFAUT --}}
    @if(!isset($editComment))
    <div class="row mb-4 d-none" id="searchSection">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Advanced Search</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="closeSearchBtn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route('comments.index') }}" method="GET" id="searchForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Content</label>
                                <input type="text" name="content" class="form-control" placeholder="Search in comment content..." 
                                       value="{{ request('content') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">User</label>
                                <select name="user_id" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Post</label>
                                <select name="post_id" class="form-control">
                                    <option value="">All Posts</option>
                                    @foreach($posts as $post)
                                        <option value="{{ $post->id }}" {{ request('post_id') == $post->id ? 'selected' : '' }}>
                                            {{ Str::limit($post->titre, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" 
                                       value="{{ request('date') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date Range</label>
                                <div class="input-group">
                                    <input type="date" name="start_date" class="form-control" 
                                           value="{{ request('start_date') }}" placeholder="Start date">
                                    <span class="input-group-text">to</span>
                                    <input type="date" name="end_date" class="form-control" 
                                           value="{{ request('end_date') }}" placeholder="End date">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sort By</label>
                                <select name="sort" class="form-control">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="user" {{ request('sort') == 'user' ? 'selected' : '' }}>User Name</option>
                                    <option value="post" {{ request('sort') == 'post' ? 'selected' : '' }}>Post Title</option>
                                </select>
                            </div>
                            {{-- SENTIMENT FILTER --}}
<div class="col-md-4 mb-3">
    <label class="form-label">Sentiment</label>
    <select name="sentiment" class="form-control">
        <option value="">All Sentiments</option>
        <option value="positive" {{ request('sentiment') == 'positive' ? 'selected' : '' }}>Positive</option>
        <option value="negative" {{ request('sentiment') == 'negative' ? 'selected' : '' }}>Negative</option>
        <option value="neutral" {{ request('sentiment') == 'neutral' ? 'selected' : '' }}>Neutral</option>
    </select>
</div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Items Per Page</label>
                                <select name="per_page" class="form-control">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 items</option>
                                    <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25 items</option>
                                    <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 items</option>
                                    <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100 items</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Content Length</label>
                                <select name="content_length" class="form-control">
                                    <option value="">Any Length</option>
                                    <option value="short" {{ request('content_length') == 'short' ? 'selected' : '' }}>Short (&lt; 50 chars)</option>
                                    <option value="medium" {{ request('content_length') == 'medium' ? 'selected' : '' }}>Medium (50-200 chars)</option>
                                    <option value="long" {{ request('content_length') == 'long' ? 'selected' : '' }}>Long (&gt; 200 chars)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn bg-gradient-primary">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                    <a href="{{ route('comments.index') }}" class="btn bg-gradient-secondary">
                                        <i class="fas fa-refresh me-2"></i>Reset
                                    </a>
                                </div>
                                <div class="text-end">
                                    @if(request('post_id'))
                                    <a href="{{ route('posts.index', ['post_id' => request('post_id')]) }}" class="btn bg-gradient-info btn-sm">
                                        <i class="fas fa-eye me-1"></i>View This Post
                                    </a>
                                    @endif
                                    <span class="text-sm text-muted ms-3">
                                        {{-- SOLUTION TEMPORAIRE : Utiliser count() --}}
                                        Found {{ $comments->count() }} results
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Filters --}}
    @if(request()->anyFilled(['content', 'user_id', 'post_id', 'date', 'start_date', 'end_date', 'content_length']))
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <span class="text-sm text-muted me-3">Active Filters:</span>
                        <div class="d-flex flex-wrap gap-2">
                            @if(request('content'))
                            <span class="badge bg-gradient-primary">
                                Content: "{{ request('content') }}"
                                <a href="{{ request()->fullUrlWithQuery(['content' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('user_id'))
                            @php $selectedUser = $users->firstWhere('id', request('user_id')); @endphp
                            <span class="badge bg-gradient-info">
                                User: {{ $selectedUser->name ?? 'Unknown' }}
                                <a href="{{ request()->fullUrlWithQuery(['user_id' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('post_id'))
                            @php $selectedPost = $posts->firstWhere('id', request('post_id')); @endphp
                            <span class="badge bg-gradient-success">
                                Post: {{ Str::limit($selectedPost->titre ?? 'Unknown', 20) }}
                                <a href="{{ request()->fullUrlWithQuery(['post_id' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('date'))
                            <span class="badge bg-gradient-warning">
                                Date: {{ request('date') }}
                                <a href="{{ request()->fullUrlWithQuery(['date' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('start_date') && request('end_date'))
                            <span class="badge bg-gradient-dark">
                                Range: {{ request('start_date') }} to {{ request('end_date') }}
                                <a href="{{ request()->fullUrlWithQuery(['start_date' => null, 'end_date' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('content_length'))
                            <span class="badge bg-gradient-secondary">
                                Length: {{ ucfirst(request('content_length')) }}
                                <a href="{{ request()->fullUrlWithQuery(['content_length' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- Inline Edit Form avec contrôles de saisie --}}
    @if(isset($editComment))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Edit Comment</h6>
                    <a href="{{ route('comments.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('comments.update', $editComment->id) }}" method="POST" id="editCommentForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- User Selection -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-control" required id="editUserSelect">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $editComment->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-success">
                                    <i class="fas fa-check-circle me-1"></i>Admin connecté: <strong>{{ auth()->user()->name }}</strong>
                                </div>
                            </div>
                            
                            <!-- Post Selection -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Post <span class="text-danger">*</span></label>
                                <select name="post_id" class="form-control" required id="editPostSelect">
                                    @foreach($posts as $post)
                                        <option value="{{ $post->id }}" {{ $editComment->post_id == $post->id ? 'selected' : '' }}>
                                            {{ $post->titre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" 
                                   value="{{ date('Y-m-d') }}" 
                                   required 
                                   id="editDateInput"
                                   readonly
                                   style="background-color: #f8f9fa; cursor: not-allowed;">
                            <div class="form-text text-info">
                                <i class="fas fa-calendar-day me-1"></i>Date de modification - Aujourd'hui
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea name="contenu" class="form-control" 
                                      rows="4" 
                                      placeholder="Enter comment content (minimum 5 characters, maximum 1000 characters)" 
                                      required 
                                      minlength="5"
                                      maxlength="1000"
                                      id="editContentTextarea">{{ old('contenu', $editComment->contenu) }}</textarea>
                            <div class="form-text">
                                <span id="editContentCount">{{ strlen(old('contenu', $editComment->contenu)) }}</span>/1000 characters
                                <span id="editContentStatus" class="ms-2"></span>
                            </div>
                            <div class="invalid-feedback" id="editContentError">
                                Content must be between 5 and 1000 characters.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-success" id="editSubmitBtn">
                                <i class="fas fa-save me-2"></i>Update Comment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Comments Table - SEULEMENT quand on n'est pas en mode édition --}}
    @if(!isset($editComment))
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Comments Management</h6>
                    <div>
                        @if(request('post_id'))
                        <span class="badge bg-gradient-info me-2">
                            Filtered by Post
                        </span>
                        @endif
                        {{-- SOLUTION TEMPORAIRE : Utiliser count() --}}
                        <span class="badge bg-gradient-dark">
                            Total: {{ $comments->count() }} comments
                        </span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if($comments->count() > 0)
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Content</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">User</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Post</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sentiment</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comments as $comment)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <p class="text-sm mb-0">{{ Str::limit($comment->contenu, 80) }}</p>
                                                <p class="text-xs text-secondary mb-0">
                                                    {{ Str::length($comment->contenu) }} characters
                                                </p>
                                            </div>
                                        </div>

                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">{{ $comment->user->name ?? 'N/A' }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $comment->user->email ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm">{{ Str::limit($comment->post->titre, 40) }}</h6>
                                                <a href="{{ route('posts.index', ['post_id' => $comment->post_id]) }}" 
                                                   class="text-xs text-info">
                                                    View Post
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-dark">
                                            {{ \Carbon\Carbon::parse($comment->date)->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
    @if($comment->sentiment)
        {!! $comment->sentiment_badge !!}
    @else
        <span class="badge badge-sm bg-gradient-secondary">Not analyzed</span>
    @endif
</td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('comments.index', ['edit' => $comment->id]) }}"
                                               class="btn btn-sm bg-gradient-info me-1"
                                               title="Edit comment">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm bg-gradient-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this comment?')" 
                                                        title="Delete comment">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- PAGINATION CONDITIONNELLE --}}
                    @if(method_exists($comments, 'hasPages') && $comments->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            {{ $comments->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    @endif
                    
                    @else
                    <div class="text-center p-4">
                        <div class="text-muted">
                            <i class="fas fa-comments fa-2x mb-3"></i>
                            <p>No comments found matching your criteria.</p>
                            <a href="{{ route('comments.index') }}" class="btn btn-sm bg-gradient-primary">Clear Filters</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.btn-group .btn {
    margin: 0 2px;
}
.badge a {
    text-decoration: none;
}
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

/* Styles pour l'édition */
.form-control.is-valid {
    border-color: #28a745;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }

#editDateInput {
    cursor: not-allowed;
}

/* Animation pour la section de recherche */
#searchSection {
    transition: all 0.3s ease-in-out;
}

#searchSection.show {
    display: block !important;
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Style pour la pagination */
.pagination {
    margin-bottom: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du toggle de recherche
    var toggleSearchBtn = document.getElementById('toggleSearchBtn');
    var closeSearchBtn = document.getElementById('closeSearchBtn');
    var searchSection = document.getElementById('searchSection');

    // Toggle de la section de recherche
    if (toggleSearchBtn && searchSection) {
        toggleSearchBtn.addEventListener('click', function() {
            if (searchSection.classList.contains('d-none')) {
                // Afficher la recherche
                searchSection.classList.remove('d-none');
                searchSection.classList.add('show');
                this.innerHTML = '<i class="fas fa-times me-1"></i>Hide Search';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                // Focus sur le premier champ de recherche
                var firstInput = searchSection.querySelector('input, select');
                if (firstInput) {
                    setTimeout(function() { firstInput.focus(); }, 300);
                }
            } else {
                // Cacher la recherche
                searchSection.classList.add('d-none');
                searchSection.classList.remove('show');
                this.innerHTML = '<i class="fas fa-search me-1"></i>Search';
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
            }
        });
    }

    // Fermer la recherche avec le bouton X
    if (closeSearchBtn && searchSection) {
        closeSearchBtn.addEventListener('click', function() {
            searchSection.classList.add('d-none');
            searchSection.classList.remove('show');
            toggleSearchBtn.innerHTML = '<i class="fas fa-search me-1"></i>Search';
            toggleSearchBtn.classList.remove('btn-primary');
            toggleSearchBtn.classList.add('btn-outline-primary');
        });
    }

    // Vérifier si le formulaire d'édition existe
    const editForm = document.getElementById('editCommentForm');
    
    if (editForm) {
        // Éléments du DOM pour l'édition
        const editContentTextarea = document.getElementById('editContentTextarea');
        const editContentCount = document.getElementById('editContentCount');
        const editContentStatus = document.getElementById('editContentStatus');
        const editDateInput = document.getElementById('editDateInput');
        const editSubmitBtn = document.getElementById('editSubmitBtn');

        // Forcer la date d'aujourd'hui
        editDateInput.value = new Date().toISOString().split('T')[0];
        
        // Initialiser les compteurs et validations
        updateEditContentCount();
        validateEditAll();

        // Événements de saisie
        editContentTextarea.addEventListener('input', function() {
            updateEditContentCount();
            validateEditContent();
        });

        // Empêcher la modification de la date
        editDateInput.addEventListener('focus', function(e) {
            e.preventDefault();
            this.blur();
        });

        editDateInput.addEventListener('keydown', function(e) {
            e.preventDefault();
        });

        // Fonctions de mise à jour des compteurs
        function updateEditContentCount() {
            const length = editContentTextarea.value.length;
            editContentCount.textContent = length;
            
            if (length < 5) {
                editContentCount.className = 'text-danger';
                editContentStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Minimum 5 characters</span>';
            } else if (length < 10) {
                editContentCount.className = 'text-warning';
                editContentStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Short</span>';
            } else if (length > 800) {
                editContentCount.className = 'text-warning';
                editContentStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Long</span>';
            } else if (length > 950) {
                editContentCount.className = 'text-danger';
                editContentStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Near maximum</span>';
            } else {
                editContentCount.className = 'text-success';
                editContentStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Good</span>';
            }
        }

        // Fonctions de validation
        function validateEditContent() {
            const length = editContentTextarea.value.length;
            const isValid = length >= 5 && length <= 1000;
            
            if (!isValid) {
                editContentTextarea.classList.add('is-invalid');
                editContentTextarea.classList.remove('is-valid');
            } else {
                editContentTextarea.classList.remove('is-invalid');
                editContentTextarea.classList.add('is-valid');
            }
            
            updateEditSubmitButton();
            return isValid;
        }

        function validateEditAll() {
            updateEditContentCount();
            validateEditContent();
        }

        // Mettre à jour l'état du bouton de soumission
        function updateEditSubmitButton() {
            const isContentValid = editContentTextarea.value.length >= 5 && editContentTextarea.value.length <= 1000;
            
            const isFormValid = isContentValid;
            
            editSubmitBtn.disabled = !isFormValid;
            
            if (isFormValid) {
                editSubmitBtn.classList.remove('btn-secondary');
                editSubmitBtn.classList.add('bg-gradient-success');
                editSubmitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Comment ✓';
            } else {
                editSubmitBtn.classList.remove('bg-gradient-success');
                editSubmitBtn.classList.add('btn-secondary');
                editSubmitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Comment';
            }
        }

        // Empêcher la soumission si le formulaire n'est pas valide
        editForm.addEventListener('submit', function(e) {
            if (editSubmitBtn.disabled) {
                e.preventDefault();
                alert('Please correct the errors in the form before submitting.');
                validateEditAll();
            }
        });

        // Initialiser l'affichage
        validateEditAll();
    }
});
</script>
@endsection
