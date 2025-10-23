<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts - BookHive</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .post-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        .post-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .comment-box {
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .edit-form {
            display: none;
        }
        .edit-form.active {
            display: block;
        }
        .post-content {
            display: block;
        }
        .post-content.hidden {
            display: none;
        }
        
        .post-image-container {
            position: relative;
            margin: 1rem 0;
            border-radius: 12px;
            overflow: hidden;
            background: #f8fafc;
        }
        .post-image {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            display: block;
        }
        .image-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .post-image-container:hover .image-actions {
            opacity: 1;
        }
        
        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: #f9fafb;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }
        
        .image-preview {
            max-width: 300px;
            border-radius: 8px;
            margin: 1rem 0;
        }

        /* New styles for comment editing */
        .comment-edit-form {
            display: none;
        }
        .comment-edit-form.active {
            display: block;
        }
        .comment-content {
            display: block;
        }
        .comment-content.hidden {
            display: none;
        }

        /* Validation styles */
        .validation-valid {
            border-color: #10b981 !important;
        }
        .validation-invalid {
            border-color: #ef4444 !important;
        }
        .char-count-warning {
            color: #f59e0b;
            font-weight: 600;
        }
        .char-count-danger {
            color: #ef4444;
            font-weight: 600;
        }
        .char-count-normal {
            color: #6b7280;
        }
        /* Add this to your existing styles */
.hidden {
    display: none !important;
}

#showCreateFormBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    
    @include('layouts.navbars.main-navbar')
    
    <main class="container mx-auto px-4 py-8 mt-16 max-w-4xl">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                    <p class="text-gray-600">Manage your posts and interactions</p>
                </div>
            </div>
        </div>

<!-- Create Post Button -->
<div class="flex justify-end mb-6">
    <button type="button" 
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-300 font-medium flex items-center gap-2 shadow-lg hover:shadow-xl"
            id="showCreateFormBtn">
        <i class="fas fa-plus-circle"></i>
        <span>Create New Post</span>
    </button>
</div>

<!-- Create Post Card (Hidden by default) -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6 hidden" id="createPostCard">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Create New Post</h2>
        <button type="button" 
                class="text-gray-400 hover:text-gray-600 transition-colors"
                id="hideCreateFormBtn">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <form action="{{ route('user.posts.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
        @csrf
        
        <div class="mb-4">
            <input type="text" name="titre" required 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                   placeholder="What's on your mind?" 
                   maxlength="255"
                   minlength="3"
                   id="titleInput">
            <div class="mt-1 text-sm text-gray-600 flex justify-between">
                <span>
                    <span id="titleCount" class="char-count-normal">0</span>/255 caractères
                </span>
                <span>Minimum: 3 caractères</span>
            </div>
            <div class="text-red-500 text-sm mt-1" id="titleError" style="display: none;">
                Le titre doit contenir entre 3 et 255 caractères
            </div>
        </div>
        
        <div class="mb-4">
            <textarea name="contenu" required rows="4"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                      placeholder="Share your thoughts..."
                      minlength="10"
                      id="contentTextarea"></textarea>
            <div class="mt-1 text-sm text-gray-600 flex justify-between">
                <span>
                    <span id="contentCount" class="char-count-normal">0</span> caractères
                </span>
                <span>Minimum: 10 caractères</span>
            </div>
            <div class="text-red-500 text-sm mt-1" id="contentError" style="display: none;">
                Le contenu doit contenir au moins 10 caractères
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-3">Add Image (Optional)</label>
            
            <div class="upload-area" id="uploadArea">
                <div class="flex flex-col items-center justify-center space-y-3">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-700">Drag & drop your image here</p>
                        <p class="text-xs text-gray-500 mt-1">or click to browse</p>
                    </div>
                    <input type="file" name="image" id="imageInput" 
                           accept="image/jpeg,image/png,image/jpg,image/gif"
                           class="hidden">
                    <button type="button" id="chooseFileBtn" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                        <i class="fas fa-folder-open mr-2"></i>Choose File
                    </button>
                </div>
            </div>
            
            <div id="imagePreview" class="hidden mt-4">
                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <img id="previewImage" src="" class="w-12 h-12 object-cover rounded">
                        <div>
                            <p id="fileName" class="text-sm font-medium text-gray-700"></p>
                            <p id="fileSize" class="text-xs text-gray-500"></p>
                        </div>
                    </div>
                    <button type="button" id="removeImageBtn" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <p class="text-xs text-gray-500 mt-2">Formats: JPEG, PNG, JPG, GIF | Max: 2MB</p>
        </div>
        
        <div class="flex justify-end gap-3">
            <button type="button" 
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors font-medium"
                    id="cancelCreateBtn">
                Cancel
            </button>
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2"
                    id="submitBtn">
                <i class="fas fa-paper-plane"></i>
                Post
            </button>
        </div>
    </form>
</div>

        <!-- My Posts Feed -->
        <div class="space-y-6">
            @forelse($posts as $post)
            <div class="bg-white rounded-lg shadow-sm post-card overflow-hidden">
                <!-- Post Header -->
                <div class="p-6 pb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $post->user->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @if($post->user_id === Auth::id())
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    class="text-gray-400 hover:text-blue-600 transition-colors edit-post-btn"
                                    data-post-id="{{ $post->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('user.posts.delete', $post) }}" method="POST" 
                                  onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Post Content -->
                <div class="post-content px-6 pb-4" id="post-content-{{ $post->id }}">
                    <h4 class="text-xl font-semibold text-gray-900 mb-3">{{ $post->titre }}</h4>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line mb-4">{{ $post->contenu }}</p>
                    
                    @if($post->image)
                    <div class="post-image-container">
                        <img src="{{ asset('storage/' . $post->image) }}" 
                             alt="Post image" 
                             class="post-image">
                        <div class="image-actions">
                            <button type="button" 
                                    class="bg-white bg-opacity-90 p-2 rounded-full shadow-sm hover:bg-opacity-100 transition-all image-expand-btn"
                                    data-image-src="{{ asset('storage/' . $post->image) }}">
                                <i class="fas fa-expand text-gray-700"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Edit Form -->
                <div class="edit-form px-6 pb-4" id="edit-form-{{ $post->id }}">
                    <form action="{{ route('user.posts.update', $post) }}" method="POST" class="space-y-4" enctype="multipart/form-data" id="editForm-{{ $post->id }}">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <input type="text" name="titre" value="{{ $post->titre }}" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-medium edit-title-input"
                                   placeholder="Post title" 
                                   maxlength="255"
                                   minlength="3"
                                   data-post-id="{{ $post->id }}">
                            <div class="mt-1 text-sm text-gray-600 flex justify-between">
                                <span>
                                    <span class="edit-title-count" data-post-id="{{ $post->id }}">{{ strlen($post->titre) }}</span>/255 caractères
                                </span>
                                <span>Minimum: 3 caractères</span>
                            </div>
                        </div>
                        
                        <div>
                            <textarea name="contenu" required rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none edit-content-textarea"
                                      placeholder="Post content"
                                      minlength="10"
                                      data-post-id="{{ $post->id }}">{{ $post->contenu }}</textarea>
                            <div class="mt-1 text-sm text-gray-600 flex justify-between">
                                <span>
                                    <span class="edit-content-count" data-post-id="{{ $post->id }}">{{ strlen($post->contenu) }}</span> caractères
                                </span>
                                <span>Minimum: 10 caractères</span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Image</label>
                            
                            @if($post->image)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-2">Current Image:</p>
                                <div class="flex items-center space-x-4">
                                    <img src="{{ asset('storage/' . $post->image) }}" 
                                         class="image-preview">
                                    <div class="flex-1">
                                        <label class="flex items-center text-sm text-red-600 cursor-pointer">
                                            <input type="checkbox" name="remove_image" value="1" class="mr-2">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="upload-area edit-upload-area" data-post-id="{{ $post->id }}">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
                                    <p class="text-sm text-gray-600">Click to change image</p>
                                    <input type="file" name="image" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif"
                                           class="hidden edit-image-input"
                                           id="editImageInput{{ $post->id }}">
                                    <button type="button" 
                                            class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700 transition-colors change-image-btn"
                                            data-post-id="{{ $post->id }}">
                                        Change Image
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" 
                                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors font-medium cancel-edit-btn"
                                    data-post-id="{{ $post->id }}">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center gap-2 edit-submit-btn"
                                    data-post-id="{{ $post->id }}">
                                <i class="fas fa-save"></i>
                                Update
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Composant Réactions -->
                @include('components.reactions', ['post' => $post])

                <!-- Comment Form -->
                <div class="comment-box p-4 comment-section" id="comment-section-{{ $post->id }}" style="display: none;">
                    <form action="{{ route('user.comments.store', $post->id) }}" method="POST" class="flex gap-3">
                        @csrf
                        <div class="flex-1">
                            <input type="text" name="contenu" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Write a comment...">
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

                <!-- Recent Comments - VISIBLE PAR DÉFAUT (2-3 derniers commentaires) -->
                @if($post->comments->count() > 0)
                <div class="comment-box p-4 pt-2 space-y-3" id="recent-comments-{{ $post->id }}">
                    @php
                        // Prendre les 2 derniers commentaires
                        $recentComments = $post->comments->sortByDesc('created_at')->take(2);
                    @endphp
                    
                    @foreach($recentComments as $comment)
                    <div class="flex gap-3 group">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <!-- Comment Content -->
                            <div class="comment-content" id="comment-content-{{ $comment->id }}">
                                <div class="bg-gray-100 rounded-2xl px-4 py-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-900 text-sm">
                                            {{ $comment->user->name }}
                                        </span>
                                        @if($comment->user_id === Auth::id())
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                                            You
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ $comment->contenu }}</p>
                                </div>
                                <div class="flex items-center gap-4 mt-1 px-1">
                                    <span class="text-xs text-gray-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    @if($comment->user_id === Auth::id())
                                    <button type="button" 
                                            class="text-xs text-blue-600 hover:text-blue-800 transition-colors edit-comment-btn"
                                            data-comment-id="{{ $comment->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('user.comments.delete', $comment) }}" method="POST"
                                          onsubmit="return confirm('Delete this comment?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Comment Edit Form -->
                            <div class="comment-edit-form" id="comment-edit-form-{{ $comment->id }}">
                                <form action="{{ route('user.comments.update', $comment) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex gap-2">
                                        <input type="text" name="contenu" value="{{ $comment->contenu }}" required 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                               placeholder="Edit your comment...">
                                        <div class="flex gap-1">
                                            <button type="submit" 
                                                    class="bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" 
                                                    class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 transition-colors text-sm cancel-comment-edit-btn"
                                                    data-comment-id="{{ $comment->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- All Comments - CACHÉE PAR DÉFAUT -->
                @if($post->comments->count() > 2)
                <div class="comment-box p-4 pt-2 space-y-3" style="display: none;" id="all-comments-{{ $post->id }}">
                    @foreach($post->comments->sortByDesc('created_at') as $comment)
                    <div class="flex gap-3 group">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <!-- Comment Content -->
                            <div class="comment-content" id="comment-content-{{ $comment->id }}">
                                <div class="bg-gray-100 rounded-2xl px-4 py-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-900 text-sm">
                                            {{ $comment->user->name }}
                                        </span>
                                        @if($comment->user_id === Auth::id())
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                                            You
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ $comment->contenu }}</p>
                                </div>
                                <div class="flex items-center gap-4 mt-1 px-1">
                                    <span class="text-xs text-gray-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    @if($comment->user_id === Auth::id())
                                    <button type="button" 
                                            class="text-xs text-blue-600 hover:text-blue-800 transition-colors edit-comment-btn"
                                            data-comment-id="{{ $comment->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('user.comments.delete', $comment) }}" method="POST"
                                          onsubmit="return confirm('Delete this comment?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Comment Edit Form -->
                            <div class="comment-edit-form" id="comment-edit-form-{{ $comment->id }}">
                                <form action="{{ route('user.comments.update', $comment) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex gap-2">
                                        <input type="text" name="contenu" value="{{ $comment->contenu }}" required 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                               placeholder="Edit your comment...">
                                        <div class="flex gap-1">
                                            <button type="submit" 
                                                    class="bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" 
                                                    class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 transition-colors text-sm cancel-comment-edit-btn"
                                                    data-comment-id="{{ $comment->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- View All Comments Link - SEULEMENT SI IL Y A PLUS DE 2 COMMENTAIRES -->
                @if($post->comments->count() > 2)
                <div class="comment-box p-4 pt-2 border-t border-gray-200">
                    <button class="text-sm text-blue-600 hover:text-blue-800 font-medium view-all-comments-btn" 
                            data-post-id="{{ $post->id }}"
                            data-total-comments="{{ $post->comments->count() }}"
                            onclick="toggleAllComments('{{ $post->id }}')">
                        View all {{ $post->comments->count() }} comments
                    </button>
                </div>
                @endif

            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <i class="fas fa-newspaper text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Posts Yet</h3>
                <p class="text-gray-500">Create your first post to start the conversation!</p>
            </div>
            @endforelse
        </div>
    <!-- Modals des réactions - PLACÉS EN DEHORS DES POSTS -->
    @foreach($posts as $post)
    <div id="reactionsModal-{{ $post->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-2xl max-h-[80vh] overflow-hidden flex flex-col">
            <!-- Header du modal -->
            <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-white sticky top-0 z-10">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Reactions</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $post->total_reactions }} total reactions</p>
                </div>
                <button class="close-modal text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors" 
                        onclick="closeReactionsModal('{{ $post->id }}')">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Onglets des réactions -->
            <div class="border-b border-gray-200 bg-gray-50">
                <div class="flex overflow-x-auto" id="reactionTabs-{{ $post->id }}">
                    <!-- Les onglets seront générés dynamiquement -->
                </div>
            </div>
            
            <!-- Liste des réactions -->
            <div class="flex-1 overflow-y-auto p-6" id="reactionsList-{{ $post->id }}">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-3"></i>
                    <p class="text-gray-500">Loading reactions...</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    </main>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button id="closeModalBtn" class="absolute -top-12 right-0 text-white text-2xl hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
            <img id="modalImage" src="" class="max-w-full max-h-screen object-contain">
        </div>
    </div>

    <script>
        // Fonctions globales
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeImage() {
            document.getElementById('imagePreview').classList.add('hidden');
            document.getElementById('uploadArea').classList.remove('hidden');
            document.getElementById('imageInput').value = '';
        }

