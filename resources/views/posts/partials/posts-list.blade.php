<div class="space-y-6" id="posts-container">
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
                <div class="flex items-center gap-2">
                    @if($post->user_id === Auth::id())
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                        Your Post
                    </span>
                    <!-- Edit and Delete buttons for own posts -->
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
                    @endif
                </div>
            </div>
        </div>

        <!-- Post Content - AVEC IMAGE -->
        <div class="post-content px-6 pb-4" id="post-content-{{ $post->id }}">
            <h4 class="text-xl font-semibold text-gray-900 mb-3">{{ $post->titre }}</h4>
            <p class="text-gray-700 leading-relaxed whitespace-pre-line mb-4">{{ $post->contenu }}</p>
            
            <!-- Composant Image -->
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

        <!-- Post Edit Form -->
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
                                 class="w-32 h-32 object-cover rounded-lg">
                            <div class="flex-1">
                                <label class="flex items-center text-sm text-red-600 cursor-pointer">
                                    <input type="checkbox" name="remove_image" value="1" class="mr-2">
                                    Remove current image
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer" onclick="document.getElementById('editImageInput{{ $post->id }}').click()">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Click to change image</p>
                        <input type="file" name="image" 
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               class="hidden"
                               id="editImageInput{{ $post->id }}">
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

        <!-- Rest of your post content (comments, etc.) -->
        <!-- Copy all the comment-related content from your main file here -->
        
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <i class="fas fa-newspaper text-4xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Posts Found</h3>
        <p class="text-gray-500">No posts match your search criteria.</p>
    </div>
    @endforelse
</div>