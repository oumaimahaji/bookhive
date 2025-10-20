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
                <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Add New Post
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

    {{-- Advanced Search Bar - TOUJOURS CACHÉ PAR DÉFAUT --}}
    @if(!isset($editPost))
    <div class="row mb-4 d-none" id="searchSection">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Search Posts</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="closeSearchBtn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route('posts.index') }}" method="GET" id="searchForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Search by title..." 
                                       value="{{ request('title') }}">
                            </div>
                            <div class="col-md-3 mb-3">
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
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Date Range</label>
                                <input type="date" name="date" class="form-control" 
                                       value="{{ request('date') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Items Per Page</label>
                                <select name="per_page" class="form-control">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Content</label>
                                <input type="text" name="content" class="form-control" placeholder="Search in content..." 
                                       value="{{ request('content') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Comments Count</label>
                                <select name="comments_count" class="form-control">
                                    <option value="">Any</option>
                                    <option value="0" {{ request('comments_count') === '0' ? 'selected' : '' }}>No Comments</option>
                                    <option value="1-5" {{ request('comments_count') == '1-5' ? 'selected' : '' }}>1-5 Comments</option>
                                    <option value="5+" {{ request('comments_count') == '5+' ? 'selected' : '' }}>5+ Comments</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn bg-gradient-primary">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                    <a href="{{ route('posts.index') }}" class="btn bg-gradient-secondary">
                                        <i class="fas fa-refresh me-2"></i>Reset
                                    </a>
                                </div>
                                <div class="text-end">
                                    <span class="text-sm text-muted">
                                        Found {{ $posts->total() }} results
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Inline Edit Form avec contrôles de saisie --}}
    @if(isset($editPost))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Edit Post: <span id="editPostTitle">{{ $editPost->titre }}</span></h6>
                    <a href="{{ route('posts.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('posts.update', $editPost->id) }}" method="POST" id="editPostForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- User Selection -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">User <span class="text-danger">*</span></label>
                                <select name="user_id" class="form-control" required id="editUserSelect">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $editPost->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-success">
                                    <i class="fas fa-check-circle me-1"></i>Admin connecté: <strong>{{ auth()->user()->name }}</strong>
                                </div>
                            </div>
                            
                            <!-- Date -->
                            <div class="col-md-6 mb-3">
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
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="titre" class="form-control" 
                                   value="{{ old('titre', $editPost->titre) }}" 
                                   placeholder="Enter post title (3-255 characters)" 
                                   required 
                                   minlength="3"
                                   maxlength="255"
                                   id="editTitleInput">
                            <div class="form-text">
                                <span id="editTitleCount">{{ strlen(old('titre', $editPost->titre)) }}</span>/255 characters
                                <span id="editTitleStatus" class="ms-2"></span>
                            </div>
                            <div class="invalid-feedback" id="editTitleError">
                                Title must be between 3 and 255 characters.
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea name="contenu" class="form-control" 
                                      rows="4" 
                                      placeholder="Enter post content (minimum 10 characters)" 
                                      required 
                                      minlength="10"
                                      id="editContentTextarea">{{ old('contenu', $editPost->contenu) }}</textarea>
                            <div class="form-text">
                                <span id="editContentCount">{{ strlen(old('contenu', $editPost->contenu)) }}</span> characters
                                <span id="editContentStatus" class="ms-2"></span>
                            </div>
                            <div class="invalid-feedback" id="editContentError">
                                Content must be at least 10 characters.
                            </div>
                        </div>

                        <!-- Image Management -->
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            
                            <!-- Current Image -->
                            @if($editPost->image)
                            <div class="mb-3 p-3 bg-light rounded">
                                <p class="text-sm font-weight-bold mb-2">Current Image:</p>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/' . $editPost->image) }}" 
                                         class="rounded me-3" 
                                         style="width: 100px; height: 100px; object-fit: cover;"
                                         alt="Current post image">
                                    <div class="flex-grow-1">
                                        <label class="d-flex align-items-center text-danger cursor-pointer">
                                            <input type="checkbox" name="remove_image" value="1" class="me-2">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="mb-3 p-3 bg-light rounded">
                                <p class="text-sm text-muted mb-0">No image currently</p>
                            </div>
                            @endif
                            
                            <!-- New Image Upload -->
                            <div class="mt-2">
                                <label class="form-label">Upload New Image</label>
                                <input type="file" name="image" 
                                       class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <div class="form-text">
                                    Leave empty to keep current image | Formats: JPEG, PNG, JPG, GIF | Max: 2MB
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-gradient-success" id="editSubmitBtn">
                                <i class="fas fa-save me-2"></i>Update Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Posts Table - SEULEMENT en mode liste --}}
    @if(!isset($editPost))
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Posts Management</h6>
                    <div>
                        <span class="badge bg-gradient-info">
                            Total: {{ $posts->total() }} posts
                        </span>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
