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
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// -------------------- ROUTES PUBLIQUES (ACCESSIBLES À TOUS) --------------------
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
<<<<<<< HEAD
Route::get('/book/{book}', [FrontendController::class, 'showBook'])->name('frontend.book');
=======
>>>>>>> 542202f4aa11f6ef658af99c6362a14a0e23898e

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

// -------------------- ROUTES POUR LES UTILISATEURS AUTHENTIFIÉS --------------------
Route::middleware(['auth'])->group(function () {

    // Home (version authentifiée)
    Route::get('/dashboard-home', [HomeController::class, 'home'])->name('dashboard.home');
    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class);

    // User Posts & Comments Routes
<<<<<<< HEAD
    Route::prefix('user')->group(function () {
        // My Posts
        Route::get('/my-posts', [UserPostController::class, 'myPosts'])->name('user.posts.my');
        Route::post('/posts', [UserPostController::class, 'storePost'])->name('user.posts.store');
        Route::delete('/posts/{post}', [UserPostController::class, 'deletePost'])->name('user.posts.delete');

        // ✅ CORRECTION - UNE SEULE ROUTE UPDATE
        Route::put('/posts/{post}', [UserPostController::class, 'update'])->name('user.posts.update');
=======
Route::prefix('user')->group(function () {
    // My Posts
    Route::get('/my-posts', [UserPostController::class, 'myPosts'])->name('user.posts.my');
    Route::post('/posts', [UserPostController::class, 'storePost'])->name('user.posts.store');
    Route::delete('/posts/{post}', [UserPostController::class, 'deletePost'])->name('user.posts.delete');
    
    // ✅ CORRECTION - UNE SEULE ROUTE UPDATE
    Route::put('/posts/{post}', [UserPostController::class, 'update'])->name('user.posts.update');
>>>>>>> 542202f4aa11f6ef658af99c6362a14a0e23898e


        // Comments
        Route::post('/posts/{postId}/comments', [UserPostController::class, 'storeComment'])->name('user.comments.store');
        Route::delete('/comments/{comment}', [UserPostController::class, 'deleteComment'])->name('user.comments.delete');

        // Community Posts (view all posts)
        Route::get('/community-posts', [UserPostController::class, 'communityPosts'])->name('user.posts.community');
    });

    // Pages statiques
    Route::view('billing', 'billing')->name('billing');
    Route::view('profile', 'profile')->name('profile');
    Route::view('rtl', 'rtl')->name('rtl');
    Route::view('tables', 'tables')->name('tables');
    Route::view('virtual-reality', 'virtual-reality')->name('virtual-reality');

    // Gestion du profil
    Route::post('/logout', [SessionsController::class, 'destroy'])->name('logout');
    Route::get('/user-profile', [InfoUserController::class, 'create'])->name('user-profile.create');
    Route::post('/user-profile', [InfoUserController::class, 'store'])->name('user-profile.store');
    Route::post('/user-profile/password', [InfoUserController::class, 'updatePassword'])->name('user-password.update');

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
            return redirect()->route('user.dashboard');
        }
    })->name('dashboard');

    // Dashboards par rôle
    Route::get('/dashboard/moderator', [ModeratorController::class, 'dashboard'])
        ->name('dashboard.moderator')
        ->middleware(['auth']);

    // CRUD Livres et Catégories
    Route::get('/books/export', [BookController::class, 'export'])->name('books.export');
    Route::resource('books', BookController::class);
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/validate', [CategoryController::class, 'validateCategory'])->name('categories.validate');
    Route::get('/books/{book}/download', [BookController::class, 'downloadPdf'])->name('books.download');



    // Routes pour les Réservations
    Route::resource('reservations', ReservationController::class);

    // Routes pour les Avis
    Route::resource('reviews', ReviewController::class);

    // Routes Moderator
    Route::prefix('moderator')->middleware(['auth'])->group(function () {
        Route::get('dashboard', [ModeratorController::class, 'dashboard'])
            ->name('moderator.dashboard');

        Route::get('books', [ModeratorController::class, 'books'])
            ->name('moderator.books');

        Route::patch('books/validate/{id}', [ModeratorController::class, 'validateBook'])
            ->name('moderator.books.validate');
    });





    // User Routes - AJOUTEZ ces lignes dans le groupe user existant
    Route::prefix('user')->middleware(['auth'])->group(function () {
        // ... vos routes existantes ...

        // AJOUTEZ CES LIGNES :
        Route::post('password', [UserController::class, 'updatePassword'])->name('user.password.update');
    });

    // Moderator Routes - AJOUTEZ ces lignes dans le groupe moderator existant  
    Route::prefix('moderator')->middleware(['auth'])->group(function () {
        // ... vos routes existantes ...

        // AJOUTEZ CES LIGNES :
        Route::get('profile', [ModeratorController::class, 'profile'])->name('moderator.profile');
        Route::post('profile', [ModeratorController::class, 'updateProfile'])->name('moderator.profile.update');
        Route::post('password', [ModeratorController::class, 'updatePassword'])->name('moderator.password.update');
    });

    // Club Manager Routes - AJOUTEZ ces lignes dans le groupe club-manager existant
    Route::prefix('club-manager')->middleware(['auth'])->group(function () {
        // ... vos routes existantes ...

        // AJOUTEZ CES LIGNES :
        Route::get('profile', [ClubManagerController::class, 'profile'])->name('club_manager.profile');
        Route::post('profile', [ClubManagerController::class, 'updateProfile'])->name('club_manager.profile.update');
        Route::post('password', [ClubManagerController::class, 'updatePassword'])->name('club_manager.password.update');
    });



    // Dans le groupe user
    Route::get('recent-activity', [UserController::class, 'getRecentActivity'])
        ->name('user.recent-activity');

    // Club Manager Routes - TOUTES LES ROUTES DANS UN SEUL GROUPE
    Route::prefix('club-manager')->middleware(['auth'])->group(function () {
        Route::get('dashboard', [ClubManagerController::class, 'dashboard'])
            ->name('club_manager.dashboard');

        // Clubs CRUD
        Route::get('clubs', [ClubManagerController::class, 'indexClub'])
            ->name('club_manager.clubs.index');
        Route::post('clubs', [ClubManagerController::class, 'storeClub'])
            ->name('club_manager.clubs.store');
        Route::get('clubs/{id}/edit', [ClubManagerController::class, 'editClub'])
            ->name('club_manager.clubs.edit');
        Route::put('clubs/{id}', [ClubManagerController::class, 'updateClub'])
            ->name('club_manager.clubs.update');
        Route::delete('clubs/{id}', [ClubManagerController::class, 'destroyClub'])
            ->name('club_manager.clubs.destroy');

        // Events CRUD
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

        // Événements d'un club spécifique
        Route::get('clubs/{clubId}/events', [ClubManagerController::class, 'showClubEvents'])
            ->name('club_manager.clubs.events');

        // Notifications Routes
        Route::get('notifications', [NotificationController::class, 'index'])
            ->name('club_manager.notifications.index');
        Route::post('notifications/{id}/accept', [NotificationController::class, 'accept'])
            ->name('club_manager.notifications.accept');
        Route::post('notifications/{id}/reject', [NotificationController::class, 'reject'])
            ->name('club_manager.notifications.reject');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])
            ->name('club_manager.notifications.destroy');
    });

    // User Routes - TOUTES LES ROUTES DANS UN SEUL GROUPE
    Route::prefix('user')->middleware(['auth'])->group(function () {
        Route::get('dashboard', [UserController::class, 'dashboard'])
            ->name('user.dashboard');

        Route::get('books', [UserController::class, 'books'])
            ->name('user.books');

        // CORRECTION : Cette route est maintenant uniquement dans le groupe user
        Route::get('clubs', [UserController::class, 'clubs'])
            ->name('user.clubs');

        Route::post('clubs/{clubId}/join', [UserController::class, 'joinClub'])
            ->name('user.clubs.join');

        Route::get('profile', [UserController::class, 'profile'])
            ->name('user.profile');

        Route::post('profile', [UserController::class, 'updateProfile'])
            ->name('user.profile.update');

        // My Posts Page
        Route::get('my-posts', [UserPostController::class, 'myPosts'])
            ->name('user.posts.my');

        // Notifications user
        Route::get('notifications', [UserController::class, 'notifications'])
            ->name('user.notifications');
        Route::post('notifications/{id}/read', [UserController::class, 'markNotificationAsRead'])
            ->name('user.notifications.read');
    });

    // User Routes - TOUTES LES ROUTES DANS UN SEUL GROUPE
    Route::prefix('user')->middleware(['auth'])->group(function () {
        // ... vos routes existantes

        // Notifications user
        Route::get('notifications', [UserController::class, 'notifications'])
            ->name('user.notifications');
        Route::post('notifications/{id}/read', [UserController::class, 'markNotificationAsRead'])
            ->name('user.notifications.read');
    });



