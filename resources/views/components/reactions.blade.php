<div class="reactions-container" data-post-id="{{ $post->id }}">
    <!-- Barre de réactions -->
    <div class="flex items-center justify-between py-3 border-t border-gray-100">
        <!-- Compteur de réactions -->
        <div class="flex items-center gap-2 flex-1">
            @if($post->total_reactions > 0)
            <div class="flex items-center -space-x-1" id="reaction-icons-{{ $post->id }}">
                @foreach($post->top_reactions as $name => $data)
                <div class="w-6 h-6 bg-white rounded-full border border-white flex items-center justify-center shadow-sm" 
                     title="{{ ucfirst($name) }}: {{ $data['count'] }}">
                    <i class="{{ $data['reaction']->icon }} {{ $data['reaction']->color }} text-xs"></i>
                </div>
                @endforeach
            </div>
            <span class="text-sm text-gray-600 reaction-count cursor-pointer hover:underline"
                  onclick="showReactionsModal('{{ $post->id }}')">
                {{ $post->total_reactions }}
            </span>
            @endif
        </div>

        <!-- Compteur de commentaires -->
        <div class="text-sm text-gray-600 flex-shrink-0 ml-4">
            {{ $post->comments->count() }} {{ Str::plural('comment', $post->comments->count()) }}
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="flex border-t border-gray-100">
        <!-- Bouton Réaction -->
        <div class="flex-1 relative group" id="reaction-group-{{ $post->id }}">
            <button class="reaction-btn w-full py-2 text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center justify-center gap-2"
                    data-post-id="{{ $post->id }}"
                    data-user-reaction="{{ $post->userReaction?->reaction->name ?? '' }}">
                @if($post->userReaction)
                <i class="{{ $post->userReaction->reaction->icon }} {{ $post->userReaction->reaction->color }}"></i>
                <span class="reaction-text">{{ ucfirst($post->userReaction->reaction->name) }}</span>
                @else
                <i class="far fa-thumbs-up"></i>
                <span class="reaction-text">Like</span>
                @endif
            </button>
            
            <!-- Palette de réactions -->
            <div class="reaction-palette absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden bg-white rounded-full shadow-lg border border-gray-200 p-1"
                 id="reaction-palette-{{ $post->id }}">
                @foreach(\App\Models\Reaction::all() as $reaction)
                <button class="reaction-option w-8 h-8 rounded-full hover:scale-125 transform transition-transform mx-1"
                        data-reaction="{{ $reaction->name }}"
                        data-post-id="{{ $post->id }}"
                        title="{{ ucfirst($reaction->name) }}">
                    <i class="{{ $reaction->icon }} {{ $reaction->color }}"></i>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Bouton Commenter -->
        <button class="flex-1 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors flex items-center justify-center gap-2 comment-toggle-btn"
                data-post-id="{{ $post->id }}">
            <i class="far fa-comment"></i>
            <span>Comment</span>
        </button>
    </div>
</div>

<!-- Modal des réactions détaillées -->
<div id="reactionsModal-{{ $post->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full max-h-96 overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold">Reactions</h3>
            <button class="close-modal text-gray-500 hover:text-gray-700" 
                    onclick="closeReactionsModal('{{ $post->id }}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-4 reactions-list" id="reactionsList-{{ $post->id }}">
            <div class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                <p class="text-gray-500 mt-2">Loading reactions...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales pour éviter les doubles clics
let isReacting = {};

// Fonction pour basculer l'affichage de tous les commentaires
function toggleAllComments(postId) {
    const myComments = document.getElementById(my-comments-${postId});
    const allComments = document.getElementById(all-comments-${postId});
    const viewAllBtn = document.querySelector(.view-all-comments-btn[data-post-id="${postId}"]);
    
    if (allComments && myComments && viewAllBtn) {
        const isShowingAll = allComments.style.display === 'block';
        
        if (isShowingAll) {
            // Cacher tous les commentaires, montrer seulement les miens
            allComments.style.display = 'none';
            myComments.style.display = 'block';
            viewAllBtn.textContent = View all ${viewAllBtn.getAttribute('data-total-comments')} comments;
        } else {
            // Montrer tous les commentaires, cacher les miens
            allComments.style.display = 'block';
            myComments.style.display = 'none';
            viewAllBtn.textContent = 'Hide comments';
        }
    }
}

