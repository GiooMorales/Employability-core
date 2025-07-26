<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $postagem->titulo }} - Employability Core</title>
    <link rel="stylesheet" href="{{ asset('css/postagem-show.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap">
</head>
<body>
    <div class="postagem-container">
        <!-- Botão voltar -->
        <div style="padding: 1rem 1rem 0 1rem;">
            <a href="{{ route('postagens.index') }}" class="voltar-btn">Voltar para postagens</a>
        </div>

        <!-- Header da postagem -->
        <div class="postagem-header">
            <a href="{{ route('perfil.show', $postagem->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                <img class="postagem-avatar" 
                     src="{{ $postagem->user->url_foto ? asset('storage/' . $postagem->user->url_foto) : asset('images/default-avatar.svg') }}" 
                     alt="Avatar de {{ $postagem->user->nome ?? 'Usuário' }}" 
                     style="cursor: pointer;" />
            </a>
            <div class="postagem-user-info">
                <a href="{{ route('perfil.show', $postagem->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                    <div class="postagem-user-name" style="cursor: pointer;">{{ $postagem->user->nome ?? 'Admin' }}</div>
                </a>
                <div class="postagem-timestamp">Postado {{ $postagem->created_at->diffForHumans() }}</div>
            </div>
            <div class="postagem-actions">
                <button class="postagem-more-btn">
                    <span class="material-symbols-outlined">more_horiz</span>
                </button>
            </div>
        </div>

        <!-- Conteúdo da postagem -->
        <div class="postagem-content">
            <p class="postagem-text">{{ $postagem->conteudo }}</p>
            @if($postagem->imagem)
                <img class="postagem-image" 
                     src="{{ asset('storage/' . $postagem->imagem) }}" 
                     alt="Imagem da postagem" />
            @endif
        </div>

        <!-- Interações da postagem -->
        <div class="postagem-interactions">
            <div class="postagem-interactions-row">
                <div class="postagem-left-actions">
                    <form action="{{ route('likes.toggle', $postagem->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="postagem-action-btn like">
                            <span class="material-symbols-outlined postagem-action-icon">favorite</span>
                            <span class="postagem-action-count">{{ $postagem->likes->count() }}</span>
                        </button>
                    </form>
                    <button class="postagem-action-btn comment" onclick="scrollToComments()">
                        <span class="material-symbols-outlined postagem-action-icon">chat_bubble_outline</span>
                        <span class="postagem-action-count">{{ $postagem->comentarios->count() }}</span>
                    </button>
                </div>
                <div class="postagem-right-actions">
                    <button class="postagem-action-btn bookmark">
                        <span class="material-symbols-outlined postagem-action-icon">bookmark</span>
                    </button>
                    <button class="postagem-action-btn share-btn" onclick="showShareModal()">
                        <span class="material-symbols-outlined postagem-action-icon">share</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Seção de comentários -->
        <div class="postagem-comments">
            <h4 class="postagem-comments-title">Comentários</h4>

            <!-- Lista de comentários -->
            <div id="comentarios-lista">
                @forelse($postagem->comentarios as $comentario)
                    <div class="postagem-comment">
                        <a href="{{ route('perfil.show', $comentario->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                            <img class="postagem-comment-avatar" 
                                 src="{{ $comentario->user->url_foto ? asset('storage/' . $comentario->user->url_foto) : asset('images/default-avatar.svg') }}" 
                                 alt="Avatar de {{ $comentario->user->nome ?? 'Usuário' }}" 
                                 style="cursor: pointer;" />
                        </a>
                        <div class="postagem-comment-content">
                            <div class="postagem-comment-header">
                                <a href="{{ route('perfil.show', $comentario->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                                    <div class="postagem-comment-author" style="cursor: pointer;">{{ $comentario->user->nome ?? 'Usuário' }}</div>
                                </a>
                                <div class="postagem-comment-time">{{ $comentario->created_at->diffForHumans() }}</div>
                            </div>
                            <p class="postagem-comment-text">{{ $comentario->conteudo }}</p>
                            <div class="postagem-comment-actions">
                                <form action="{{ route('comment.likes.toggle', $comentario->id) }}" method="POST" style="display: inline;" class="comment-like-form" data-comment-id="{{ $comentario->id }}">
                                    @csrf
                                    <button type="submit" class="postagem-comment-action comment-like-btn" data-comment-id="{{ $comentario->id }}">
                                        <span class="material-symbols-outlined postagem-comment-action-icon comment-like-icon">
                                            {{ $comentario->likes->where('user_id', auth()->id())->count() ? 'favorite' : 'favorite_border' }}
                                        </span>
                                        <span class="comment-like-count">{{ $comentario->likes->count() }}</span>
                                    </button>
                                </form>
                                <button class="postagem-comment-action reply">Responder</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; color: #6b7280; font-size: 0.875rem; margin: 1rem 0;">Seja o primeiro a comentar!</p>
                @endforelse
            </div>

            <!-- Formulário de comentário -->
            @auth
                <div class="postagem-comment-form">
                    <img class="postagem-comment-avatar" 
                         src="{{ Auth::user()->url_foto ? asset('storage/' . Auth::user()->url_foto) : asset('images/default-avatar.svg') }}" 
                         alt="Seu avatar" />
                    <div class="postagem-comment-input-container">
                        <form action="{{ route('comentarios.store', $postagem->id) }}" method="POST" id="comentario-form">
                            @csrf
                            <input type="text" 
                                   name="conteudo" 
                                   class="postagem-comment-input" 
                                   placeholder="Adicione um comentário..." 
                                   required />
                            <button type="submit" class="postagem-comment-send">
                                <span class="material-symbols-outlined">send</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <!-- Modal de compartilhamento -->
    <div id="shareModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 1rem; max-width: 400px; width: 90%;">
            <h3 style="margin: 0 0 1rem 0; color: #111827;">Compartilhar com uma conexão</h3>
            <div style="margin-bottom: 1rem;">
                <p style="color: #6b7280; margin: 0;">Funcionalidade de compartilhamento em desenvolvimento...</p>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" onclick="hideShareModal()" style="padding: 0.5rem 1rem; background: #6b7280; color: white; border: none; border-radius: 0.375rem; cursor: pointer;">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function scrollToComments() {
                document.querySelector('.postagem-comments').scrollIntoView({ behavior: 'smooth' });
            }

            function showShareModal() {
                document.getElementById('shareModal').style.display = 'flex';
            }

            function hideShareModal() {
                document.getElementById('shareModal').style.display = 'none';
            }

            // Fechar modal ao clicar fora
            document.getElementById('shareModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideShareModal();
                }
            });

            // Tornar funções globais para o onclick funcionar
            window.scrollToComments = scrollToComments;
            window.showShareModal = showShareModal;
            window.hideShareModal = hideShareModal;

            // Likes de comentários via AJAX
            document.querySelectorAll('.comment-like-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var commentId = this.getAttribute('data-comment-id');
                    var url = this.action;
                    var token = this.querySelector('input[name="_token"]').value;
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            var likeBtn = this.querySelector('.comment-like-btn');
                            var likeIcon = likeBtn.querySelector('.comment-like-icon');
                            var likeCount = likeBtn.querySelector('.comment-like-count');
                            
                            // Atualizar ícone
                            likeIcon.textContent = data.liked ? 'favorite' : 'favorite_border';
                            
                            // Atualizar contador
                            likeCount.textContent = data.count;
                            
                            // Adicionar/remover classe de liked
                            if (data.liked) {
                                likeBtn.classList.add('liked');
                            } else {
                                likeBtn.classList.remove('liked');
                            }
                        }
                    });
                });
            });

            // Likes de postagem via AJAX
            var postagemLikeForm = document.querySelector('.postagem-action-btn.like').closest('form');
            if (postagemLikeForm) {
                postagemLikeForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var url = this.action;
                    var token = this.querySelector('input[name="_token"]').value;
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            var likeBtn = this.querySelector('.postagem-action-btn.like');
                            var likeIcon = likeBtn.querySelector('.postagem-action-icon');
                            var likeCount = likeBtn.querySelector('.postagem-action-count');
                            
                            // Atualizar ícone
                            likeIcon.textContent = data.liked ? 'favorite' : 'favorite_border';
                            
                            // Atualizar contador
                            likeCount.textContent = data.count;
                            
                            // Adicionar/remover classe de liked
                            if (data.liked) {
                                likeBtn.classList.add('liked');
                            } else {
                                likeBtn.classList.remove('liked');
                            }
                        }
                    });
                });
            }

            // Comentários via AJAX
            var form = document.getElementById('comentario-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var url = form.action;
                    var token = form.querySelector('input[name="_token"]').value;
                    var conteudo = form.querySelector('input[name="conteudo"]').value;
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ conteudo: conteudo })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.conteudo) {
                            var lista = document.getElementById('comentarios-lista');
                            var novo = document.createElement('div');
                            novo.className = 'postagem-comment';
                            novo.innerHTML = `
                                <img class="postagem-comment-avatar" src="{{ Auth::user()->url_foto ? asset('storage/' . Auth::user()->url_foto) : asset('images/default-avatar.svg') }}" alt="Seu avatar" />
                                <div class="postagem-comment-content">
                                    <div class="postagem-comment-header">
                                        <div class="postagem-comment-author">{{ Auth::user()->nome ?? 'Você' }}</div>
                                        <div class="postagem-comment-time">agora mesmo</div>
                                    </div>
                                    <p class="postagem-comment-text">${data.conteudo}</p>
                                    <div class="postagem-comment-actions">
                                        <form action="/comentarios/${data.id}/like" method="POST" style="display: inline;" class="comment-like-form" data-comment-id="${data.id}">
                                            @csrf
                                            <button type="submit" class="postagem-comment-action comment-like-btn" data-comment-id="${data.id}">
                                                <span class="material-symbols-outlined postagem-comment-action-icon comment-like-icon">favorite_border</span>
                                                <span class="comment-like-count">0</span>
                                            </button>
                                        </form>
                                        <button class="postagem-comment-action reply">Responder</button>
                                    </div>
                                </div>
                            `;
                            lista.prepend(novo);
                            form.querySelector('input[name="conteudo"]').value = '';
                            
                            // Adicionar event listener para o novo comentário
                            var newForm = novo.querySelector('.comment-like-form');
                            newForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                var commentId = this.getAttribute('data-comment-id');
                                var url = `/comentarios/${commentId}/like`;
                                var token = this.querySelector('input[name="_token"]').value;
                                
                                fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': token,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data) {
                                        var likeBtn = this.querySelector('.comment-like-btn');
                                        var likeIcon = likeBtn.querySelector('.comment-like-icon');
                                        var likeCount = likeBtn.querySelector('.comment-like-count');
                                        
                                        likeIcon.textContent = data.liked ? 'favorite' : 'favorite_border';
                                        likeCount.textContent = data.count;
                                        
                                        if (data.liked) {
                                            likeBtn.classList.add('liked');
                                        } else {
                                            likeBtn.classList.remove('liked');
                                        }
                                    }
                                });
                            });
                        }
                    });
                });
            }
        });
    </script>
</body>
</html> 