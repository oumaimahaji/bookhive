@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un Post</h5>
                    <a href="{{ route('posts.index') }}" class="btn bg-gradient-primary btn-sm mb-0">Retour à la liste</a>
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

                    <form action="{{ route('posts.store') }}" method="POST" id="postForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- User Selection -->
                        <div class="mb-3">
                            <label class="form-label">Utilisateur <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control" required id="userSelect">
                                <option value="">-- Sélectionnez un utilisateur --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Admin connecté: <strong>{{ auth()->user()->name }}</strong> sélectionné par défaut
                            </div>
                            <div class="invalid-feedback" id="userError" style="display: none;">
                                Veuillez sélectionner un utilisateur.
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" value="{{ old('titre') }}" 
                                   class="form-control" 
                                   placeholder="Entrez le titre du post (3-255 caractères)" 
                                   required 
                                   minlength="3"
                                   maxlength="255"
                                   id="titleInput">
                            <div class="form-text">
                                <span id="titleCount">{{ old('titre') ? strlen(old('titre')) : 0 }}</span>/255 caractères
                            </div>
                            <div class="invalid-feedback" id="titleError" style="display: none;">
                                Le titre doit contenir entre 3 et 255 caractères.
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label class="form-label">Contenu <span class="text-danger">*</span></label>
                            <textarea name="contenu" class="form-control" 
                                      rows="6" 
                                      placeholder="Entrez le contenu du post (minimum 10 caractères)" 
                                      required 
                                      minlength="10"
                                      id="contentTextarea">{{ old('contenu') }}</textarea>
                            <div class="form-text">
                                <span id="contentCount">{{ old('contenu') ? strlen(old('contenu')) : 0 }}</span> caractères | Minimum: 10 caractères
                            </div>
                            <div class="invalid-feedback" id="contentError" style="display: none;">
                                Le contenu doit contenir au moins 10 caractères.
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label class="form-label">Image (Optionnel)</label>
                            <div class="upload-area" id="uploadArea" style="border: 2px dashed #d1d5db; border-radius: 8px; padding: 2rem; text-align: center; background: #f9fafb; cursor: pointer;">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-700 mb-1">Cliquez pour ajouter une image</p>
                                        <p class="text-xs text-gray-500">ou glissez-déposez</p>
                                    </div>
                                    <input type="file" name="image" id="imageInput" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif"
                                           class="d-none">
                                    <button type="button" id="chooseFileBtn" 
                                            class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-folder-open me-2"></i>Choisir un fichier
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="d-none mt-3">
                                <div class="d-flex align-items-center justify-content-between bg-gray-50 p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <img id="previewImage" src="" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <p id="fileName" class="text-sm font-medium text-gray-700 mb-0"></p>
                                            <p id="fileSize" class="text-xs text-gray-500 mb-0"></p>
                                        </div>
                                    </div>
                                    <button type="button" id="removeImageBtn" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-text">
                                Formats: JPEG, PNG, JPG, GIF | Taille max: 2MB
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" 
                                   class="form-control" 
                                   required 
                                   id="dateInput">
                            <div class="form-text">
                                Date du post - Aujourd'hui par défaut
                            </div>
                            <div class="invalid-feedback" id="dateError" style="display: none;">
                                Veuillez sélectionner une date valide.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn bg-gradient-secondary" onclick="resetForm()">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </button>
                            <button type="submit" class="btn bg-gradient-primary" id="submitBtn">
                                <i class="fas fa-plus me-2"></i>Ajouter le Post
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
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control:focus.is-valid,
.form-control:focus.is-invalid {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control:focus.is-invalid {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

#titleCount, #contentCount {
    font-weight: 600;
}

#titleCount.limit-warning, #contentCount.limit-warning {
    color: #ffc107;
}

#titleCount.limit-danger, #contentCount.limit-danger {
    color: #dc3545;
}

