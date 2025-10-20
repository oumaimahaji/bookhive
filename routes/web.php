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
use App\Http\Controllers\AdminClubEventController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\ReactionController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// -------------------- ROUTES PUBLIQUES (ACCESSIBLES À TOUS) --------------------
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');
Route::get('/book/{book}', [FrontendController::class, 'showBook'])->name('frontend.book');

// -------------------- ROUTES POUR LES INVITÉS --------------------
Route::middleware(['guest'])->group(function () {
    // Authentification - CORRIGÉ : nom de route changé de 'register.create' à 'register'
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store'])->name('login.store');

    // Mot de passe oublié / réinitialisation
    Route::get('/login/forgot-password', [ResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ResetController::class, 'sendEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

    // Login social
    Route::get('/auth/google/redirect', [SocialAuthController::class, 'googleRedirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback'])->name('google.callback');
    Route::get('/auth/facebook/redirect', [SocialAuthController::class, 'facebookRedirect'])->name('facebook.redirect');
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'facebookCallback'])->name('facebook.callback');
});

// -------------------- ROUTES POUR LES UTILISATEURS AUTHENTIFIÉS --------------------
Route::middleware(['auth'])->group(function () {
    // Home (version authentifiée)
    Route::get('/dashboard-home', [HomeController::class, 'home'])->name('dashboard.home');

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

    // -------------------- ROUTES COMMUNES --------------------
    // Posts et Commentaires
    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class);
    Route::get('/posts/{post}/comments', [PostController::class, 'getPostComments'])->name('posts.comments');

    // Livres et Catégories
    Route::get('/books/export', [BookController::class, 'export'])->name('books.export');
    Route::resource('books', BookController::class);
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/validate', [CategoryController::class, 'validateCategory'])->name('categories.validate');

    Route::get('/books/{book}/download', [BookController::class, 'download'])->name('books.download');
    Route::get('/categories/{category}/pdf', [CategoryController::class, 'pdf'])->name('categories.pdf');
    // Réservations
    Route::resource('reservations', ReservationController::class);
    Route::put('reservations/{reservation}/mark-returned', [ReservationController::class, 'markReturned'])->name('reservations.markReturned');

    // Avis
    Route::resource('reviews', ReviewController::class);

    // Gestion des utilisateurs (Admin)
    Route::get('/user-management', [InfoUserController::class, 'userManagement'])->name('user-management');
    Route::get('/users/create', [InfoUserController::class, 'createUser'])->name('users.create');
    Route::post('/users', [InfoUserController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [InfoUserController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [InfoUserController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [InfoUserController::class, 'destroyUser'])->name('users.destroy');

    // Routes supplémentaires pour l'admin
    Route::get('/admin/posts', [PostController::class, 'index'])->name('admin.posts');
    Route::get('/admin/comments', [CommentController::class, 'index'])->name('admin.comments');


    // =============================================
// ROUTES UNIFIÉES POUR LA GESTION ADMIN DES RÉACTIONS (NOUVELLES URL)
// =============================================

// Page principale de gestion des réactions (NOUVELLE URL SANS CONFLIT)
Route::get('/admin/reactions-management', [PostController::class, 'reactionsIndex'])->name('admin.posts.reactions.index');

// Suppression des réactions d'un post (NOUVELLE URL SANS CONFLIT)
Route::delete('/admin/reactions-management/{post}', [PostController::class, 'deletePostReactions'])->name('admin.posts.reactions.delete');

// =============================================
// ROUTES API POUR LES RÉACTIONS (frontend - NE PAS TOUCHER)
// =============================================
Route::post('/posts/{post}/react', [ReactionController::class, 'react'])->name('posts.react');
Route::get('/posts/{post}/reactions', [ReactionController::class, 'getReactions'])->name('posts.reactions');
// Suppression individuelle d'une réaction
Route::delete('/admin/reactions-management/{post}/reaction/{reaction}', [PostController::class, 'deleteSingleReaction'])->name('admin.posts.reactions.delete-single');




    // -------------------- ROUTES UTILISATEUR STANDARD --------------------
    Route::prefix('user')->group(function () {
        // Dashboard
        Route::get('dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

        // Livres
        Route::get('books', [UserController::class, 'books'])->name('user.books');

        // Clubs
        Route::get('clubs', [UserController::class, 'clubs'])->name('user.clubs');
        Route::post('clubs/{clubId}/join', [UserController::class, 'joinClub'])->name('user.clubs.join');

        // Profil
        Route::get('profile', [UserController::class, 'profile'])->name('user.profile');
        Route::post('profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
        Route::post('password', [UserController::class, 'updatePassword'])->name('user.password.update');

        // Posts utilisateur
        Route::get('my-posts', [UserPostController::class, 'myPosts'])->name('user.posts.my');
        Route::post('posts', [UserPostController::class, 'storePost'])->name('user.posts.store');
        Route::delete('posts/{post}', [UserPostController::class, 'deletePost'])->name('user.posts.delete');
        Route::put('posts/{post}', [UserPostController::class, 'update'])->name('user.posts.update');
        // Routes pour les réactions
Route::post('/posts/{post}/react', [ReactionController::class, 'react'])->name('posts.react');
Route::get('/posts/{post}/reactions', [ReactionController::class, 'getReactions'])->name('posts.reactions');

        // Commentaires utilisateur
        Route::post('posts/{postId}/comments', [UserPostController::class, 'storeComment'])->name('user.comments.store');
        Route::delete('comments/{comment}', [UserPostController::class, 'deleteComment'])->name('user.comments.delete');

        // Posts communautaires
        Route::get('community-posts', [UserPostController::class, 'communityPosts'])->name('user.posts.community');

        // Activité récente
        Route::get('recent-activity', [UserController::class, 'getRecentActivity'])->name('user.recent-activity');

        // Notifications
        Route::get('notifications', [UserController::class, 'notifications'])->name('user.notifications');
        Route::post('notifications/{id}/read', [UserController::class, 'markNotificationAsRead'])->name('user.notifications.read');
    });
    // -------------------- ROUTES ADMIN POUR LES NOTIFICATIONS --------------------
    Route::prefix('admin')->group(function () {
        // Notifications CRUD
        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
        Route::post('notifications/{id}/accept', [AdminNotificationController::class, 'accept'])->name('admin.notifications.accept');
        Route::post('notifications/{id}/reject', [AdminNotificationController::class, 'reject'])->name('admin.notifications.reject');
        Route::delete('notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    });
    // -------------------- ROUTES MODÉRATEUR --------------------
    Route::prefix('moderator')->group(function () {
        // Dashboard
        Route::get('dashboard', [ModeratorController::class, 'dashboard'])->name('moderator.dashboard');

        // Gestion des livres
        Route::get('books', [ModeratorController::class, 'books'])->name('moderator.books');
        Route::patch('books/validate/{id}', [ModeratorController::class, 'validateBook'])->name('moderator.books.validate');

        // Profil
        Route::get('profile', [ModeratorController::class, 'profile'])->name('moderator.profile');
        Route::post('profile', [ModeratorController::class, 'updateProfile'])->name('moderator.profile.update');
        Route::post('password', [ModeratorController::class, 'updatePassword'])->name('moderator.password.update');
    });


    // -------------------- ROUTES ADMIN POUR CLUBS ET ÉVÉNEMENTS --------------------
    Route::prefix('admin')->group(function () {
        // Clubs CRUD (tout dans index)
        Route::get('clubs', [AdminClubEventController::class, 'indexClub'])->name('admin.clubs.index');
        Route::post('clubs', [AdminClubEventController::class, 'storeClub'])->name('admin.clubs.store');
        Route::put('clubs/{id}', [AdminClubEventController::class, 'updateClub'])->name('admin.clubs.update');
        Route::get('members', [AdminClubEventController::class, 'indexMembers'])->name('admin.members.index');
        Route::delete('members/{memberId}', [AdminClubEventController::class, 'removeMember'])->name('admin.members.remove');
        Route::delete('clubs/{id}', [AdminClubEventController::class, 'destroyClub'])->name('admin.clubs.destroy');

        // Events CRUD (tout dans index)
        Route::get('events', [AdminClubEventController::class, 'indexEvent'])->name('admin.events.index');
        Route::post('events', [AdminClubEventController::class, 'storeEvent'])->name('admin.events.store');
        Route::put('events/{id}', [AdminClubEventController::class, 'updateEvent'])->name('admin.events.update');
        Route::delete('events/{id}', [AdminClubEventController::class, 'destroyEvent'])->name('admin.events.destroy');
    });


    // -------------------- ROUTES CLUB MANAGER --------------------
    Route::prefix('club-manager')->group(function () {
        // Dashboard
        Route::get('dashboard', [ClubManagerController::class, 'dashboard'])->name('club_manager.dashboard');

        // Clubs CRUD
        Route::get('clubs', [ClubManagerController::class, 'indexClub'])->name('club_manager.clubs.index');
        Route::get('clubs/create', [ClubManagerController::class, 'createClub'])->name('club_manager.clubs.create');
        Route::get('members', [ClubManagerController::class, 'indexMembers'])->name('club_manager.members.index');
        Route::delete('members/{memberId}', [ClubManagerController::class, 'removeMember'])->name('club_manager.members.remove');
        Route::post('clubs', [ClubManagerController::class, 'storeClub'])->name('club_manager.clubs.store');
        Route::get('clubs/{id}/edit', [ClubManagerController::class, 'editClub'])->name('club_manager.clubs.edit');
        Route::put('clubs/{id}', [ClubManagerController::class, 'updateClub'])->name('club_manager.clubs.update');
        Route::delete('clubs/{id}', [ClubManagerController::class, 'destroyClub'])->name('club_manager.clubs.destroy');

        // Events CRUD
        Route::get('events', [ClubManagerController::class, 'indexEvent'])->name('club_manager.events.index');
        Route::get('events/create', [ClubManagerController::class, 'createEvent'])->name('club_manager.events.create');
        Route::post('events', [ClubManagerController::class, 'storeEvent'])->name('club_manager.events.store');
        Route::get('events/{id}/edit', [ClubManagerController::class, 'editEvent'])->name('club_manager.events.edit');
        Route::put('events/{id}', [ClubManagerController::class, 'updateEvent'])->name('club_manager.events.update');
        Route::delete('events/{id}', [ClubManagerController::class, 'destroyEvent'])->name('club_manager.events.destroy');

        // Événements d'un club spécifique
        Route::get('clubs/{clubId}/events', [ClubManagerController::class, 'showClubEvents'])->name('club_manager.clubs.events');

        // Profil
        Route::get('profile', [ClubManagerController::class, 'profile'])->name('club_manager.profile');
        Route::post('profile', [ClubManagerController::class, 'updateProfile'])->name('club_manager.profile.update');
        Route::post('password', [ClubManagerController::class, 'updatePassword'])->name('club_manager.password.update');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('club_manager.notifications.index');
        Route::post('notifications/{id}/accept', [NotificationController::class, 'accept'])->name('club_manager.notifications.accept');
        Route::post('notifications/{id}/reject', [NotificationController::class, 'reject'])->name('club_manager.notifications.reject');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('club_manager.notifications.destroy');
    });



    // Routes pour l'API de recherche (dans web.php car vous utilisez le même contrôleur)
    Route::get('/api/books/search', [App\Http\Controllers\FrontendController::class, 'searchBooks']);

    // Dans la section des routes authentifiées
    Route::post('/books/ai/recommendations', [BookController::class, 'getAIRecommendations'])->name('books.ai.recommendations');
});
