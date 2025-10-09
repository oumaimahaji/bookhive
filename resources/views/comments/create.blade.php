@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un Commentaire</h5>
                    <a href="{{ route('comments.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
                </div>
                <div class="card-body px-4 pt-4 pb-2">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('comments.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Utilisateur</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Sélectionnez un utilisateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Post</label>
                            <select name="post_id" class="form-control" required>
                                <option value="">-- Sélectionnez un post --</option>
                                @foreach($posts as $post)
                                    <option value="{{ $post->id }}" {{ old('post_id') == $post->id ? 'selected' : '' }}>
                                        {{ $post->titre }} (par {{ $post->user->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contenu du commentaire</label>
                            <textarea name="contenu" class="form-control" rows="6" placeholder="Entrez le contenu du commentaire" required>{{ old('contenu') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="form-control" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('comments.index') }}" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn bg-gradient-primary">Ajouter le commentaire</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script pour améliorer l'expérience utilisateur
    document.addEventListener('DOMContentLoaded', function() {
        const postSelect = document.querySelector('select[name="post_id"]');
        const userSelect = document.querySelector('select[name="user_id"]');
        
        // Focus sur le premier champ
        userSelect.focus();
        
        // Validation visuelle
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const content = document.querySelector('textarea[name="contenu"]').value.trim();
            if (content.length < 5) {
                e.preventDefault();
                alert('Le contenu du commentaire doit contenir au moins 5 caractères.');
            }
        });
    });
</script>

<style>
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .form-control:focus {
        border-color: #cb0c9f;
        box-shadow: 0 0 0 2px rgba(203,12,159,0.2);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }
</style>
@endsection