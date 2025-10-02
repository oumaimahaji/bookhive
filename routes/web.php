<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\ClubManagerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// -------------------- ROUTES POUR LES UTILISATEURS AUTHENTIFIÉS --------------------
Route::middleware(['auth'])->group(function () {

    // Home
    Route::get('/', [HomeController::class, 'home'])->name('home');

    // Pages statiques
    Route::view('billing', 'billing')->name('billing');
    Route::view('profile', 'profile')->name('profile');
    Route::view('rtl', 'rtl')->name('rtl');
    Route::view('tables', 'tables')->name('tables');
    Route::view('virtual-reality', 'virtual-reality')->name('virtual-reality');

    // Gestion du profil
    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');
    Route::get('/user-profile', [InfoUserController::class, 'create'])->name('user-profile.create');
    Route::post('/user-profile', [InfoUserController::class, 'store'])->name('user-profile.store');

    // Dashboard central : redirection selon rôle
    Route::get('/dashboard', function () {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return view('dashboard'); 
        } elseif ($user->isModerator()) {
            return redirect()->route('moderator.dashboard');
        } elseif ($user->isClubManager()) {
            return redirect()->route('club_manager.dashboard');
        } else {
            return view('dashboard.user'); 
        }
    })->name('dashboard');

    // Dashboards par rôle
    Route::get('/dashboard/moderator', [ModeratorController::class, 'dashboard'])
        ->name('dashboard.moderator')
        ->middleware(['auth']);

    // CRUD Livres et Catégories
    Route::resource('books', BookController::class);
    Route::resource('categories', CategoryController::class);

    // Routes Moderator
    Route::prefix('moderator')->middleware(['auth'])->group(function() {
        Route::get('dashboard', [ModeratorController::class, 'dashboard'])
            ->name('moderator.dashboard');

        Route::get('books', [ModeratorController::class, 'books'])
            ->name('moderator.books');

        Route::patch('books/validate/{id}', [ModeratorController::class, 'validateBook'])
            ->name('moderator.books.validate');
    });

    // Club Manager Routes - COMPLÈTES
    Route::prefix('club-manager')->middleware(['auth'])->group(function() {
        Route::get('dashboard', [ClubManagerController::class, 'dashboard'])
            ->name('club_manager.dashboard');

        // Clubs CRUD COMPLET
        Route::get('clubs', [ClubManagerController::class, 'indexClub'])
            ->name('club_manager.clubs.index');
        Route::get('clubs/create', [ClubManagerController::class, 'createClub'])
            ->name('club_manager.clubs.create');
        Route::post('clubs', [ClubManagerController::class, 'storeClub'])
            ->name('club_manager.clubs.store');
        Route::get('clubs/{id}/edit', [ClubManagerController::class, 'editClub'])
            ->name('club_manager.clubs.edit');
        Route::put('clubs/{id}', [ClubManagerController::class, 'updateClub'])
            ->name('club_manager.clubs.update');
        Route::delete('clubs/{id}', [ClubManagerController::class, 'destroyClub'])
            ->name('club_manager.clubs.destroy');

        // Events CRUD SIMPLIFIÉ (comme les clubs)
        Route::get('events', [ClubManagerController::class, 'indexEvent'])
            ->name('club_manager.events.index');
        Route::get('events/create', [ClubManagerController::class, 'createEvent'])
            ->name('club_manager.events.create');
        Route::post('events', [ClubManagerController::class, 'storeEvent'])
            ->name('club_manager.events.store');
        Route::get('events/{id}/edit', [ClubManagerController::class, 'editEvent'])
            ->name('club_manager.events.edit');
        Route::put('events/{id}', [ClubManagerController::class, 'updateEvent'])
            ->name('club_manager.events.update');
        Route::delete('events/{id}', [ClubManagerController::class, 'destroyEvent'])
            ->name('club_manager.events.destroy');
    });

    // Gestion des utilisateurs
    Route::get('/user-management', [InfoUserController::class, 'userManagement'])->name('user-management');
    Route::get('/users/create', [InfoUserController::class, 'createUser'])->name('users.create');
    Route::post('/users', [InfoUserController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [InfoUserController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [InfoUserController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [InfoUserController::class, 'destroyUser'])->name('users.destroy');

});

// -------------------- ROUTES POUR LES INVITÉS --------------------
Route::middleware(['guest'])->group(function () {
    // Authentification
    Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store'])->name('login.store');

    // Mot de passe oublié / réinitialisation
    Route::get('/login/forgot-password', [ResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

    // Login social
    Route::get('/auth/google/redirect', [SocialAuthController::class, 'googleRedirect']);
    Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback']);
    Route::get('/auth/facebook/redirect', [SocialAuthController::class, 'facebookRedirect']);
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);
});