// Fonction pour basculer l'affichage de tous les commentaires
function toggleAllComments(postId) {
    const recentComments = document.getElementById(recent-comments-${postId});
    const allComments = document.getElementById(all-comments-${postId});
    const viewAllBtn = document.querySelector(.view-all-comments-btn[data-post-id="${postId}"]);
    
    if (allComments && recentComments && viewAllBtn) {
        const isShowingAll = allComments.style.display === 'block';
        
        if (isShowingAll) {
            // Cacher tous les commentaires, montrer seulement les récents
            allComments.style.display = 'none';
            recentComments.style.display = 'block';
            viewAllBtn.textContent = View all ${viewAllBtn.getAttribute('data-total-comments')} comments;
        } else {
            // Montrer tous les commentaires, cacher les récents
            allComments.style.display = 'block';
            recentComments.style.display = 'none';
            viewAllBtn.textContent = 'Hide comments';
        }
    }
}

// Fonction pour basculer le formulaire de commentaire
function toggleCommentSection(postId) {
    const commentSection = document.getElementById(comment-section-${postId});
    
    if (commentSection) {
        // Basculer l'affichage du formulaire de commentaire
        const isVisible = commentSection.style.display === 'block';
        commentSection.style.display = isVisible ? 'none' : 'block';
        
        // Focus sur le champ de commentaire si on l'affiche
        if (!isVisible) {
            setTimeout(() => {
                const commentInput = commentSection.querySelector('input[name="contenu"]');
                if (commentInput) {
                    commentInput.focus();
                }
            }, 100);
        }
    }
}

// Fonction pour rafraîchir les commentaires après en avoir ajouté un
function refreshComments(postId) {
    // Recharger la page ou faire un AJAX pour rafraîchir les commentaires
    // Pour l'instant, on va simplement rafraîchir la page
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Les autres fonctions restent identiques...
function initReactionHover(postId) {
    const reactionBtn = document.querySelector(.reaction-btn[data-post-id="${postId}"]);
    const palette = document.getElementById(reaction-palette-${postId});
    const group = document.getElementById(reaction-group-${postId});
    
    if (!reactionBtn || !palette) return;
    
    let hideTimeout;
    
    // Montrer la palette au hover
    reactionBtn.addEventListener('mouseenter', function() {
        clearTimeout(hideTimeout);
        palette.classList.remove('hidden');
    });
    
    // Cacher la palette quand la souris quitte
    group.addEventListener('mouseleave', function() {
        hideTimeout = setTimeout(() => {
            palette.classList.add('hidden');
        }, 300);
    });
    
    // Garder la palette visible quand la souris est dessus
    palette.addEventListener('mouseenter', function() {
        clearTimeout(hideTimeout);
    });
    
    palette.addEventListener('mouseleave', function() {
        hideTimeout = setTimeout(() => {
            palette.classList.add('hidden');
        }, 300);
    });
}

function initReactionOptions(postId) {
    const options = document.querySelectorAll(.reaction-option[data-post-id="${postId}"]);
    
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const reaction = this.getAttribute('data-reaction');
            console.log('Reaction option clicked:', postId, reaction);
            
            // Cacher la palette après le clic
            const palette = document.getElementById(reaction-palette-${postId});
            if (palette) {
                palette.classList.add('hidden');
            }
            
            reactToPost(postId, reaction);
        });
    });
}

function handleReactionClick(postId) {
    const reactionBtn = document.querySelector(.reaction-btn[data-post-id="${postId}"]);
    const currentReaction = reactionBtn?.getAttribute('data-user-reaction');
    
    if (currentReaction && currentReaction !== '') {
        reactToPost(postId, currentReaction);
    } else {
        reactToPost(postId, 'like');
    }
}