function toggleAllComments(postId) {
    const recentComments = document.getElementById('recent-comments-' + postId);
    const allComments = document.getElementById('all-comments-' + postId);
    const viewAllBtn = document.querySelector('.view-all-comments-btn[data-post-id="' + postId + '"]');
    
    if (allComments && recentComments && viewAllBtn) {
        const isShowingAll = allComments.style.display === 'block';
        
        if (isShowingAll) {
            allComments.style.display = 'none';
            recentComments.style.display = 'block';
            viewAllBtn.textContent = 'View all ' + viewAllBtn.getAttribute('data-total-comments') + ' comments';
        } else {
            allComments.style.display = 'block';
            recentComments.style.display = 'none';
            viewAllBtn.textContent = 'Show less comments';
        }
    }
}

        // Comment editing functions
        function editComment(commentId) {
            const commentContent = document.getElementById('comment-content-' + commentId);
            const commentEditForm = document.getElementById('comment-edit-form-' + commentId);
            
            commentContent.classList.add('hidden');
            commentEditForm.classList.add('active');
        }

        function cancelCommentEdit(commentId) {
            const commentContent = document.getElementById('comment-content-' + commentId);
            const commentEditForm = document.getElementById('comment-edit-form-' + commentId);
            
            commentContent.classList.remove('hidden');
            commentEditForm.classList.remove('active');
        }

        // Validation functions
        function updateCharCount(element, countElement, minLength, maxLength = null) {
            const length = element.value.length;
            countElement.textContent = length;
            
            if (maxLength && length > maxLength * 0.8) {
                countElement.className = 'char-count-warning';
            } else if (length < minLength) {
                countElement.className = 'char-count-danger';
            } else {
                countElement.className = 'char-count-normal';
            }
            
            return length;
        }

        function validateField(element, minLength, maxLength = null) {
            const length = element.value.length;
            let isValid = length >= minLength;
            
            if (maxLength !== null) {
                isValid = isValid && length <= maxLength;
            }
            
            if (element.value && !isValid) {
                element.classList.add('validation-invalid');
                element.classList.remove('validation-valid');
                return false;
            } else {
                element.classList.remove('validation-invalid');
                if (element.value && isValid) {
                    element.classList.add('validation-valid');
                }
                return isValid;
            }
        }

        function validateCreateForm() {
            const isTitleValid = validateField(titleInput, 3, 255);
            const isContentValid = validateField(contentTextarea, 10);
            const isFormValid = isTitleValid && isContentValid;
            
            submitBtn.disabled = !isFormValid;
            
            if (isFormValid) {
                submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else {
                submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
            
            return isFormValid;
        }

        function validateEditForm(postId) {
            const titleInput = document.querySelector(`.edit-title-input[data-post-id="${postId}"]`);
            const contentTextarea = document.querySelector(`.edit-content-textarea[data-post-id="${postId}"]`);
            const submitBtn = document.querySelector(`.edit-submit-btn[data-post-id="${postId}"]`);
            
            if (!titleInput || !contentTextarea || !submitBtn) return true;
            
            const isTitleValid = validateField(titleInput, 3, 255);
            const isContentValid = validateField(contentTextarea, 10);
            const isFormValid = isTitleValid && isContentValid;
            
            submitBtn.disabled = !isFormValid;
            
            if (isFormValid) {
                submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
            
            return isFormValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Elements for create form validation
            const titleInput = document.getElementById('titleInput');
            const contentTextarea = document.getElementById('contentTextarea');
            const titleCount = document.getElementById('titleCount');
            const contentCount = document.getElementById('contentCount');
            const titleError = document.getElementById('titleError');
            const contentError = document.getElementById('contentError');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('createPostForm');
            // Show/hide create post form
const showCreateFormBtn = document.getElementById('showCreateFormBtn');
const hideCreateFormBtn = document.getElementById('hideCreateFormBtn');
const cancelCreateBtn = document.getElementById('cancelCreateBtn');
const createPostCard = document.getElementById('createPostCard');

if (showCreateFormBtn && createPostCard) {
    showCreateFormBtn.addEventListener('click', function() {
        createPostCard.classList.remove('hidden');
        showCreateFormBtn.parentElement.classList.add('hidden');
        // Focus on title input when form is shown
        setTimeout(() => {
            if (titleInput) titleInput.focus();
        }, 100);
    });
}

if (hideCreateFormBtn) {
    hideCreateFormBtn.addEventListener('click', hideCreateForm);
}

if (cancelCreateBtn) {
    cancelCreateBtn.addEventListener('click', hideCreateForm);
}

function hideCreateForm() {
    if (createPostCard) {
        createPostCard.classList.add('hidden');
        showCreateFormBtn.parentElement.classList.remove('hidden');
        // Reset form when hiding
        if (form) {
            form.reset();
            // Reset validation states
            if (titleInput) {
                titleInput.classList.remove('validation-valid', 'validation-invalid');
                updateCharCount(titleInput, titleCount, 3, 255);
            }
            if (contentTextarea) {
                contentTextarea.classList.remove('validation-valid', 'validation-invalid');
                updateCharCount(contentTextarea, contentCount, 10);
            }
            validateCreateForm();
            // Reset image preview
            removeImage();
        }
    }
}

// Also hide form after successful submission
const originalFormSubmit = form ? form.onsubmit : null;
if (form) {
    form.onsubmit = function(e) {
        if (originalFormSubmit) originalFormSubmit.call(this, e);
        // If form is valid and submitted, hide the form after a delay
        if (validateCreateForm()) {
            setTimeout(hideCreateForm, 1000);
        }
    };
}

            // Initialize validation
            if (titleInput && contentTextarea) {
                updateCharCount(titleInput, titleCount, 3, 255);
                updateCharCount(contentTextarea, contentCount, 10);
                validateCreateForm();
            }

            // Event listeners for create form
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    updateCharCount(this, titleCount, 3, 255);
                    validateCreateForm();
                });
            }

            if (contentTextarea) {
                contentTextarea.addEventListener('input', function() {
                    updateCharCount(this, contentCount, 10);
                    validateCreateForm();
                });
            }

            // Event listeners for edit forms
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('edit-title-input')) {
                    const postId = e.target.getAttribute('data-post-id');
                    const countElement = document.querySelector(`.edit-title-count[data-post-id="${postId}"]`);
                    if (countElement) {
                        updateCharCount(e.target, countElement, 3, 255);
                    }
                    validateEditForm(postId);
                }
                
                if (e.target.classList.contains('edit-content-textarea')) {
                    const postId = e.target.getAttribute('data-post-id');
                    const countElement = document.querySelector(`.edit-content-count[data-post-id="${postId}"]`);
                    if (countElement) {
                        updateCharCount(e.target, countElement, 10);
                    }
                    validateEditForm(postId);
                }
            });

            // Prevent invalid form submission
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateCreateForm()) {
                        e.preventDefault();
                        showNotification('Please correct the errors in the form before submitting.', 'error');
                    }
                });
            }

            // Prevent invalid edit form submission
            document.addEventListener('submit', function(e) {
                if (e.target.id && e.target.id.startsWith('editForm-')) {
                    const postId = e.target.id.replace('editForm-', '');
                    if (!validateEditForm(postId)) {
                        e.preventDefault();
                        showNotification('Please correct the errors in the form before submitting.', 'error');
                    }
                }
            });

            // Gestion de l'upload d'image
            const uploadArea = document.getElementById('uploadArea');
            const imageInput = document.getElementById('imageInput');
            const chooseFileBtn = document.getElementById('chooseFileBtn');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeImageBtn = document.getElementById('removeImageBtn');

            // Événements pour l'upload
            if (uploadArea) {
                uploadArea.addEventListener('click', function() {
                    imageInput.click();
                });
            }

            if (chooseFileBtn) {
                chooseFileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    imageInput.click();
                });
            }

            if (imageInput) {
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
                                imagePreview.classList.remove('hidden');
                                uploadArea.classList.add('hidden');
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                });
            }

            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', removeImage);
            }

            // Gestion de l'édition des posts
            const editButtons = document.querySelectorAll('.edit-post-btn');
            const cancelButtons = document.querySelectorAll('.cancel-edit-btn');
            const changeImageBtns = document.querySelectorAll('.change-image-btn');
            const editUploadAreas = document.querySelectorAll('.edit-upload-area');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    const postContent = document.getElementById('post-content-' + postId);
                    const editForm = document.getElementById('edit-form-' + postId);

                    postContent.classList.add('hidden');
                    editForm.classList.add('active');
                    
                    // Initialize validation for edit form
                    setTimeout(() => validateEditForm(postId), 100);
                });
            });

            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    const postContent = document.getElementById('post-content-' + postId);
                    const editForm = document.getElementById('edit-form-' + postId);

                    postContent.classList.remove('hidden');
                    editForm.classList.remove('active');
                });
            });

            changeImageBtns.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const postId = this.getAttribute('data-post-id');
                    document.getElementById('editImageInput' + postId).click();
                });
            });

            editUploadAreas.forEach(area => {
                area.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    document.getElementById('editImageInput' + postId).click();
                });
            });

            // Gestion du modal d'image
            const imageExpandBtns = document.querySelectorAll('.image-expand-btn');
            const closeModalBtn = document.getElementById('closeModalBtn');

            imageExpandBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const imageSrc = this.getAttribute('data-image-src');
                    openImageModal(imageSrc);
                });
            });

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', closeImageModal);
            }

            // Fermer modal avec ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeImageModal();
                }
            });

            // Comment editing event listeners
            const editCommentBtns = document.querySelectorAll('.edit-comment-btn');
            const cancelCommentEditBtns = document.querySelectorAll('.cancel-comment-edit-btn');

            editCommentBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.getAttribute('data-comment-id');
                    editComment(commentId);
                });
            });

            cancelCommentEditBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.getAttribute('data-comment-id');
                    cancelCommentEdit(commentId);
                });
            });

            // Animation des posts
            const postCards = document.querySelectorAll('.post-card');
            postCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        function showNotification(message, type = 'info') {
            // Créer une notification simple
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }
    </script>
</body>
</html>