<thead>
    <tr>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                Title & Image
                @if(request('sort') == 'title')
                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'user', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                User
                @if(request('sort') == 'user')
                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                Date
                @if(request('sort') == 'date')
                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            Comments
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            Reactions
        </th>
        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
    </tr>
</thead>

<tbody>
    @forelse ($posts as $post)
    <tr>
        <td>
            <div class="d-flex px-2 py-1">
                <!-- Image Thumbnail -->
                @if($post->image)
                <div class="me-3">
                    <img src="{{ asset('storage/' . $post->image) }}" 
                         class="rounded cursor-pointer" 
                         style="width: 60px; height: 60px; object-fit: cover;"
                         alt="Post image"
                         data-bs-toggle="tooltip"
                         data-bs-title="Click to view larger"
                         onclick="openImageModal(this.src)">
                </div>
                @else
                <div class="me-3">
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                </div>
                @endif
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ $post->titre }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ Str::limit($post->contenu, 50) }}</p>
                    @if($post->image)
                    <small class="text-info">
                        <i class="fas fa-image me-1"></i>Has image
                    </small>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <div class="d-flex flex-column">
                    <h6 class="mb-0 text-sm">{{ $post->user->name ?? 'N/A' }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ $post->user->email ?? '' }}</p>
                </div>
            </div>
        </td>
        <td class="align-middle text-center">
            <span class="badge badge-sm bg-gradient-dark">
                {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}
            </span>
        </td>
        <td class="align-middle text-center">
            @if(($post->comments_count ?? $post->comments->count()) > 0)
            <a href="{{ route('comments.index', ['post_id' => $post->id]) }}" 
               class="badge badge-sm bg-gradient-info cursor-pointer"
               title="Click to view comments"
               style="text-decoration: none;">
                {{ $post->comments_count ?? $post->comments->count() }} comments
            </a>
            @else
            <span class="badge badge-sm bg-gradient-secondary">0 comments</span>
            @endif
        </td>
        <td class="align-middle text-center">
            @if($post->reactions_count > 0)
            <a href="{{ route('admin.posts.reactions.index', ['post_id' => $post->id]) }}" 
               class="badge badge-sm bg-gradient-success cursor-pointer"
               title="Click to view reactions"
               style="text-decoration: none;">
                {{ $post->reactions_count }} reactions
            </a>
            @else
            <span class="badge badge-sm bg-gradient-secondary">0 reactions</span>
            @endif
        </td>
        <td class="align-middle text-center">
            <div class="btn-group" role="group">
                <a href="{{ route('posts.index', ['edit' => $post->id]) }}"
                   class="btn btn-sm bg-gradient-info me-1"
                   title="Edit post">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm bg-gradient-danger" 
                            onclick="return confirm('Are you sure you want to delete this post?')" 
                            title="Delete post">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="text-center p-4">
            <div class="text-muted">
                <i class="fas fa-inbox fa-2x mb-3"></i>
                <p>No posts found matching your criteria.</p>
                <a href="{{ route('posts.index') }}" class="btn btn-sm bg-gradient-primary">Clear Filters</a>
            </div>
        </td>
    </tr>
    @endforelse
