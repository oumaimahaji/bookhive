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
    <div class="row">
      <!-- Colonne gauche - Paramètres -->
      <div class="col-12 col-xl-4">
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
                    Email me for user registrations
                  </label>
                </div>
              </li>
              <li class="list-group-item border-0 px-0">
                <div class="form-check form-switch ps-0">
                  <input class="form-check-input ms-auto" type="checkbox" id="emailAnswers" checked>
                  <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="emailAnswers">
                    Email me for system alerts
                  </label>
                </div>
              </li>
              <li class="list-group-item border-0 px-0">
                <div class="form-check form-switch ps-0">
                  <input class="form-check-input ms-auto" type="checkbox" id="emailMentions" checked>
                  <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="emailMentions">
                    Email me for reports
                  </label>
                </div>
              </li>
            </ul>
            <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4">System</h6>
            <ul class="list-group">
              <li class="list-group-item border-0 px-0">
                <div class="form-check form-switch ps-0">
                  <input class="form-check-input ms-auto" type="checkbox" id="notifNewBooks" checked>
                  <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="notifNewBooks">
                    System notifications
                  </label>
                </div>
              </li>
              <li class="list-group-item border-0 px-0">
                <div class="form-check form-switch ps-0">
                  <input class="form-check-input ms-auto" type="checkbox" id="notifClubActivities" checked>
                  <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="notifClubActivities">
                    Performance reports
                  </label>
                </div>
              </li>
              <li class="list-group-item border-0 px-0 pb-0">
                <div class="form-check form-switch ps-0">
                  <input class="form-check-input ms-auto" type="checkbox" id="notifNewsletter">
                  <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0" for="notifNewsletter">
                    Security alerts
                  </label>
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
                <form method="POST" action="{{ route('admin.profile.update') }}" id="profileForm">
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
                        <input class="form-control" type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
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
                <form method="POST" action="{{ route('admin.password.update') }}" id="changePasswordForm">
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

        <!-- Section Statistiques Admin -->
        <div class="card mb-4">
          <div class="card-header pb-0 p-3">
            <h6 class="mb-0">System Statistics</h6>
          </div>
          <div class="card-body p-3">
            <div class="row">
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="card-body p-3">
                    <div class="row">
                      <div class="col-8">
                        <div class="numbers">
                          <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Users</p>
                          <h5 class="font-weight-bolder mb-0">
                            {{ $stats['totalUsers'] ?? 0 }}
                          </h5>
                        </div>
                      </div>
                      <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                          <i class="fas fa-users text-lg opacity-10"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="card-body p-3">
                    <div class="row">
                      <div class="col-8">
                        <div class="numbers">
                          <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Books</p>
                          <h5 class="font-weight-bolder mb-0">
                            {{ $stats['totalBooks'] ?? 0 }}
                          </h5>
                        </div>
                      </div>
                      <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                          <i class="fas fa-book text-lg opacity-10"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="card-body p-3">
                    <div class="row">
                      <div class="col-8">
                        <div class="numbers">
                          <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Clubs</p>
                          <h5 class="font-weight-bolder mb-0">
                            {{ $stats['totalClubs'] ?? 0 }}
                          </h5>
                        </div>
                      </div>
                      <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                          <i class="fas fa-users text-lg opacity-10"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card card-blog card-plain">
                  <div class="card-body p-3">
                    <div class="row">
                      <div class="col-8">
                        <div class="numbers">
                          <p class="text-sm mb-0 text-capitalize font-weight-bold">Reservations</p>
                          <h5 class="font-weight-bolder mb-0">
                            {{ $stats['totalReservations'] ?? 0 }}
                          </h5>
                        </div>
                      </div>
                      <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                          <i class="fas fa-calendar-check text-lg opacity-10"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
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

        // Remove active class from all tabs
        document.querySelectorAll('.nav-link').forEach(function(t) {
          t.classList.remove('active');
        });

        // Add active class to clicked tab
        this.classList.add('active');

        // Show corresponding tab content
        const target = this.getAttribute('href');
        document.querySelectorAll('.tab-pane').forEach(function(pane) {
          pane.classList.remove('show', 'active');
        });
        document.querySelector(target).classList.add('show', 'active');
      });
    });

    // Form submission loading states
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('changePasswordForm');

    if (profileForm) {
      profileForm.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
        btn.disabled = true;
      });
    }

    if (passwordForm) {
      passwordForm.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Updating...';
        btn.disabled = true;
      });
    }
  });
</script>
@endsection