<<<<<<< HEAD
    // Gestion des utilisateurs - CORRIGÉ
    Route::get('/user-management', [InfoUserController::class, 'userManagement'])->name('user-management');
    Route::get('/users/create', [InfoUserController::class, 'createUser'])->name('users.create');
    Route::post('/users', [InfoUserController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [InfoUserController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [InfoUserController::class, 'destroyUser'])->name('users.destroy');

    // PAS de route users.edit ici !
=======
// Gestion des utilisateurs - CORRIGÉ
Route::get('/user-management', [InfoUserController::class, 'userManagement'])->name('user-management');
Route::get('/users/create', [InfoUserController::class, 'createUser'])->name('users.create');
Route::post('/users', [InfoUserController::class, 'storeUser'])->name('users.store');
Route::put('/users/{user}', [InfoUserController::class, 'updateUser'])->name('users.update');
Route::delete('/users/{user}', [InfoUserController::class, 'destroyUser'])->name('users.destroy');

// PAS de route users.edit ici !
>>>>>>> 542202f4aa11f6ef658af99c6362a14a0e23898e



    // AJOUTEZ CES ROUTES APRÈS LES ROUTES EXISTANTES DANS LE GROUPE AUTH
<<<<<<< HEAD
    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class);



    // Routes supplémentaires pour l'admin
    Route::get('/admin/posts', [PostController::class, 'index'])->name('admin.posts');
    Route::get('/admin/comments', [CommentController::class, 'index'])->name('admin.comments');
    // AJOUTEZ CETTE ROUTE APRÈS LES ROUTES POSTS EXISTANTES
    Route::get('/posts/{post}/comments', [PostController::class, 'getPostComments'])->name('posts.comments');
=======
Route::resource('posts', PostController::class);
Route::resource('comments', CommentController::class);

// Routes supplémentaires pour l'admin
Route::get('/admin/posts', [PostController::class, 'index'])->name('admin.posts');
Route::get('/admin/comments', [CommentController::class, 'index'])->name('admin.comments');
// AJOUTEZ CETTE ROUTE APRÈS LES ROUTES POSTS EXISTANTES
Route::get('/posts/{post}/comments', [PostController::class, 'getPostComments'])->name('posts.comments');
>>>>>>> 542202f4aa11f6ef658af99c6362a14a0e23898e
});
