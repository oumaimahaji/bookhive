@extends('layouts.user_type.auth')

@section('content')
<div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
    <div class="container-fluid">
        <!-- Header avec image de fond -->
        <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('../assets/img/curved-images/curved0.jpg'); background-position-y: 50%;">
            <span class="mask bg-gradient-primary opacity-6"></span>
        </div>

        <!-- Carte profil -->
        <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
            <div class="row gx-4">
                <div class="col-auto">
                    <div class="avatar avatar-xl position-relative">
                        @if(auth()->user()->profile_picture)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                        @else
                        <img src="../assets/img/bruce-mars.jpg" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                        @endif
                    </div>
                </div>
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">
                            {{ auth()->user()->name }}
                        </h5>
                        <p class="mb-0 font-weight-bold text-sm">
                            👑 {{ ucfirst(auth()->user()->role) }} • Admin since {{ auth()->user()->created_at->format('M Y') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
                    <div class="nav-wrapper position-relative end-0">
                        <ul class="nav nav-pills nav-fill p-1 bg-transparent" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#profile" role="tab" aria-selected="true">
                                    <i class="fas fa-user text-dark me-1"></i>
                                    <span class="ms-1">Profile</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#password" role="tab" aria-selected="false">
                                    <i class="fas fa-lock text-dark me-1"></i>
                                    <span class="ms-1">Password</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <!-- Colonne des paramètres -->
            <div class="col-12 col-xl-3 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Admin Settings</h6>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Account</h6>
                        <ul class="list-group">
                            <li class="list-group-item border-0 px-0">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-auto" type="checkbox" id="emailFollows" checked>
                                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="emailFollows">
                                        Email notifications
                                    </label>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-auto" type="checkbox" id="emailAnswers" checked>
                                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="emailAnswers">
                                        System alerts
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Colonne centrale - Informations profil et changement de mot de passe -->
            <div class="col-12 col-xl-6">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Contenu des onglets -->
                <div class="tab-content">
                    <!-- Onglet Profil -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel">
                        <div class="card h-100 mb-4">
                            <div class="card-header pb-0 p-3">
                                <div class="row">
                                    <div class="col-md-8 d-flex align-items-center">
                                        <h6 class="mb-0">Profile Information</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <form method="POST" action="{{ route('user-profile.store') }}" id="profileForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-control-label">Full Name <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" id="name" name="name"
                                                    value="{{ old('name', auth()->user()->name) }}"
                                                    required
                                                    minlength="2"
                                                    maxlength="50"
                                                    pattern="[A-Za-zÀ-ÿ\s]{2,50}"
                                                    title="Le nom doit contenir entre 2 et 50 caractères alphabétiques">
                                                <div class="form-text text-xs">2-50 caractères, lettres uniquement</div>
                                                @error('name')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="form-control-label">Email <span class="text-danger">*</span></label>
                                                <input class="form-control" type="email" id="email" name="email"
                                                    value="{{ old('email', auth()->user()->email) }}"
                                                    required
                                                    maxlength="255"
                                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                    title="Format d'email valide requis">
                                                <div class="form-text text-xs">Format: exemple@domain.com</div>
                                                @error('email')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone" class="form-control-label">Phone</label>
                                                <input class="form-control" type="tel" id="phone" name="phone"
                                                    value="{{ old('phone', auth()->user()->phone) }}"
                                                    pattern="[\+]?[0-9\s\-\(\)]{10,20}"
                                                    maxlength="20"
                                                    title="Format de téléphone valide (10-20 chiffres)">
                                                <div class="form-text text-xs">Format: +33 1 23 45 67 89 ou 0123456789</div>
                                                @error('phone')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Admin Since</label>
                                                <p class="form-control-static">{{ auth()->user()->created_at->format('F d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-primary" id="updateProfileBtn">
                                            <i class="fas fa-save me-1"></i>Update Profile
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Mot de passe -->
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <div class="card h-100 mb-4">
                            <div class="card-header pb-0 p-3">
                                <h6 class="mb-0">Change Password</h6>
                            </div>
                            <div class="card-body p-3">
                                <form method="POST" action="{{ route('user-password.update') }}" id="changePasswordForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="current_password" class="form-control-label">Current Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="current_password" name="current_password"
                                                required
                                                minlength="1">
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        @error('current_password')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password" class="form-control-label">New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="new_password" name="new_password"
                                                required
                                                minlength="8"
                                                maxlength="100"
                                                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                                                title="Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial">
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">
                                            <small>
                                                <ul class="list-unstyled mb-0">
                                                    <li id="lengthCheck" class="text-xs"><i class="fas fa-circle me-1"></i> Au moins 8 caractères</li>
                                                    <li id="uppercaseCheck" class="text-xs"><i class="fas fa-circle me-1"></i> Une majuscule (A-Z)</li>
                                                    <li id="lowercaseCheck" class="text-xs"><i class="fas fa-circle me-1"></i> Une minuscule (a-z)</li>
                                                    <li id="numberCheck" class="text-xs"><i class="fas fa-circle me-1"></i> Un chiffre (0-9)</li>
                                                    <li id="specialCheck" class="text-xs"><i class="fas fa-circle me-1"></i> Un caractère spécial (@$!%*?&)</li>
                                                </ul>
                                            </small>
                                        </div>
                                        @error('new_password')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password_confirmation" class="form-control-label">Confirm New Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="new_password_confirmation" name="new_password_confirmation"
                                                required
                                                minlength="8">
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div id="passwordMatch" class="form-text"></div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="updatePasswordBtn" disabled>
                                            <i class="fas fa-key me-1"></i>Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite vide pour équilibrer -->
            <div class="col-12 col-xl-3"></div>
        </div>
    </div>
</div>

<style>
    .toggle-password {
        cursor: pointer;
        background: #f8f9fa;
        border: 1px solid #ced4da;
        border-left: none;
    }

    .toggle-password:hover {
        background: #e9ecef;
    }

    .form-control-static {
        padding: 0.5rem 0;
        margin-bottom: 0;
        line-height: 1.5;
        border: 0;
        background: transparent;
        color: #6c757d;
    }

    .form-text ul {
        margin-bottom: 0;
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Gestion des onglets
        document.querySelectorAll('.nav-link').forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                document.querySelectorAll('.nav-link').forEach(function(t) {
                    t.classList.remove('active');
                });

                this.classList.add('active');

                const target = this.getAttribute('href');
                document.querySelectorAll('.tab-pane').forEach(function(pane) {
                    pane.classList.remove('show', 'active');
                });
                document.querySelector(target).classList.add('show', 'active');
            });
        });

        // Validation en temps réel du mot de passe
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('new_password_confirmation');
        const updatePasswordBtn = document.getElementById('updatePasswordBtn');

        // Éléments de vérification
        const lengthCheck = document.getElementById('lengthCheck');
        const uppercaseCheck = document.getElementById('uppercaseCheck');
        const lowercaseCheck = document.getElementById('lowercaseCheck');
        const numberCheck = document.getElementById('numberCheck');
        const specialCheck = document.getElementById('specialCheck');

        function validatePassword() {
            const password = newPassword.value;

            // Vérifications individuelles
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[@$!%*?&]/.test(password);

            // Mise à jour des icônes
            updateCheckIcon(lengthCheck, hasLength);
            updateCheckIcon(uppercaseCheck, hasUppercase);
            updateCheckIcon(lowercaseCheck, hasLowercase);
            updateCheckIcon(numberCheck, hasNumber);
            updateCheckIcon(specialCheck, hasSpecial);

            // Vérification de la confirmation
            checkPasswordMatch();

            // Activation du bouton
            const isValid = hasLength && hasUppercase && hasLowercase && hasNumber && hasSpecial;
            const passwordsMatch = newPassword.value === confirmPassword.value && newPassword.value !== '';

            updatePasswordBtn.disabled = !(isValid && passwordsMatch);
        }

        function updateCheckIcon(element, isValid) {
            const icon = element.querySelector('i');
            if (isValid) {
                icon.className = 'fas fa-check text-success me-1';
                element.classList.add('text-success');
                element.classList.remove('text-warning');
            } else {
                icon.className = 'fas fa-circle me-1';
                element.classList.remove('text-success', 'text-warning');
            }
        }

        function checkPasswordMatch() {
            const passwordMatch = document.getElementById('passwordMatch');
            if (newPassword.value && confirmPassword.value) {
                if (newPassword.value === confirmPassword.value) {
                    passwordMatch.innerHTML = '<i class="fas fa-check text-success me-1"></i> Les mots de passe correspondent';
                    passwordMatch.className = 'form-text text-success';
                } else {
                    passwordMatch.innerHTML = '<i class="fas fa-times text-danger me-1"></i> Les mots de passe ne correspondent pas';
                    passwordMatch.className = 'form-text text-danger';
                }
            } else {
                passwordMatch.innerHTML = '';
                passwordMatch.className = 'form-text';
            }
        }

        // Événements
        if (newPassword) {
            newPassword.addEventListener('input', validatePassword);
        }

        if (confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                checkPasswordMatch();
                validatePassword();
            });
        }

        // Validation du formulaire de profil
        const profileForm = document.getElementById('profileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const name = document.getElementById('name').value;
                const email = document.getElementById('email').value;

                if (!name || !email) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs obligatoires');
                    return false;
                }

                // Afficher l'indicateur de chargement
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mise à jour...';
                btn.disabled = true;
            });
        }

        // Validation initiale
        validatePassword();
    });
</script>
@endsection