function reactToPost(postId, reaction) {
    // Éviter les doubles clics
    if (isReacting[postId]) {
        console.log('Already reacting to post:', postId);
        return;
    }
    
    isReacting[postId] = true;
    console.log('Reacting to post:', postId, 'with:', reaction);
    
    // Récupérer le token CSRF
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value ||
                   '{{ csrf_token() }}';

    // Désactiver le bouton pendant la requête
    const reactionBtn = document.querySelector(.reaction-btn[data-post-id="${postId}"]);
    if (reactionBtn) {
        reactionBtn.disabled = true;
        reactionBtn.classList.add('opacity-50');
    }

    fetch('/user/posts/' + postId + '/react', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            reaction: reaction,
            _token: csrfToken
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Success:', data);
        updateReactionUI(postId, data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error reacting to post: ' + error.message);
    })
    .finally(() => {
        // Réactiver le bouton
        if (reactionBtn) {
            reactionBtn.disabled = false;
            reactionBtn.classList.remove('opacity-50');
        }
        isReacting[postId] = false;
    });
}

function updateReactionUI(postId, data) {
    console.log('Updating UI for post:', postId, 'with data:', data);
    
    const container = document.querySelector(.reactions-container[data-post-id="${postId}"]);
    if (!container) {
        console.error('Container not found for post:', postId);
        return;
    }

    const reactionBtn = container.querySelector('.reaction-btn');
    const reactionText = container.querySelector('.reaction-text');
    const reactionIcon = container.querySelector('.reaction-btn i');
    const reactionCount = container.querySelector('.reaction-count');
    const reactionIconsContainer = container.querySelector(#reaction-icons-${postId});
    
    if (!reactionBtn || !reactionText || !reactionIcon) {
        console.error('One or more reaction elements not found');
        return;
    }
    
    // MISE À JOUR IMMÉDIATE ET STABLE
    if (data.user_reaction) {
        reactionBtn.setAttribute('data-user-reaction', data.user_reaction);
        const reactionClasses = {
            like: 'fas fa-thumbs-up text-blue-600',
            love: 'fas fa-heart text-red-600', 
            haha: 'fas fa-laugh text-yellow-600',
            wow: 'fas fa-surprise text-yellow-500',
            sad: 'fas fa-sad-tear text-blue-500',
            angry: 'fas fa-angry text-red-700'
        };
        reactionIcon.className = reactionClasses[data.user_reaction] || 'far fa-thumbs-up';
        reactionText.textContent = data.user_reaction.charAt(0).toUpperCase() + data.user_reaction.slice(1);
    } else {
        reactionBtn.setAttribute('data-user-reaction', '');
        reactionIcon.className = 'far fa-thumbs-up';
        reactionText.textContent = 'Like';
    }
    
    // Mettre à jour le compteur total
    const totalReactions = data.total_reactions || 0;
    if (reactionCount) {
        if (totalReactions > 0) {
            reactionCount.textContent = totalReactions;
            reactionCount.style.display = 'inline';
        } else {
            reactionCount.style.display = 'none';
        }
    }
    
    // Mettre à jour les icônes des réactions populaires
    updateReactionIcons(postId, data.reactions_count);
}

function updateReactionIcons(postId, reactionsCount) {
    const container = document.querySelector(#reaction-icons-${postId});
    if (!container || !reactionsCount) return;
    
    const topReactions = Object.entries(reactionsCount)
        .filter(([name, count]) => count > 0)
        .sort(([,a], [,b]) => b - a)
        .slice(0, 3);
    
    let html = '';
    topReactions.forEach(([name, count]) => {
        const reactionClasses = {
            like: 'fas fa-thumbs-up text-blue-600',
            love: 'fas fa-heart text-red-600', 
            haha: 'fas fa-laugh text-yellow-600',
            wow: 'fas fa-surprise text-yellow-500',
            sad: 'fas fa-sad-tear text-blue-500',
            angry: 'fas fa-angry text-red-700'
        };
        
        const iconClass = reactionClasses[name] || 'fas fa-thumbs-up text-blue-600';
        html += `<div class="w-6 h-6 bg-white rounded-full border border-white flex items-center justify-center shadow-sm" 
                     title="${name.charAt(0).toUpperCase() + name.slice(1)}: ${count}">
                    <i class="${iconClass} text-xs"></i>
                </div>`;
    });
    
    container.innerHTML = html;
}

// Fonction pour afficher le modal des réactions
function showReactionsModal(postId) {
    const modal = document.getElementById(reactionsModal-${postId});
    const list = document.getElementById(reactionsList-${postId});
    
    if (modal) {
        modal.classList.remove('hidden');
    }
    
    if (list) {
        list.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-gray-400"></i><p class="text-gray-500 mt-2">Loading reactions...</p></div>';
        
        fetch(/user/posts/${postId}/reactions)
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
                displayReactionsList(list, data.reactions);
            })
            .catch(error => {
                console.error('Error loading reactions:', error);
                list.innerHTML = `<div class="text-center py-4 text-red-500">
                    <i class="fas fa-exclamation-triangle mb-2"></i>
                    <p>Error loading reactions</p>
                    <p class="text-sm text-gray-500 mt-1">${error.message}</p>
                </div>`;
            });
    }
}

