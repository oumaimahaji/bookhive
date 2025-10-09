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
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .comment-box {
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
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

        <!-- Create Post Card (Only if user wants to post in community) -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-900">Create a Post</h2>
            <form action="{{ route('user.posts.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <input type="text" name="titre" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-medium"
                           placeholder="What's on your mind?" maxlength="255">
                </div>
                <div class="mb-4">
                    <textarea name="contenu" required rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Share your thoughts with the community..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Post to Community
                    </button>
                </div>
            </form>
        </div>

        <!-- Community Posts Feed -->
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
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            Your Post
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Post Content -->
                <div class="px-6 pb-4">
                    <h4 class="text-xl font-semibold text-gray-900 mb-3">{{ $post->titre }}</h4>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $post->contenu }}</p>
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
                <p class="text-gray-500">Be the first to create a post and start the conversation!</p>
            </div>
            @endforelse
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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