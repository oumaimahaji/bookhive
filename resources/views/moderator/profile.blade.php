@extends('layouts.user_type.auth')

@section('content')
<div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
    <div class="container-fluid">
        <!-- Header avec image de fond -->
        <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('../assets/img/curved-images/curved0.jpg'); background-position-y: 50%;">
            <span class="mask bg-gradient-success opacity-6"></span>
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
                            📚 {{ ucfirst(auth()->user()->role) }} • Moderator since {{ auth()->user()->created_at->format('M Y') }}
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
        <div class="row">
            <!-- Colonne gauche - Paramètres du modérateur -->
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Moderator Settings</h6>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder">Book Management</h6>
                        <ul class="list-group">
                            <li class="list-group-item border-0 px-0">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-auto" type="checkbox" id="bookNotifications" checked>
                                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="bookNotifications">
                                        Book validation notifications
                                    </label>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-auto" type="checkbox" id="emailAlerts" checked>
                                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="emailAlerts">
                                        Email alerts for new books
                                    </label>
                                </div>
                            </li>
                        </ul>
                        
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4">Statistics</h6>
                        <ul class="list-group">
                            <li class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-book text-success me-2"></i>
                                    <span class="text-sm">Books validated this month: <strong>24</strong></span>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <span class="text-sm">Pending books: <strong>{{ \App\Models\Book::where('is_valid', false)->count() }}</strong></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Colonne centrale - Informations profil et changement de mot de passe -->
            <div class="col-12 col-xl-8">
                <!-- Messages de statut -->
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

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Please check the form below for errors.
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
                                <form method="POST" action="{{ route('moderator.profile.update') }}" id="profileForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-control-label">Full Name</label>
                                                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                                @error('name')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="form-control-label">Email</label>
                                                <input class="form-control" type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
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
                                                <input class="form-control" type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                                                @error('phone')
                                                <div class="text-danger text-xs">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Moderator Since</label>
                                                <p class="form-control-static">{{ auth()->user()->created_at->format('F d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-primary">
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
                                <form method="POST" action="{{ route('moderator.password.update') }}" id="changePasswordForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="current_password" class="form-control-label">Current Password</label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="current_password" name="current_password" required>
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        @error('current_password')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password" class="form-control-label">New Password</label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="new_password" name="new_password" required minlength="8">
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="form-text">
                                            Password must be at least 8 characters long.
                                        </div>
                                        @error('new_password')
                                        <div class="text-danger text-xs">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password_confirmation" class="form-control-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <input class="form-control" type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                                            <span class="input-group-text toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <div id="passwordMatch" class="form-text"></div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="updatePasswordBtn">
                                            <i class="fas fa-key me-1"></i>Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

        // Check password confirmation
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('new_password_confirmation');
        const passwordMatch = document.getElementById('passwordMatch');

        function checkPasswordMatch() {
            if (newPassword && confirmPassword && newPassword.value && confirmPassword.value) {
                if (newPassword.value === confirmPassword.value) {
                    passwordMatch.innerHTML = '<i class="fas fa-check text-success"></i> Passwords match';
                    passwordMatch.className = 'form-text text-success';
                } else {
                    passwordMatch.innerHTML = '<i class="fas fa-times text-danger"></i> Passwords do not match';
                    passwordMatch.className = 'form-text text-danger';
                }
            } else {
                passwordMatch.innerHTML = '';
                passwordMatch.className = 'form-text';
            }
        }

        if (newPassword && confirmPassword) {
            newPassword.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
        }

        // Tab switching
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

        // Form validation
        const profileForm = document.getElementById('profileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                
                if (!name || !email) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        }
    });
</script>

<style>
    .toggle-password {
        cursor: pointer;
        background-color: #f8f9fa;
        border: 1px solid #d2d6da;
        border-left: none;
    }
    .toggle-password:hover {
        background-color: #e9ecef;
    }
    .form-control:focus + .input-group-text {
        border-color: #cb0c9f;
        box-shadow: 0 0 0 2px rgba(203, 12, 159, 0.25);
    }
</style>
@endsection