// Fonction pour fermer le modal des réactions
function closeReactionsModal(postId) {
    const modal = document.getElementById(reactionsModal-${postId});
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Fonction pour afficher la liste des réactions dans le modal
function displayReactionsList(container, reactions) {
    let html = '';
    
    if (!reactions || Object.keys(reactions).length === 0) {
        html = '<p class="text-gray-500 text-center py-4">No reactions yet</p>';
    } else {
        const sortedReactions = Object.entries(reactions).sort(([,a], [,b]) => b.count - a.count);
        
        sortedReactions.forEach(([name, data]) => {
            html += <div class="mb-6">;
            html += <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-lg">;
            html += <i class="${data.reaction.icon} ${data.reaction.color} text-xl"></i>;
            html += <div class="flex-1">;
            html += <span class="font-semibold text-gray-900">${name.charAt(0).toUpperCase() + name.slice(1)}</span>;
            html += <span class="text-gray-500 ml-2">${data.count} ${data.count === 1 ? 'person' : 'people'}</span>;
            html += </div>;
            html += </div>;
            html += <div class="space-y-2 ml-4">;
            
            if (data.users && data.users.length > 0) {
                data.users.forEach(user => {
                    if (user && user.name) {
                        html += <div class="flex items-center gap-3 py-2">;
                        html += <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold">;
                        html += user.name.charAt(0).toUpperCase();
                        html += </div>;
                        html += <span class="text-sm text-gray-700">${user.name}</span>;
                        html += </div>;
                    }
                });
                
                if (data.count > data.users.length) {
                    html += <div class="text-sm text-gray-500 mt-2">and ${data.count - data.users.length} more...</div>;
                }
            }
            
            html += </div></div>;
        });
    }
    
    if (container) {
        container.innerHTML = html;
    }
}
// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reactions system initialized');
    
    // Initialiser le hover pour chaque post
    document.querySelectorAll('.reactions-container').forEach(container => {
        const postId = container.getAttribute('data-post-id');
        initReactionHover(postId);
        initReactionOptions(postId);
    });

    // Gestion du bouton de réaction principal
    document.querySelectorAll('.reaction-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.getAttribute('data-post-id');
            handleReactionClick(postId);
        });
    });

    // Gestion de l'affichage des commentaires
    document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            toggleCommentSection(postId);
        });
    });

    // Initialiser les boutons "View all comments"
    document.querySelectorAll('.view-all-comments-btn').forEach(btn => {
        const postId = btn.getAttribute('data-post-id');
        const totalComments = btn.textContent.match(/\d+/)?.[0] || '0';
        btn.setAttribute('data-total-comments', totalComments);
    });
});
</script>

<style>
.reaction-palette {
    transition: all 0.3s ease;
    opacity: 0;
    transform: translate(-50%, 10px);
    z-index: 1000;
}

.reaction-palette:not(.hidden) {
    opacity: 1;
    transform: translate(-50%, 0);
}

.reaction-option {
    transition: all 0.2s ease;
}

.reaction-option:hover {
    transform: scale(1.3) translateY(-5px);
}

.reaction-btn:disabled {
    cursor: not-allowed;
    opacity: 0.7;
}
</style>