</tbody>
                        </table>
                    </div>
                    
                    {{-- Pagination - VERSION SIMPLIFIÉE --}}
                    @if($posts->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            {{ $posts->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<style>
.btn-group .btn {
    margin: 0 2px;
}
.table th a {
    text-decoration: none;
    color: inherit;
}
.table th a:hover {
    color: #007bff;
}

/* Styles pour l'édition inline */
.form-control.is-valid {
    border-color: #28a745;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.text-success { 
    color: #28a745 !important; 
}
.text-warning { 
    color: #ffc107 !important; 
}
.text-danger { 
    color: #dc3545 !important; 
}

#editValidationSummary {
    border-left: 4px solid #17a2b8;
}

.validation-item {
    display: flex;
    align-items: center;
    margin-bottom: 2px;
    font-size: 0.875rem;
}

.validation-item i {
    width: 16px;
    margin-right: 8px;
}

#editDateInput {
    cursor: not-allowed;
}

/* Image hover effects */
.table img:hover {
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.cursor-pointer {
    cursor: pointer;
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
.cursor-pointer {
    cursor: pointer;
    transition: all 0.3s ease;
}

.cursor-pointer:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<script>
// Fonction pour ouvrir le modal d'image
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    var imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}

// Fonction pour afficher les toasts
function showEditToast(message, type) {
    var toast = document.createElement('div');
    toast.className = 'alert alert-' + (type === 'error' ? 'danger' : 'success') + ' alert-dismissible fade show';
    toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    
    document.body.appendChild(toast);
    
    setTimeout(function() {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Gestion du toggle de recherche
    var toggleSearchBtn = document.getElementById('toggleSearchBtn');
    var closeSearchBtn = document.getElementById('closeSearchBtn');
    var searchSection = document.getElementById('searchSection');

    // NE JAMAIS AFFICHER AUTOMATIQUEMENT LA RECHERCHE - TOUJOURS CACHÉE PAR DÉFAUT
    // Le bloc reste caché (d-none) sur toutes les pages

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
    var editForm = document.getElementById('editPostForm');
    
    if (editForm) {
        // Éléments du DOM pour l'édition
        var editTitleInput = document.getElementById('editTitleInput');
        var editContentTextarea = document.getElementById('editContentTextarea');
        var editTitleCount = document.getElementById('editTitleCount');
        var editContentCount = document.getElementById('editContentCount');
        var editTitleStatus = document.getElementById('editTitleStatus');
        var editContentStatus = document.getElementById('editContentStatus');
        var editDateInput = document.getElementById('editDateInput');
        var editSubmitBtn = document.getElementById('editSubmitBtn');

        // Forcer la date d'aujourd'hui
        editDateInput.value = new Date().toISOString().split('T')[0];
        
        // Initialiser les compteurs et validations
        updateEditTitleCount();
        updateEditContentCount();
        validateEditAll();

        // Événements de saisie
        editTitleInput.addEventListener('input', function() {
            updateEditTitleCount();
            validateEditTitle();
        });

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
        function updateEditTitleCount() {
            var length = editTitleInput.value.length;
            editTitleCount.textContent = length;
            
            if (length > 200) {
                editTitleCount.className = 'text-danger';
                editTitleStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Too long</span>';
            } else if (length > 150) {
                editTitleCount.className = 'text-warning';
                editTitleStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Warning</span>';
            } else if (length >= 3) {
                editTitleCount.className = 'text-success';
                editTitleStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Good</span>';
            } else {
                editTitleCount.className = 'text-danger';
                editTitleStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
            }
        }

        function updateEditContentCount() {
            var length = editContentTextarea.value.length;
            editContentCount.textContent = length;
            
            if (length < 10) {
                editContentCount.className = 'text-danger';
                editContentStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Minimum 10 characters</span>';
            } else if (length < 20) {
                editContentCount.className = 'text-warning';
                editContentStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Short</span>';
            } else {
                editContentCount.className = 'text-success';
                editContentStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Good</span>';
            }
        }

        // Fonctions de validation
        function validateEditTitle() {
            var length = editTitleInput.value.length;
            var isValid = length >= 3 && length <= 255;
            
            if (!isValid) {
                editTitleInput.classList.add('is-invalid');
                editTitleInput.classList.remove('is-valid');
            } else {
                editTitleInput.classList.remove('is-invalid');
                editTitleInput.classList.add('is-valid');
            }
            
            updateEditSubmitButton();
            return isValid;
        }

        function validateEditContent() {
            var length = editContentTextarea.value.length;
            var isValid = length >= 10;
            
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
            updateEditTitleCount();
            updateEditContentCount();
            validateEditTitle();
            validateEditContent();
        }

        // Mettre à jour l'état du bouton de soumission
        function updateEditSubmitButton() {
            var isTitleValid = editTitleInput.value.length >= 3 && editTitleInput.value.length <= 255;
            var isContentValid = editContentTextarea.value.length >= 10;
            
            var isFormValid = isTitleValid && isContentValid;
            
            editSubmitBtn.disabled = !isFormValid;
            
            if (isFormValid) {
                editSubmitBtn.classList.remove('btn-secondary');
                editSubmitBtn.classList.add('bg-gradient-success');
            } else {
                editSubmitBtn.classList.remove('bg-gradient-success');
                editSubmitBtn.classList.add('btn-secondary');
            }
        }

        // Empêcher la soumission si le formulaire n'est pas valide
        editForm.addEventListener('submit', function(e) {
            if (editSubmitBtn.disabled) {
                e.preventDefault();
                showEditToast('Please correct the errors in the form before submitting.', 'error');
                validateEditAll();
            }
        });

        // Initialiser l'affichage
        validateEditAll();
    }

    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
