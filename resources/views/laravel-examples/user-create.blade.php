@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Create New User</h6>
                        <a href="{{ route('user-management') }}" class="btn btn-secondary btn-sm">Back to Users</a>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <form action="{{ route('users.store') }}" method="POST" role="form" id="createUserForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Enter full name"
                                       required 
                                       minlength="2"
                                       maxlength="255">
                                <div class="form-text">
                                    <span id="nameCount">0</span>/255 characters
                                    <span id="nameStatus" class="ms-2"></span>
                                </div>
                                @error('name') 
                                    <div class="text-danger text-xs">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="Enter email address"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-envelope me-1"></i>Must be a valid email address
                                    <span id="emailStatus" class="ms-2"></span>
                                </div>
                                @error('email') 
                                    <div class="text-danger text-xs">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter password"
                                       required
                                       minlength="6">
                                <div class="form-text">
                                    <span id="passwordCount">0</span>/6 characters minimum
                                    <span id="passwordStatus" class="ms-2"></span>
                                </div>
                                @error('password') 
                                    <div class="text-danger text-xs">{{ $message }}</div> 
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm password"
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-shield-alt me-1"></i>Re-enter password for confirmation
                                    <span id="passwordConfirmStatus" class="ms-2"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                <option value="club_manager" {{ old('role') == 'club_manager' ? 'selected' : '' }}>Club Manager</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                            <div class="form-text">
                                <i class="fas fa-user-tag me-1"></i>Select user role
                                <span id="roleStatus" class="ms-2"></span>
                            </div>
                            @error('role') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}" 
                                   placeholder="Enter phone number (optional)"
                                   pattern="[0-9+\-\s()]{10,20}">
                            <div class="form-text">
                                <i class="fas fa-phone me-1"></i>Optional phone number (10-20 digits)
                                <span id="phoneStatus" class="ms-2"></span>
                            </div>
                            @error('phone') 
                                <div class="text-danger text-xs">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('user-management') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary" id="submitBtn">
                                <i class="fas fa-plus me-1"></i>Create User
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
.text-muted { color: #6c757d !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const form = document.getElementById('createUserForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const roleSelect = document.getElementById('role');
    const phoneInput = document.getElementById('phone');
    const submitBtn = document.getElementById('submitBtn');

    // Counters
    const nameCount = document.getElementById('nameCount');
    const passwordCount = document.getElementById('passwordCount');
    
    // Status elements
    const nameStatus = document.getElementById('nameStatus');
    const emailStatus = document.getElementById('emailStatus');
    const passwordStatus = document.getElementById('passwordStatus');
    const passwordConfirmStatus = document.getElementById('passwordConfirmStatus');
    const roleStatus = document.getElementById('roleStatus');
    const phoneStatus = document.getElementById('phoneStatus');

    // Initialize counters
    updateNameCount();
    updatePasswordCount();
    updateSubmitButton();

    // Event listeners
    nameInput.addEventListener('input', function() {
        updateNameCount();
        validateName();
        updateSubmitButton();
    });

    emailInput.addEventListener('input', function() {
        validateEmail();
        updateSubmitButton();
    });

    passwordInput.addEventListener('input', function() {
        updatePasswordCount();
        validatePassword();
        validatePasswordConfirm();
        updateSubmitButton();
    });

    passwordConfirmInput.addEventListener('input', function() {
        validatePasswordConfirm();
        updateSubmitButton();
    });

    roleSelect.addEventListener('change', function() {
        validateRole();
        updateSubmitButton();
    });

    phoneInput.addEventListener('input', function() {
        validatePhone();
    });

    // Update counters
    function updateNameCount() {
        const length = nameInput.value.length;
        nameCount.textContent = length;
        
        if (length < 2) {
            nameCount.className = 'text-danger';
            nameStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
        } else if (length > 200) {
            nameCount.className = 'text-warning';
            nameStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Long</span>';
        } else {
            nameCount.className = 'text-success';
            nameStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Good</span>';
        }
    }

    function updatePasswordCount() {
        const length = passwordInput.value.length;
        passwordCount.textContent = length;
        
        if (length < 8) {
            passwordCount.className = 'text-danger';
            passwordStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Too short</span>';
        } else if (length < 12) {
            passwordCount.className = 'text-warning';
            passwordStatus.innerHTML = '<span class="text-warning"><i class="fas fa-info-circle"></i> Fair</span>';
        } else {
            passwordCount.className = 'text-success';
            passwordStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Strong</span>';
        }
    }

    // Validation functions
    function validateName() {
        const length = nameInput.value.length;
        const isValid = length >= 2 && length <= 255;
        
        if (!isValid && nameInput.value) {
            nameInput.classList.add('is-invalid');
            nameInput.classList.remove('is-valid');
        } else if (isValid && nameInput.value) {
            nameInput.classList.remove('is-invalid');
            nameInput.classList.add('is-valid');
        } else {
            nameInput.classList.remove('is-invalid');
            nameInput.classList.remove('is-valid');
        }
        
        return isValid;
    }

    function validateEmail() {
        const email = emailInput.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(email);
        
        if (!isValid && emailInput.value) {
            emailInput.classList.add('is-invalid');
            emailInput.classList.remove('is-valid');
            emailStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Invalid</span>';
        } else if (isValid && emailInput.value) {
            emailInput.classList.remove('is-invalid');
            emailInput.classList.add('is-valid');
            emailStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Valid</span>';
        } else {
            emailInput.classList.remove('is-invalid');
            emailInput.classList.remove('is-valid');
            emailStatus.innerHTML = '';
        }
        
        return isValid;
    }

    function validatePassword() {
        const password = passwordInput.value;
        const isValid = password.length >= 8;
        
        if (!isValid && passwordInput.value) {
            passwordInput.classList.add('is-invalid');
            passwordInput.classList.remove('is-valid');
        } else if (isValid && passwordInput.value) {
            passwordInput.classList.remove('is-invalid');
            passwordInput.classList.add('is-valid');
        } else {
            passwordInput.classList.remove('is-invalid');
            passwordInput.classList.remove('is-valid');
        }
        
        return isValid;
    }

    function validatePasswordConfirm() {
        const password = passwordInput.value;
        const confirm = passwordConfirmInput.value;
        const isValid = password === confirm && password.length >= 8;
        
        if (!isValid && passwordConfirmInput.value) {
            passwordConfirmInput.classList.add('is-invalid');
            passwordConfirmInput.classList.remove('is-valid');
            passwordConfirmStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> No match</span>';
        } else if (isValid && passwordConfirmInput.value) {
            passwordConfirmInput.classList.remove('is-invalid');
            passwordConfirmInput.classList.add('is-valid');
            passwordConfirmStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Matches</span>';
        } else {
            passwordConfirmInput.classList.remove('is-invalid');
            passwordConfirmInput.classList.remove('is-valid');
            passwordConfirmStatus.innerHTML = '';
        }
        
        return isValid;
    }

    function validateRole() {
        const isValid = roleSelect.value !== '';
        
        if (!isValid) {
            roleSelect.classList.add('is-invalid');
            roleSelect.classList.remove('is-valid');
            roleStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Required</span>';
        } else {
            roleSelect.classList.remove('is-invalid');
            roleSelect.classList.add('is-valid');
            roleStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Selected</span>';
        }
        
        return isValid;
    }

    function validatePhone() {
        const phone = phoneInput.value;
        // Phone is optional, so only validate if there's content
        const isValid = phone === '' || /^[0-9+\-\s()]{10,20}$/.test(phone);
        
        if (!isValid && phoneInput.value) {
            phoneInput.classList.add('is-invalid');
            phoneInput.classList.remove('is-valid');
            phoneStatus.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Invalid</span>';
        } else if (isValid && phoneInput.value) {
            phoneInput.classList.remove('is-invalid');
            phoneInput.classList.add('is-valid');
            phoneStatus.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Valid</span>';
        } else {
            phoneInput.classList.remove('is-invalid');
            phoneInput.classList.remove('is-valid');
            phoneStatus.innerHTML = '';
        }
        
        return isValid;
    }

    function updateSubmitButton() {
        const isNameValid = nameInput.value.length >= 2 && nameInput.value.length <= 255;
        const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value);
        const isPasswordValid = passwordInput.value.length >= 8;
        const isPasswordConfirmValid = passwordInput.value === passwordConfirmInput.value && passwordInput.value.length >= 8;
        const isRoleValid = roleSelect.value !== '';
        
        const isFormValid = isNameValid && isEmailValid && isPasswordValid && isPasswordConfirmValid && isRoleValid;
        
        submitBtn.disabled = !isFormValid;
        
        if (isFormValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('bg-gradient-primary');
            submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i>Create User ✓';
        } else {
            submitBtn.classList.remove('bg-gradient-primary');
            submitBtn.classList.add('btn-secondary');
            submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i>Create User';
        }
    }

    // Prevent form submission if invalid
    form.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            // Show validation for all fields
            validateName();
            validateEmail();
            validatePassword();
            validatePasswordConfirm();
            validateRole();
            validatePhone();
            
            alert('Please correct the errors in the form before submitting.');
        }
    });

    // Focus on first field
    nameInput.focus();
});
</script>
@endsection