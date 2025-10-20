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

        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Advanced Search Bar - CACHÉ PAR DÉFAUT --}}
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
                    <form action="{{ route('admin.posts.reactions.index') }}" method="GET" id="searchForm">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Post Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Search by post title..." 
                                       value="{{ request('title') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Author</label>
                                <select name="user_id" class="form-control">
                                    <option value="">All Authors</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Reactions Count</label>
                                <select name="reactions_count" class="form-control">
                                    <option value="">Any Count</option>
                                    <option value="0" {{ request('reactions_count') === '0' ? 'selected' : '' }}>No Reactions</option>
                                    <option value="1-5" {{ request('reactions_count') == '1-5' ? 'selected' : '' }}>1-5 Reactions</option>
                                    <option value="5-10" {{ request('reactions_count') == '5-10' ? 'selected' : '' }}>5-10 Reactions</option>
                                    <option value="10+" {{ request('reactions_count') == '10+' ? 'selected' : '' }}>10+ Reactions</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sort By</label>
                                <select name="sort" class="form-control">
                                    <option value="reactions_desc" {{ request('sort') == 'reactions_desc' ? 'selected' : '' }}>Most Reactions</option>
                                    <option value="reactions_asc" {{ request('sort') == 'reactions_asc' ? 'selected' : '' }}>Fewest Reactions</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Posts</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest Posts</option>
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
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="submit" class="btn bg-gradient-primary">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                    <a href="{{ route('admin.posts.reactions.index') }}" class="btn bg-gradient-secondary">
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

    {{-- Active Filters --}}
    @if(request()->anyFilled(['title', 'user_id', 'reactions_count', 'date_from', 'date_to']))
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <span class="text-sm text-muted me-3">Active Filters:</span>
                        <div class="d-flex flex-wrap gap-2">
                            @if(request('title'))
                            <span class="badge bg-gradient-primary">
                                Title: "{{ request('title') }}"
                                <a href="{{ request()->fullUrlWithQuery(['title' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('user_id'))
                            @php $selectedUser = $users->firstWhere('id', request('user_id')); @endphp
                            <span class="badge bg-gradient-info">
                                Author: {{ $selectedUser->name ?? 'Unknown' }}
                                <a href="{{ request()->fullUrlWithQuery(['user_id' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('reactions_count'))
                            <span class="badge bg-gradient-success">
                                Reactions: {{ request('reactions_count') }}
                                <a href="{{ request()->fullUrlWithQuery(['reactions_count' => null]) }}" class="text-white ms-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                            @endif
                            
                            @if(request('date_from') && request('date_to'))
                            <span class="badge bg-gradient-warning">
                                Date: {{ request('date_from') }} to {{ request('date_to') }}
                                <a href="{{ request()->fullUrlWithQuery(['date_from' => null, 'date_to' => null]) }}" class="text-white ms-1">
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

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Reactions</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $totalReactions }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="fas fa-smile text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($mostReactedPost)
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Most Reacted Post</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $mostReactedPost->reactions_count }}
                                </h5>
                                <p class="mb-0 text-sm">
                                    "{{ Str::limit($mostReactedPost->titre, 20) }}"
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-trophy text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Active Posts</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $posts->total() }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-newspaper text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Reaction Types</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $reactionsByType->count() }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="fas fa-heart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Si un post spécifique est sélectionné, afficher ses détails --}}
@if(isset($selectedPost) && $selectedPost)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6>Reactions for Post: "{{ $selectedPost->titre }}"</h6>
                    <p class="text-sm mb-0">by {{ $selectedPost->user->name }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.posts.reactions.index') }}" class="btn btn-sm bg-gradient-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to All Reactions
                    </a>
                </div>
            </div>
<div class="card-body">
    @if($postReactions->count() > 0)
        @php
            $totalPostReactions = $postReactions->sum('count');
        @endphp

        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    This post has received <strong>{{ $totalPostReactions }} reactions</strong> in total.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>

        @foreach($postReactions as $reactionName => $data)
        <div class="mb-4">
            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                @if(isset($data['reaction']->icon) && isset($data['reaction']->color))
                    <i class="{{ $data['reaction']->icon }} {{ $data['reaction']->color }} fa-2x me-3"></i>
                @else
                    <i class="fas fa-smile text-primary fa-2x me-3"></i>
                @endif
                <div class="flex-grow-1">
                    <h6 class="mb-0 text-capitalize">{{ $reactionName }}</h6>
                    <p class="text-sm mb-0">{{ $data['count'] }} {{ Str::plural('person', $data['count']) }}</p>
                </div>
                <span class="badge bg-gradient-primary fs-6">{{ $data['count'] }}</span>
            </div>
            
            <div class="row">
                @foreach($data['users'] as $user)
                <div class="col-md-6 col-lg-4 mb-2">
                    <div class="d-flex align-items-center p-2 bg-gray-100 rounded">
                        <div class="avatar avatar-xs me-2">
                            <div class="bg-gradient-dark rounded-circle text-center d-flex align-items-center justify-content-center" 
                                 style="width: 30px; height: 30px;">
                                <span class="text-white text-xs font-weight-bold">
                                    {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                            <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="ms-2">
                            @php
                                $userReaction = $selectedPost->reactions
                                    ->where('user_id', $user->id)
                                    ->where('reaction.name', $reactionName)
                                    ->first();
                            @endphp
                            @if($userReaction)
                            <form action="{{ route('admin.posts.reactions.delete-single', ['post' => $selectedPost->id, 'reaction' => $userReaction->id]) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs bg-gradient-danger" 
                                        onclick="return confirm('Are you sure you want to delete this reaction from {{ $user->name }}?')" 
                                        title="Delete this reaction">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if($data['count'] > $data['users']->count())
                <div class="col-12">
                    <p class="text-sm text-muted mt-2">
                        <i class="fas fa-ellipsis-h me-1"></i>
                        and {{ $data['count'] - $data['users']->count() }} more...
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Delete All Reactions Button -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Danger Zone
                        </h6>
                        <p class="text-sm text-muted mb-3">
                            This will permanently delete all {{ $totalPostReactions }} reactions for this post. This action cannot be undone.
                        </p>
                        <form action="{{ route('admin.posts.reactions.delete', $selectedPost->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm bg-gradient-danger" 
                                    onclick="return confirm('Are you sure you want to delete ALL {{ $totalPostReactions }} reactions for this post? This action cannot be undone.')">
                                <i class="fas fa-trash me-1"></i>Delete All Reactions
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="text-center py-4">
            <i class="fas fa-smile fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">No reactions yet</h6>
            <p class="text-sm text-muted">This post hasn't received any reactions.</p>
        </div>
    @endif
</div>
        </div>
    </div>
</div>
@else
{{-- Sinon, afficher la liste de tous les posts --}}
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h6>All Posts with Reactions</h6>
                <div>
                    <span class="badge bg-gradient-dark">
                        Total: {{ $posts->total() }} posts
                    </span>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                @if($posts->count() > 0)
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Post</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Author</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reactions</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posts as $post)
                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">{{ $post->titre }}</h6>
                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($post->contenu, 50) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-weight-bold mb-0">{{ $post->user->name }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $post->user->email }}</p>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge badge-sm bg-gradient-success">{{ $post->reactions_count }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="text-secondary text-xs font-weight-bold">
                                        {{ $post->created_at->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.posts.reactions.index', ['post_id' => $post->id]) }}" 
                                           class="btn btn-sm bg-gradient-warning me-1"
                                           title="View reactions">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($post->reactions_count > 0)
                                        <form action="{{ route('admin.posts.reactions.delete', $post->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-gradient-danger" 
                                                    onclick="return confirm('Are you sure you want to delete ALL reactions for this post?')" 
                                                    title="Delete all reactions">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

{{-- PAGINATION CENTRÉE --}}
@if($posts->hasPages())
<div class="card-footer">
    <div class="d-flex flex-column align-items-center">
        {{-- Informations sur les résultats --}}
        <div class="text-sm text-muted mb-2">
            Affichage de {{ $posts->firstItem() }} à {{ $posts->lastItem() }} sur {{ $posts->total() }} résultats
        </div>
        {{-- Pagination centrée --}}
        <div class="d-flex justify-content-center">
            {{ $posts->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endif

                @else
                <div class="text-center p-4">
                    <div class="text-muted">
                        <i class="fas fa-smile fa-2x mb-3"></i>
                        <p>No posts found matching your criteria.</p>
                        <a href="{{ route('admin.posts.reactions.index') }}" class="btn btn-sm bg-gradient-primary">Clear Filters</a>
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
.avatar {
    display: flex;
    align-items: center;
    justify-content: center;
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
.btn-xs {
    padding: 0.15rem 0.4rem;
    font-size: 0.75rem;
    line-height: 1;
    border-radius: 0.2rem;
}

.reaction-user-card {
    transition: all 0.3s ease;
}

.reaction-user-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
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

    // Confirmation pour la suppression
    const deleteForms = document.querySelectorAll('form[action*="reactions.delete"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you absolutely sure you want to delete all reactions for this post? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection