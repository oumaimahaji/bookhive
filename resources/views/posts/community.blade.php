<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Posts - BookHive</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .post-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .post-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .comment-box {
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
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

        /* New styles for editing */
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

        .hidden {
            display: none !important;
        }

        /* Upload area styles */
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
    </style>
</head>

<body class="bg-gray-100 min-h-screen">

    @include('layouts.navbars.main-navbar')

    <main class="container mx-auto px-4 py-8 mt-16 max-w-4xl">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Community Posts</h1>
            <p class="text-gray-600">Discover what everyone is sharing in our reading community</p>
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
                <h2 class="text-lg font-semibold text-gray-900">Create a Post</h2>
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
                        placeholder="Share your thoughts with the community..."
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

                <!-- Upload Image avec zone de drag & drop -->
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
                        Post to Community
                    </button>
                </div>
            </form>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text"
                        id="searchInput"
                        placeholder="Search by title, content, or user name..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                </div>
                <div class="flex items-center gap-2">
                    <span id="searchResultsCount" class="text-sm text-gray-600 hidden">
                        <span id="postsCount">0</span> posts found
                    </span>
                    <button id="clearSearchBtn"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors hidden">
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden text-center py-8">
            <div class="inline-flex items-center gap-3 bg-white rounded-lg shadow-sm px-6 py-4">
                <i class="fas fa-spinner fa-spin text-blue-600"></i>
                <span class="text-gray-700">Searching posts...</span>
            </div>
        </div>

        <!-- Community Posts Feed - AVEC IMAGES -->
        @include('posts.partials.posts-list')

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

    <!-- Modals des réactions - MÊME DESIGN QUE MY POSTS -->
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

    <script>
        // Fonctions globales
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function toggleAllComments(postId) {
            const recentComments = document.getElementById('recent-comments-' + postId);
            const allComments = document.getElementById('all-comments-' + postId);
            const viewAllBtn = document.querySelector('.view-all-comments-btn[data-post-id="' + postId + '"]');

            if (allComments.style.display === 'none') {
                recentComments.style.display = 'none';
                allComments.style.display = 'block';
                viewAllBtn.textContent = 'Show less comments';
            } else {
                recentComments.style.display = 'block';
                allComments.style.display = 'none';
                viewAllBtn.textContent = 'View all ' + viewAllBtn.getAttribute('data-total-comments') + ' comments';
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

        // Post editing functions
        function editPost(postId) {
            const postContent = document.getElementById('post-content-' + postId);
            const editForm = document.getElementById('edit-form-' + postId);

            postContent.classList.add('hidden');
            editForm.classList.add('active');

            // Initialize validation for edit form
            setTimeout(() => validateEditForm(postId), 100);
        }

        function cancelPostEdit(postId) {
            const postContent = document.getElementById('post-content-' + postId);
            const editForm = document.getElementById('edit-form-' + postId);

            postContent.classList.remove('hidden');
            editForm.classList.remove('active');
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
            const titleInput = document.getElementById('titleInput');
            const contentTextarea = document.getElementById('contentTextarea');
            const submitBtn = document.getElementById('submitBtn');

            if (!titleInput || !contentTextarea || !submitBtn) return true;

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

        function hideCreateForm() {
            const createPostCard = document.getElementById('createPostCard');
            const showCreateFormBtn = document.getElementById('showCreateFormBtn');
            const form = document.getElementById('createPostForm');

            if (createPostCard) {
                createPostCard.classList.add('hidden');
                if (showCreateFormBtn) {
                    showCreateFormBtn.parentElement.classList.remove('hidden');
                }
                // Reset form when hiding
                if (form) {
                    form.reset();
                    // Reset validation states
                    const titleInput = document.getElementById('titleInput');
                    const contentTextarea = document.getElementById('contentTextarea');
                    const titleCount = document.getElementById('titleCount');
                    const contentCount = document.getElementById('contentCount');

                    if (titleInput && titleCount) {
                        titleInput.classList.remove('validation-valid', 'validation-invalid');
                        updateCharCount(titleInput, titleCount, 3, 255);
                    }
                    if (contentTextarea && contentCount) {
                        contentTextarea.classList.remove('validation-valid', 'validation-invalid');
                        updateCharCount(contentTextarea, contentCount, 10);
                    }
                    validateCreateForm();
                    // Reset image preview
                    removeImage();
                }
            }
        }

        function removeImage() {
            const imagePreview = document.getElementById('imagePreview');
            const uploadArea = document.getElementById('uploadArea');
            const imageInput = document.getElementById('imageInput');

            if (imagePreview) imagePreview.classList.add('hidden');
            if (uploadArea) uploadArea.classList.remove('hidden');
            if (imageInput) imageInput.value = '';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Fonctions pour les modals de réactions (mêmes que dans my-posts)
        function showReactionsModal(postId) {
            const modal = document.getElementById(`reactionsModal-${postId}`);

            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            loadReactionsForModal(postId);
        }

        function closeReactionsModal(postId) {
            const modal = document.getElementById(`reactionsModal-${postId}`);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function loadReactionsForModal(postId) {
            const list = document.getElementById(`reactionsList-${postId}`);
            const tabs = document.getElementById(`reactionTabs-${postId}`);

            if (list) {
                list.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-3"></i><p class="text-gray-500">Loading reactions...</p></div>';
            }

            if (tabs) {
                tabs.innerHTML = '';
            }

            fetch(`/user/posts/${postId}/reactions`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load reactions');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    displayReactionsModal(postId, data.reactions);
                })
                .catch(error => {
                    console.error('Error loading reactions:', error);
                    const list = document.getElementById(`reactionsList-${postId}`);
                    if (list) {
                        list.innerHTML = `<div class="text-center py-8 text-red-500">
                            <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
                            <p class="font-medium">Error loading reactions</p>
                            <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                        </div>`;
                    }
                });
        }

        function displayReactionsModal(postId, reactions) {
            const list = document.getElementById(`reactionsList-${postId}`);
            const tabs = document.getElementById(`reactionTabs-${postId}`);

            if (!reactions || Object.keys(reactions).length === 0) {
                list.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-heart text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500 text-lg">No reactions yet</p>
                        <p class="text-gray-400 text-sm mt-2">Be the first to react to this post!</p>
                    </div>
                `;
                return;
            }

            // Créer les onglets
            const sortedReactions = Object.entries(reactions).sort(([, a], [, b]) => b.count - a.count);
            let tabsHtml = '';
            let contentHtml = '';

            // Onglet "All" (Toutes les réactions)
            tabsHtml += `
                <button class="tab-btn flex items-center gap-2 px-4 py-3 border-b-2 border-blue-600 text-blue-600 font-medium whitespace-nowrap"
                        data-tab="all" onclick="switchReactionTab('${postId}', 'all')">
                    <i class="fas fa-layer-group"></i>
                    <span>All</span>
                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">${Object.values(reactions).reduce((sum, r) => sum + r.count, 0)}</span>
                </button>
            `;

            // Onglets par type de réaction
            sortedReactions.forEach(([name, data], index) => {
                const isActive = index === 0;
                tabsHtml += `
                    <button class="tab-btn flex items-center gap-2 px-4 py-3 border-b-2 ${isActive ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600'} font-medium whitespace-nowrap hover:text-blue-600 transition-colors"
                            data-tab="${name}" onclick="switchReactionTab('${postId}', '${name}')">
                        <i class="${data.reaction.icon} ${data.reaction.color}"></i>
                        <span>${name.charAt(0).toUpperCase() + name.slice(1)}</span>
                        <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs">${data.count}</span>
                    </button>
                `;
            });

            // Contenu pour l'onglet "All"
            contentHtml += `
                <div id="tab-content-all-${postId}" class="tab-content space-y-6">
            `;

            sortedReactions.forEach(([name, data]) => {
                contentHtml += `
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <i class="${data.reaction.icon} ${data.reaction.color} text-xl"></i>
                            <div class="flex-1">
                                <span class="font-semibold text-gray-900">${name.charAt(0).toUpperCase() + name.slice(1)}</span>
                                <span class="text-gray-500 ml-2">${data.count} ${data.count === 1 ? 'person' : 'people'}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                `;

                data.users.slice(0, 10).forEach(user => {
                    contentHtml += `
                        <div class="flex items-center gap-3 p-2 bg-white rounded-lg">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">${user.name}</span>
                                ${user.email ? `<p class="text-xs text-gray-500">${user.email}</p>` : ''}
                            </div>
                        </div>
                    `;
                });

                contentHtml += `
                        </div>
                `;

                if (data.count > 10) {
                    contentHtml += `
                        <div class="text-center mt-3 pt-3 border-t border-gray-200">
                            <span class="text-sm text-gray-500">and ${data.count - 10} more people</span>
                        </div>
                    `;
                }

                contentHtml += `
                    </div>
                `;
            });

            contentHtml += `
                </div>
            `;

            // Contenu pour chaque onglet de réaction
            sortedReactions.forEach(([name, data]) => {
                contentHtml += `
                    <div id="tab-content-${name}-${postId}" class="tab-content hidden">
                        <div class="text-center mb-6">
                            <i class="${data.reaction.icon} ${data.reaction.color} text-4xl mb-3"></i>
                            <h4 class="text-xl font-semibold text-gray-900">${name.charAt(0).toUpperCase() + name.slice(1)}</h4>
                            <p class="text-gray-600">${data.count} ${data.count === 1 ? 'person' : 'people'} reacted with ${name}</p>
                        </div>
                        <div class="space-y-3">
                `;

                data.users.forEach(user => {
                    contentHtml += `
                        <div class="flex items-center gap-4 p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="flex-1">
                                <span class="font-semibold text-gray-900">${user.name}</span>
                                ${user.email ? `<p class="text-sm text-gray-500">${user.email}</p>` : ''}
                            </div>
                            <div class="text-right">
                                <i class="${data.reaction.icon} ${data.reaction.color}"></i>
                            </div>
                        </div>
                    `;
                });

                contentHtml += `
                        </div>
                `;

                if (data.count > data.users.length) {
                    contentHtml += `
                        <div class="text-center mt-6 pt-4 border-t border-gray-200">
                            <span class="text-gray-500">and ${data.count - data.users.length} more people</span>
                        </div>
                    `;
                }

                contentHtml += `
                    </div>
                `;
            });

            if (tabs) tabs.innerHTML = tabsHtml;
            if (list) list.innerHTML = contentHtml;
        }

        function switchReactionTab(postId, tabName) {
            // Mettre à jour les onglets actifs
            const tabs = document.querySelectorAll(`#reactionTabs-${postId} .tab-btn`);
            tabs.forEach(tab => {
                const tabType = tab.getAttribute('data-tab');
                if (tabType === tabName) {
                    tab.classList.add('border-blue-600', 'text-blue-600');
                    tab.classList.remove('border-transparent', 'text-gray-600');
                } else {
                    tab.classList.remove('border-blue-600', 'text-blue-600');
                    tab.classList.add('border-transparent', 'text-gray-600');
                }
            });

            // Afficher le contenu correspondant
            const contents = document.querySelectorAll(`#reactionsList-${postId} .tab-content`);
            contents.forEach(content => {
                if (content.id === `tab-content-${tabName}-${postId}`) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
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
                    // Fermer aussi les modals de réactions
                    const openReactionModal = document.querySelector('[id^="reactionsModal-"]:not(.hidden)');
                    if (openReactionModal) {
                        const postId = openReactionModal.id.replace('reactionsModal-', '');
                        closeReactionsModal(postId);
                    }
                }
            });

            // Post editing event listeners
            const editPostBtns = document.querySelectorAll('.edit-post-btn');
            const cancelEditBtns = document.querySelectorAll('.cancel-edit-btn');

            editPostBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    editPost(postId);
                });
            });

            cancelEditBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    cancelPostEdit(postId);
                });
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

            // Create form validation and show/hide functionality
            const showCreateFormBtn = document.getElementById('showCreateFormBtn');
            const hideCreateFormBtn = document.getElementById('hideCreateFormBtn');
            const cancelCreateBtn = document.getElementById('cancelCreateBtn');
            const createPostCard = document.getElementById('createPostCard');
            const form = document.getElementById('createPostForm');

            // Initialize validation if form elements exist
            const titleInput = document.getElementById('titleInput');
            const contentTextarea = document.getElementById('contentTextarea');
            const titleCount = document.getElementById('titleCount');
            const contentCount = document.getElementById('contentCount');

            if (titleInput && contentTextarea && titleCount && contentCount) {
                updateCharCount(titleInput, titleCount, 3, 255);
                updateCharCount(contentTextarea, contentCount, 10);
                validateCreateForm();
            }

            // Event listeners for create form validation
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

            // Show/hide create form
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

            // Image upload functionality for create form
            const uploadArea = document.getElementById('uploadArea');
            const imageInput = document.getElementById('imageInput');
            const chooseFileBtn = document.getElementById('chooseFileBtn');
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeImageBtn = document.getElementById('removeImageBtn');

            if (uploadArea && imageInput) {
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
                                if (previewImage) previewImage.src = e.target.result;
                                if (fileName) fileName.textContent = file.name;
                                if (fileSize) fileSize.textContent = formatFileSize(file.size);
                                if (imagePreview) imagePreview.classList.remove('hidden');
                                if (uploadArea) uploadArea.classList.add('hidden');
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                });
            }

            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', removeImage);
            }

            // Prevent invalid form submission
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateCreateForm()) {
                        e.preventDefault();
                        showNotification('Please correct the errors in the form before submitting.', 'error');
                    } else {
                        // Hide form after successful submission
                        setTimeout(hideCreateForm, 1000);
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
        // Search functionality
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const searchResultsCount = document.getElementById('searchResultsCount');
        const postsCount = document.getElementById('postsCount');
        const clearSearchBtn = document.getElementById('clearSearchBtn');


        function performSearch(searchTerm) {
            if (searchTerm.length === 0) {
                // If search is empty, reload the page to show all posts
                window.location.reload();
                return;
            }

            loadingIndicator.classList.remove('hidden');

            // USING EXISTING ROUTE with ajax parameter
            fetch(`/user/community-posts?search=${encodeURIComponent(searchTerm)}&ajax=1`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Search failed');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('posts-container').innerHTML = data.posts;
                    postsCount.textContent = data.count;
                    searchResultsCount.classList.remove('hidden');
                    clearSearchBtn.classList.remove('hidden');

                    // Re-initialize event listeners for the new content
                    initializePostEventListeners();
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showNotification('Error performing search', 'error');
                })
                .finally(() => {
                    loadingIndicator.classList.add('hidden');
                });
        }

        function initializePostEventListeners() {
            // Re-initialize all event listeners for posts
            const editPostBtns = document.querySelectorAll('.edit-post-btn');
            const cancelEditBtns = document.querySelectorAll('.cancel-edit-btn');
            const editCommentBtns = document.querySelectorAll('.edit-comment-btn');
            const cancelCommentEditBtns = document.querySelectorAll('.cancel-comment-edit-btn');
            const imageExpandBtns = document.querySelectorAll('.image-expand-btn');

            editPostBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    editPost(postId);
                });
            });

            cancelEditBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    cancelPostEdit(postId);
                });
            });

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

            imageExpandBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const imageSrc = this.getAttribute('data-image-src');
                    openImageModal(imageSrc);
                });
            });
        }

        // Event listeners for search
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.trim();

                clearTimeout(searchTimeout);

                if (searchTerm.length >= 2 || searchTerm.length === 0) {
                    searchTimeout = setTimeout(() => {
                        performSearch(searchTerm);
                    }, 500);
                }
            });

            // Add Enter key support
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = e.target.value.trim();
                    clearTimeout(searchTimeout);
                    performSearch(searchTerm);
                }
            });
        }

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchResultsCount.classList.add('hidden');
                clearSearchBtn.classList.add('hidden');
                window.location.reload();
            });
        }

        // Initialize search functionality when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there's a search term in URL
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');

            if (searchParam) {
                searchInput.value = searchParam;
                performSearch(searchParam);
            }

            // Initialize post event listeners
            initializePostEventListeners();
        });
    </script>
</body>

</html>