.upload-area:hover {
    border-color: #3b82f6 !important;
    background: #f0f9ff !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const titleInput = document.getElementById('titleInput');
    const contentTextarea = document.getElementById('contentTextarea');
    const titleCount = document.getElementById('titleCount');
    const contentCount = document.getElementById('contentCount');
    const userSelect = document.getElementById('userSelect');
    const dateInput = document.getElementById('dateInput');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('postForm');

    // Éléments pour l'upload d'image
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('imageInput');
    const chooseFileBtn = document.getElementById('chooseFileBtn');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeImageBtn = document.getElementById('removeImageBtn');

    // Initialiser les compteurs avec les valeurs existantes
    updateTitleCount();
    updateContentCount();
    
    // Valider l'utilisateur au chargement (admin sélectionné par défaut)
    validateUser();

    // Compteur de caractères pour le titre
    titleInput.addEventListener('input', function() {
        updateTitleCount();
        validateTitle();
    });

    // Compteur de caractères pour le contenu
    contentTextarea.addEventListener('input', function() {
        updateContentCount();
        validateContent();
    });

    // Validation de l'utilisateur
    userSelect.addEventListener('change', function() {
        validateUser();
    });

    // Forcer la date d'aujourd'hui et désactiver les modifications
    dateInput.addEventListener('change', function() {
        // Réinitialiser à la date d'aujourd'hui si l'utilisateur essaie de changer
        this.value = new Date().toISOString().split('T')[0];
    });

    // Événements pour l'upload d'image
    uploadArea.addEventListener('click', function() {
        imageInput.click();
    });

    chooseFileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        imageInput.click();
    });

    imageInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    imagePreview.classList.remove('d-none');
                    imagePreview.classList.add('d-block');
                    uploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imagePreview.classList.remove('d-block');
        imagePreview.classList.add('d-none');
        uploadArea.style.display = 'block';
        imageInput.value = '';
    });

    // Fonctions de mise à jour des compteurs
    function updateTitleCount() {
        const length = titleInput.value.length;
        titleCount.textContent = length;
        
        if (length > 200) {
            titleCount.className = 'limit-danger';
        } else if (length > 150) {
            titleCount.className = 'limit-warning';
        } else {
            titleCount.className = '';
        }
    }

    function updateContentCount() {
        const length = contentTextarea.value.length;
        contentCount.textContent = length;
        
        if (length < 10) {
            contentCount.className = 'limit-danger';
        } else if (length < 20) {
            contentCount.className = 'limit-warning';
        } else {
            contentCount.className = '';
        }
    }

    // Fonctions de validation
    function validateTitle() {
        const length = titleInput.value.length;
        const isValid = length >= 3 && length <= 255;
        
        if (titleInput.value && !isValid) {
            titleInput.classList.add('is-invalid');
            document.getElementById('titleError').style.display = 'block';
        } else {
            titleInput.classList.remove('is-invalid');
            document.getElementById('titleError').style.display = 'none';
        }
        
        if (titleInput.value && isValid) {
            titleInput.classList.add('is-valid');
        } else {
            titleInput.classList.remove('is-valid');
        }
        
        updateSubmitButton();
    }

    function validateContent() {
        const length = contentTextarea.value.length;
        const isValid = length >= 10;
        
        if (contentTextarea.value && !isValid) {
            contentTextarea.classList.add('is-invalid');
            document.getElementById('contentError').style.display = 'block';
        } else {
            contentTextarea.classList.remove('is-invalid');
            document.getElementById('contentError').style.display = 'none';
        }
        
        if (contentTextarea.value && isValid) {
            contentTextarea.classList.add('is-valid');
        } else {
            contentTextarea.classList.remove('is-valid');
        }
        
        updateSubmitButton();
    }

    function validateUser() {
        const isValid = userSelect.value !== '';
        
        if (!isValid) {
            userSelect.classList.add('is-invalid');
            document.getElementById('userError').style.display = 'block';
        } else {
            userSelect.classList.remove('is-invalid');
            document.getElementById('userError').style.display = 'none';
        }
        
        if (isValid) {
            userSelect.classList.add('is-valid');
        } else {
            userSelect.classList.remove('is-valid');
        }
        
        updateSubmitButton();
    }

    // Mettre à jour l'état du bouton de soumission
    function updateSubmitButton() {
        const isTitleValid = titleInput.value.length >= 3 && titleInput.value.length <= 255;
        const isContentValid = contentTextarea.value.length >= 10;
        const isUserValid = userSelect.value !== '';
        
        const isFormValid = isTitleValid && isContentValid && isUserValid;
        
        submitBtn.disabled = !isFormValid;
        
        if (isFormValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('bg-gradient-primary');
        } else {
            submitBtn.classList.remove('bg-gradient-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }

    // Validation initiale seulement si des valeurs existent
    if (titleInput.value) validateTitle();
    if (contentTextarea.value) validateContent();

    // Empêcher la soumission si le formulaire n'est pas valide
    form.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            showToast('Veuillez corriger les erreurs dans le formulaire.', 'error');
            
            // Forcer la validation de tous les champs
            validateTitle();
            validateContent();
            validateUser();
        }
    });

    // Fonction de réinitialisation
    window.resetForm = function() {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ? Toutes les données seront perdues.')) {
            form.reset();
            
            // Réinitialiser la date à aujourd'hui
            dateInput.value = new Date().toISOString().split('T')[0];
            
            // Resélectionner l'admin par défaut
            userSelect.value = '{{ auth()->id() }}';
            
            // Réinitialiser l'image
            imagePreview.classList.remove('d-block');
            imagePreview.classList.add('d-none');
            uploadArea.style.display = 'block';
            imageInput.value = '';
            
            // Réinitialiser les compteurs
            updateTitleCount();
            updateContentCount();
            
            // Réinitialiser les validations
            titleInput.classList.remove('is-valid', 'is-invalid');
            contentTextarea.classList.remove('is-valid', 'is-invalid');
            userSelect.classList.remove('is-invalid');
            userSelect.classList.add('is-valid');
            
            // Cacher les messages d'erreur
            document.getElementById('titleError').style.display = 'none';
            document.getElementById('contentError').style.display = 'none';
            document.getElementById('userError').style.display = 'none';
            
            // Mettre à jour le bouton
            updateSubmitButton();
            
            showToast('Formulaire réinitialisé.', 'success');
        }
    };

    // Fonction pour formater la taille du fichier
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Fonction pour afficher les toasts (notification)
    function showToast(message, type = 'info') {
        // Créer un toast Bootstrap simple
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.style.minWidth = '300px';
        
        document.body.appendChild(toast);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
});
</script>
@endsection