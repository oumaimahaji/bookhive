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

<script>
// Variables globales pour éviter les doubles clics
let isReacting = {};

// Fonction pour basculer l'affichage de tous les commentaires
function toggleAllComments(postId) {
    const recentComments = document.getElementById(`recent-comments-${postId}`);
    const allComments = document.getElementById(`all-comments-${postId}`);
    const viewAllBtn = document.querySelector(`.view-all-comments-btn[data-post-id="${postId}"]`);
    
    if (allComments && recentComments && viewAllBtn) {
        const isShowingAll = allComments.style.display === 'block';
        
        if (isShowingAll) {
            allComments.style.display = 'none';
            recentComments.style.display = 'block';
            viewAllBtn.textContent = `View all ${viewAllBtn.getAttribute('data-total-comments')} comments`;
        } else {
            allComments.style.display = 'block';
            recentComments.style.display = 'none';
            viewAllBtn.textContent = 'Hide comments';
        }
    }
}

// Fonction pour basculer le formulaire de commentaire
function toggleCommentSection(postId) {
    const commentSection = document.getElementById(`comment-section-${postId}`);
    
    if (commentSection) {
        const isVisible = commentSection.style.display === 'block';
        commentSection.style.display = isVisible ? 'none' : 'block';
        
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

function initReactionHover(postId) {
    const reactionBtn = document.querySelector(`.reaction-btn[data-post-id="${postId}"]`);
    const palette = document.getElementById(`reaction-palette-${postId}`);
    const group = document.getElementById(`reaction-group-${postId}`);
    
    if (!reactionBtn || !palette) return;
    
    let hideTimeout;
    
    reactionBtn.addEventListener('mouseenter', function() {
        clearTimeout(hideTimeout);
        palette.classList.remove('hidden');
    });
    
    group.addEventListener('mouseleave', function() {
        hideTimeout = setTimeout(() => {
            palette.classList.add('hidden');
        }, 300);
    });
    
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
    const options = document.querySelectorAll(`.reaction-option[data-post-id="${postId}"]`);
    
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const reaction = this.getAttribute('data-reaction');
            
            const palette = document.getElementById(`reaction-palette-${postId}`);
            if (palette) {
                palette.classList.add('hidden');
            }
            
            reactToPost(postId, reaction);
        });
    });
}

function handleReactionClick(postId) {
    const reactionBtn = document.querySelector(`.reaction-btn[data-post-id="${postId}"]`);
    const currentReaction = reactionBtn?.getAttribute('data-user-reaction');
    
    if (currentReaction && currentReaction !== '') {
        reactToPost(postId, currentReaction);
    } else {
        reactToPost(postId, 'like');
    }
}

function reactToPost(postId, reaction) {
    if (isReacting[postId]) {
        return;
    }
    
    isReacting[postId] = true;
    
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value ||
                   '{{ csrf_token() }}';

    const reactionBtn = document.querySelector(`.reaction-btn[data-post-id="${postId}"]`);
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
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        updateReactionUI(postId, data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error reacting to post: ' + error.message);
    })
    .finally(() => {
        if (reactionBtn) {
            reactionBtn.disabled = false;
            reactionBtn.classList.remove('opacity-50');
        }
        isReacting[postId] = false;
    });
}

function updateReactionUI(postId, data) {
    const container = document.querySelector(`.reactions-container[data-post-id="${postId}"]`);
    if (!container) {
        return;
    }

    const reactionBtn = container.querySelector('.reaction-btn');
    const reactionText = container.querySelector('.reaction-text');
    const reactionIcon = container.querySelector('.reaction-btn i');
    const reactionCount = container.querySelector('.reaction-count');
    const reactionIconsContainer = container.querySelector(`#reaction-icons-${postId}`);
    
    if (!reactionBtn || !reactionText || !reactionIcon) {
        return;
    }
    
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
    
    const totalReactions = data.total_reactions || 0;
    if (reactionCount) {
        if (totalReactions > 0) {
            reactionCount.textContent = totalReactions;
            reactionCount.style.display = 'inline';
        } else {
            reactionCount.style.display = 'none';
        }
    }
    
    updateReactionIcons(postId, data.reactions_count);
}

function updateReactionIcons(postId, reactionsCount) {
    const container = document.querySelector(`#reaction-icons-${postId}`);
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

// FONCTIONS POUR LE MODAL DES RÉACTIONS (le modal est maintenant dans le layout principal)

// Fonction pour afficher le modal des réactions
function showReactionsModal(postId) {
    const modal = document.getElementById(`reactionsModal-${postId}`);
    
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    loadReactionsForModal(postId);
}

// Fonction pour fermer le modal des réactions
function closeReactionsModal(postId) {
    const modal = document.getElementById(`reactionsModal-${postId}`);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Fonction pour charger les réactions dans le modal
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

// Fonction pour afficher les réactions dans le modal avec onglets
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
    const sortedReactions = Object.entries(reactions).sort(([,a], [,b]) => b.count - a.count);
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

// Fonction pour changer d'onglet dans le modal
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

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
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

    // Fermer le modal en cliquant en dehors
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fixed') && e.target.id?.startsWith('reactionsModal-')) {
            const postId = e.target.id.replace('reactionsModal-', '');
            closeReactionsModal(postId);
        }
    });

    // Fermer le modal avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('[id^="reactionsModal-"]:not(.hidden)');
            if (openModal) {
                const postId = openModal.id.replace('reactionsModal-', '');
                closeReactionsModal(postId);
            }
        }
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