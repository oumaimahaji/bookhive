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
            return redirect()->route('moderator.dashboard'); // REDIRECTION CORRECTE
        } elseif ($user->isClubManager()) {
            return view('dashboard.club_manager'); 
        } else {
            return view('dashboard.user'); 
        }
    })->name('dashboard');

    // Dashboards par rôle - CORRIGÉ
    Route::get('/dashboard/moderator', [ModeratorController::class, 'dashboard'])
        ->name('dashboard.moderator')
        ->middleware(['auth']);

    // CRUD Livres et Catégories
    Route::resource('books', BookController::class);
    Route::resource('categories', CategoryController::class);

    // Routes Moderator - CORRIGÉ
    Route::prefix('moderator')->middleware(['auth'])->group(function() {
        Route::get('dashboard', [ModeratorController::class, 'dashboard'])
            ->name('moderator.dashboard');

        Route::get('books', [ModeratorController::class, 'books'])
            ->name('moderator.books');

        Route::patch('books/validate/{id}', [ModeratorController::class, 'validateBook'])
            ->name('moderator.books.validate');
    });

    // Gestion des utilisateurs - CORRIGÉ (SANS préfixe admin)
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