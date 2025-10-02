@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        <div class="row mb-3">
            <div class="col-12 text-start">
                <a href="{{ route('comments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour aux commentaires
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Erreurs :</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Modifier le Commentaire</h6>
                            <span class="badge bg-gradient-info">ID: {{ $comment->id }}</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pt-3 pb-3">
                        <form action="{{ route('comments.update', $comment->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Utilisateur <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-control" required>
                                        <option value="">-- Sélectionnez un utilisateur --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ (old('user_id', $comment->user_id) == $user->id) ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Utilisateur qui a écrit le commentaire</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Post <span class="text-danger">*</span></label>
                                    <select name="post_id" class="form-control" required>
                                        <option value="">-- Sélectionnez un post --</option>
                                        @foreach($posts as $post)
                                            <option value="{{ $post->id }}" 
                                                {{ (old('post_id', $comment->post_id) == $post->id) ? 'selected' : '' }}>
                                                {{ Str::limit($post->titre, 50) }} (par {{ $post->user->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Post associé au commentaire</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" 
                                       value="{{ old('date', $comment->date) }}" required>
                                <small class="form-text text-muted">Date de publication du commentaire</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Contenu du commentaire <span class="text-danger">*</span></label>
                                <textarea name="contenu" class="form-control" rows="6" 
                                          placeholder="Entrez le contenu du commentaire" required>{{ old('contenu', $comment->contenu) }}</textarea>
                                <small class="form-text text-muted">
                                    Caractères: <span id="charCount">{{ strlen(old('contenu', $comment->contenu)) }}</span>
                                </small>
                            </div>

                            <div class="card bg-gradient-light border mb-4">
                                <div class="card-body py-3">
                                    <h6 class="mb-2">Informations du commentaire</h6>
                                    <div class="row text-sm">
                                        <div class="col-md-6">
                                            <strong>Créé le:</strong> {{ $comment->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Modifié le:</strong> {{ $comment->updated_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('comments.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times me-2"></i>Annuler
                                    </a>
                                    <a href="{{ route('comments.create') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-plus me-2"></i>Nouveau
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="btn bg-gradient-primary">
                                        <i class="fas fa-save me-2"></i>Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Compteur de caractères
        const textarea = document.querySelector('textarea[name="contenu"]');
        const charCount = document.getElementById('charCount');
        
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        
        // Validation avant soumission
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const content = textarea.value.trim();
            if (content.length < 5) {
                e.preventDefault();
                alert('Le contenu du commentaire doit contenir au moins 5 caractères.');
                textarea.focus();
            }
        });
        
        // Focus sur le premier champ
        document.querySelector('select[name="user_id"]').focus();
    });
</script>

<style>
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: none;
    }
    
    .card-header {
        background: linear-gradient(45deg, #cb0c9f, #7928ca);
        color: white;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .form-control:focus {
        border-color: #cb0c9f;
        box-shadow: 0 0 0 2px rgba(203,12,159,0.2);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 150px;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 600;
    }
    
    .bg-gradient-light {
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    }
</style>
@endsection