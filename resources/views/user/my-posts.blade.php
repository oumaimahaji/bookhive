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

        <!-- Create Post Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900">Create New Post</h2>
            <form action="{{ route('user.posts.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
                @csrf
                
                <div class="mb-4">
                    <input type="text" name="titre" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                           placeholder="What's on your mind?" maxlength="255">
                </div>
                
                <div class="mb-4">
                    <textarea name="contenu" required rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Share your thoughts..."></textarea>
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
                
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
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
                                    {{ \Carbon\Carbon::parse($post->date)->diffForHumans() }}
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
                    <form action="{{ route('user.posts.update', $post) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <input type="text" name="titre" value="{{ $post->titre }}" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                                   placeholder="Post title" maxlength="255">
                        </div>
                        
                        <div>
                            <textarea name="contenu" required rows="4"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Post content">{{ $post->contenu }}</textarea>
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
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                Update
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Post Stats -->
                <div class="px-6 py-3 border-t border-gray-100 text-sm text-gray-500">
                    {{ $post->comments->count() }} {{ Str::plural('comment', $post->comments->count()) }}
                </div>

                <!-- Comment Form -->
                <div class="comment-box p-4">
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

                <!-- Comments List -->
                @if($post->comments->count() > 0)
                <div class="comment-box p-4 pt-2 space-y-3">
                    @foreach($post->comments as $comment)
                    <div class="flex gap-3 group">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
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
                                    {{ \Carbon\Carbon::parse($comment->date)->diffForHumans() }}
                                </span>
                                @if($comment->user_id === Auth::id())
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
                    </div>
                    @endforeach
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

        document.addEventListener('DOMContentLoaded', function() {
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
            uploadArea.addEventListener('click', function() {
                imageInput.click();
            });

            chooseFileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                imageInput.click();
            });

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

            removeImageBtn.addEventListener('click', removeImage);

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

            closeModalBtn.addEventListener('click', closeImageModal);

            // Fermer modal avec ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeImageModal();
                }
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
    </script>
</body>
</html>