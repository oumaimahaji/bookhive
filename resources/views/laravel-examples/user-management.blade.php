@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                <span class="alert-text">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                <span class="alert-text">{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">All Users</h5>
                                <p class="text-sm mb-0">Manage moderators, club managers and users</p>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-sm me-2" id="toggleSearchBtn">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                                <a href="{{ route('users.create') }}" class="btn bg-gradient-primary btn-sm mb-0" type="button">
                                    <i class="fas fa-plus me-1"></i> New User
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Advanced Search Bar - CACHÉ PAR DÉFAUT --}}
                    <div class="card-body d-none" id="searchSection">
                        <div class="card">
                            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                                <h6>Advanced Search</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="closeSearchBtn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('user-management') }}" method="GET" id="searchForm">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="Search by name..."
                                                   value="{{ request('name') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="text" name="email" class="form-control" placeholder="Search by email..."
                                                   value="{{ request('email') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Role</label>
                                            <select name="role" class="form-control">
                                                <option value="">All Roles</option>
                                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                                <option value="moderator" {{ request('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                                <option value="club_manager" {{ request('role') == 'club_manager' ? 'selected' : '' }}>Club Manager</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="phone" class="form-control" placeholder="Search by phone..."
                                                   value="{{ request('phone') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Creation Date</label>
                                            <input type="date" name="created_at" class="form-control"
                                                   value="{{ request('created_at') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sort By</label>
                                            <select name="sort" class="form-control">
                                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email A-Z</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 d-flex justify-content-between align-items-center">
                                            <div>
                                                <button type="submit" class="btn bg-gradient-primary">
                                                    <i class="fas fa-search me-2"></i>Search
                                                </button>
                                                <a href="{{ route('user-management') }}" class="btn bg-gradient-secondary">
                                                    <i class="fas fa-refresh me-2"></i>Reset
                                                </a>
                                            </div>
                                            <div class="text-end">
                                                <span class="text-sm text-muted">
                                                    Found {{ $users->count() }} results
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Active Filters --}}
                    @if(request()->anyFilled(['name', 'email', 'role', 'phone', 'created_at']))
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <span class="text-sm text-muted me-3">Active Filters:</span>
                            <div class="d-flex flex-wrap gap-2">
                                @if(request('name'))
                                <span class="badge bg-gradient-primary">
                                    Name: "{{ request('name') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['name' => null]) }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                                @endif

                                @if(request('email'))
                                <span class="badge bg-gradient-info">
                                    Email: "{{ request('email') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['email' => null]) }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                                @endif

                                @if(request('role'))
                                <span class="badge bg-gradient-success">
                                    Role: {{ ucfirst(request('role')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['role' => null]) }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                                @endif

                                @if(request('phone'))
                                <span class="badge bg-gradient-warning">
                                    Phone: "{{ request('phone') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['phone' => null]) }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                                @endif

                                @if(request('created_at'))
                                <span class="badge bg-gradient-dark">
                                    Created: {{ request('created_at') }}
                                    <a href="{{ request()->fullUrlWithQuery(['created_at' => null]) }}" class="text-white ms-1">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Inline Edit Form --}}
                    @if(isset($editUser))
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                                <h6>Edit User: <span class="text-primary">{{ $editUser->name }}</span></h6>
                                <a href="{{ route('user-management') }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('users.update', $editUser->id) }}" method="POST" id="editUserForm" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <!-- Name -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control"
                                                   value="{{ old('name', $editUser->name) }}"
                                                   placeholder="Enter full name"
                                                   required
                                                   minlength="2"
                                                   maxlength="255"
                                                   id="editNameInput">
                                            <div class="form-text">
                                                <span id="editNameCount">{{ strlen(old('name', $editUser->name)) }}</span>/255 characters
                                                <span id="editNameStatus" class="ms-2"></span>
                                            </div>
                                            <div class="invalid-feedback" id="editNameError">
                                                Name must be between 2 and 255 characters.
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control"
                                                   value="{{ old('email', $editUser->email) }}"
                                                   placeholder="Enter email address"
                                                   required
                                                   id="editEmailInput">
                                            <div class="form-text">
                                                <i class="fas fa-envelope me-1"></i>Must be a valid email address
                                            </div>
                                            <div class="invalid-feedback" id="editEmailError">
                                                Please enter a valid email address.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Phone -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" name="phone" class="form-control"
                                                   value="{{ old('phone', $editUser->phone) }}"
                                                   placeholder="Enter phone number"
                                                   pattern="[0-9+\-\s()]{10,20}"
                                                   id="editPhoneInput">
                                            <div class="form-text">
                                                <i class="fas fa-phone me-1"></i>Optional phone number
                                            </div>
                                            <div class="invalid-feedback" id="editPhoneError">
                                                Please enter a valid phone number (10-20 digits).
                                            </div>
                                        </div>

                                        <!-- Role -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Role <span class="text-danger">*</span></label>
                                            <select name="role" class="form-control" required id="editRoleSelect">
                                                <option value="user" {{ old('role', $editUser->role) == 'user' ? 'selected' : '' }}>User</option>
                                                <option value="moderator" {{ old('role', $editUser->role) == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                                <option value="club_manager" {{ old('role', $editUser->role) == 'club_manager' ? 'selected' : '' }}>Club Manager</option>
                                            </select>
                                            <div class="form-text">
                                                <i class="fas fa-user-tag me-1"></i>Select user role
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password (Optional) -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="password" class="form-control"
                                                   placeholder="Leave blank to keep current password"
                                                   minlength="8"
                                                   id="editPasswordInput">
                                            <div class="form-text">
                                                <span id="editPasswordCount">0</span>/8 characters minimum
                                                <span id="editPasswordStatus" class="ms-2"></span>
                                            </div>
                                            <div class="invalid-feedback" id="editPasswordError">
                                                Password must be at least 8 characters.
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                   placeholder="Confirm new password"
                                                   id="editPasswordConfirmInput">
                                            <div class="form-text">
                                                <i class="fas fa-shield-alt me-1"></i>Re-enter password for confirmation
                                            </div>
                                            <div class="invalid-feedback" id="editPasswordConfirmError">
                                                Passwords do not match.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn bg-gradient-success" id="editSubmitBtn">
                                            <i class="fas fa-save me-2"></i>Update User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Users Table - Only show when not editing --}}
                    @if(!isset($editUser))
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                                                ID
                                                @if(request('sort') == 'id')
                                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Photo</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                                                Name
                                                @if(request('sort') == 'name')
                                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                                                Email
                                                @if(request('sort') == 'email')
                                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Phone</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-dark text-decoration-none">
                                                Creation Date
                                                @if(request('sort') == 'created_at')
                                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="fas fa-sort"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->id }}</p>
                                        </td>
                                        <td>
                                            <div>
                                                @if($user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" class="avatar avatar-sm me-3" alt="{{ $user->name }}">
                                                @else
                                                    <img src="{{ asset('assets/img/team-2.jpg') }}" class="avatar avatar-sm me-3" alt="{{ $user->name }}">
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->name }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $user->phone ?? '-' }}</p>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-sm
                                                @if($user->role == 'moderator') bg-gradient-info
                                                @elseif($user->role == 'club_manager') bg-gradient-warning
                                                @else bg-gradient-secondary @endif">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center">
                                                {{-- ✅ BOUTON EDIT INLINE CORRIGÉ --}}
                                                <a href="{{ route('user-management', ['edit' => $user->id]) }}"
                                                   class="btn btn-outline-info btn-sm me-2"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-original-title="Edit user">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </a>

                                                {{-- ✅ BOUTON DELETE --}}
                                                <form action="{{ route('users.destroy', $user) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-outline-danger btn-sm"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="Delete user">
                                                        <i class="fas fa-trash me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach

                                    @if($users->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-xs text-secondary mb-0">No users found matching your criteria.</p>
                                            <a href="{{ route('user-management') }}" class="btn btn-sm bg-gradient-primary mt-2">Clear Filters</a>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</main>

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

.avatar {
    border-radius: 50%;
    object-fit: cover;
}

.badge a {
    text-decoration: none;
}

.table th a {
    text-decoration: none;
    color: inherit;
}

.table th a:hover {
    color: #007bff;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du toggle de recherche
    const toggleSearchBtn = document.getElementById('toggleSearchBtn');
    const closeSearchBtn = document.getElementById('closeSearchBtn');
    const searchSection = document.getElementById('searchSection');

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
                const firstInput = searchSection.querySelector('input, select');
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
    const editForm = document.getElementById('editUserForm');

    if (editForm) {
        // Éléments du DOM pour l'édition
        const editNameInput = document.getElementById('editNameInput');
        const editEmailInput = document.getElementById('editEmailInput');
        const editPhoneInput = document.getElementById('editPhoneInput');
        const editPasswordInput = document.getElementById('editPasswordInput');
        const editPasswordConfirmInput = document.getElementById('editPasswordConfirmInput');
        const editNameCount = document.getElementById('editNameCount');
        const editPasswordCount = document.getElementById('editPasswordCount');
        const editNameStatus = document.getElementById('editNameStatus');
        const editPasswordStatus = document.getElementById('editPasswordStatus');
        const editSubmitBtn = document.getElementById('editSubmitBtn');

        // Initialiser les compteurs et validations
        updateEditNameCount();
        updateEditPasswordCount();
        validateEditAll();

        // Événements de saisie
        editNameInput.addEventListener('input', function() {
            updateEditNameCount();
            validateEditName();
        });

        editEmailInput.addEventListener('input', function() {
            validateEditEmail();
        });

        editPhoneInput.addEventListener('input', function() {
            validateEditPhone();
        });

        editPasswordInput.addEventListener('input', function() {
            updateEditPasswordCount();
            validateEditPassword();
            validateEditPasswordConfirm();
        });

        editPasswordConfirmInput.addEventListener('input', function() {
            validateEditPasswordConfirm();
        });

        // Fonctions de mise à jour des compteurs
        function updateEditNameCount() {
            const length = editNameInput.value.length;
            editNameCount.textContent = length;

            if (length < 2) {
                editNameCount.className = 'text-danger';
                editNameStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
            } else if (length > 200) {
                editNameCount.className = 'text-warning';
                editNameStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Long</span>';
            } else {
                editNameCount.className = 'text-success';
                editNameStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Good</span>';
            }
        }

        function updateEditPasswordCount() {
            const length = editPasswordInput.value.length;
            editPasswordCount.textContent = length;

            if (length > 0 && length < 8) {
                editPasswordCount.className = 'text-danger';
                editPasswordStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
            } else if (length >= 8) {
                editPasswordCount.className = 'text-success';
                editPasswordStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Strong</span>';
            } else {
                editPasswordCount.className = 'text-muted';
                editPasswordStatus.innerHTML = '<span class="text-muted"><i class="fas fa-info-circle"></i> Optional</span>';
            }
        }

        // Fonctions de validation
        function validateEditName() {
            const length = editNameInput.value.length;
            const isValid = length >= 2 && length <= 255;

            if (!isValid) {
                editNameInput.classList.add('is-invalid');
                editNameInput.classList.remove('is-valid');
            } else {
                editNameInput.classList.remove('is-invalid');
                editNameInput.classList.add('is-valid');
            }

            updateEditSubmitButton();
            return isValid;
        }

        function validateEditEmail() {
            const email = editEmailInput.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const isValid = emailRegex.test(email);

            if (!isValid) {
                editEmailInput.classList.add('is-invalid');
                editEmailInput.classList.remove('is-valid');
            } else {
                editEmailInput.classList.remove('is-invalid');
                editEmailInput.classList.add('is-valid');
            }

            updateEditSubmitButton();
            return isValid;
        }

        function validateEditPhone() {
            const phone = editPhoneInput.value;
            // Phone is optional, so only validate if there's content
            const isValid = phone === '' || /^[0-9+\-\s()]{10,20}$/.test(phone);

            if (!isValid) {
                editPhoneInput.classList.add('is-invalid');
                editPhoneInput.classList.remove('is-valid');
            } else {
                editPhoneInput.classList.remove('is-invalid');
                editPhoneInput.classList.add('is-valid');
            }

            return isValid;
        }

        function validateEditPassword() {
            const password = editPasswordInput.value;
            // Password is optional, but if provided must be at least 8 chars
            const isValid = password === '' || password.length >= 8;

            if (!isValid) {
                editPasswordInput.classList.add('is-invalid');
                editPasswordInput.classList.remove('is-valid');
            } else {
                editPasswordInput.classList.remove('is-invalid');
                editPasswordInput.classList.add('is-valid');
            }

            updateEditSubmitButton();
            return isValid;
        }

        function validateEditPasswordConfirm() {
            const password = editPasswordInput.value;
            const confirm = editPasswordConfirmInput.value;
            // Only validate if password is provided
            const isValid = password === '' || password === confirm;

            if (!isValid) {
                editPasswordConfirmInput.classList.add('is-invalid');
                editPasswordConfirmInput.classList.remove('is-valid');
            } else {
                editPasswordConfirmInput.classList.remove('is-invalid');
                editPasswordConfirmInput.classList.add('is-valid');
            }

            updateEditSubmitButton();
            return isValid;
        }

        function validateEditAll() {
            updateEditNameCount();
            updateEditPasswordCount();
            validateEditName();
            validateEditEmail();
            validateEditPhone();
            validateEditPassword();
            validateEditPasswordConfirm();
        }

        // Mettre à jour l'état du bouton de soumission
        function updateEditSubmitButton() {
            const isNameValid = validateEditName();
            const isEmailValid = validateEditEmail();
            const isPasswordValid = validateEditPassword();
            const isPasswordConfirmValid = validateEditPasswordConfirm();

            const isFormValid = isNameValid && isEmailValid && isPasswordValid && isPasswordConfirmValid;

            editSubmitBtn.disabled = !isFormValid;

            if (isFormValid) {
                editSubmitBtn.classList.remove('btn-secondary');
                editSubmitBtn.classList.add('bg-gradient-success');
                editSubmitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update User ✓';
            } else {
                editSubmitBtn.classList.remove('bg-gradient-success');
                editSubmitBtn.classList.add('btn-secondary');
                editSubmitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update User';
            }
        }

        // Empêcher la soumission si le formulaire n'est pas valide
        editForm.addEventListener('submit', function(e) {
            if (editSubmitBtn.disabled) {
                e.preventDefault();
                alert('Please correct the errors in the form before submitting.');
            }
        });
    }

    // Activation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection