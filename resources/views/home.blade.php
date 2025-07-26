<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/home.css') }}">
    <title>Document</title>
</head>
<body>
@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="content-columns">
        <div class="feed">
            @if(auth()->check() && (auth()->user()->is_admin || str_ends_with(auth()->user()->email, '@teste.com')))
                <div class="post-creation-card">
                    <form action="{{ route('postagens.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="post-input-area">
                            <div class="user-avatar">
                                @if(Auth::user()->url_foto)
                                    <img src="{{ asset('storage/' . Auth::user()->url_foto) }}" alt="Seu avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    <img src="{{ asset('images/default-avatar.svg') }}" alt="Avatar padrão" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @endif
                            </div>
                            <div class="input-fields">
                                <input type="text" name="titulo" placeholder="Título da postagem..." required value="{{ old('titulo') }}">
                                @error('titulo')<div class="error-message">{{ $message }}</div>@enderror
                                
                                <textarea name="conteudo" placeholder="O que está acontecendo?" rows="3" required>{{ old('conteudo') }}</textarea>
                                @error('conteudo')<div class="error-message">{{ $message }}</div>@enderror
                                
                                <input type="file" name="imagem">
                                @error('imagem')<div class="error-message">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="post-actions">
                            <div class="action-buttons">
                                <button type="button" class="action-btn">
                                    <i class="fas fa-image"></i>
                                </button>
                                <button type="button" class="action-btn">
                                    <i class="fas fa-smile"></i>
                                </button>
                                <button type="button" class="action-btn">
                                    <i class="fas fa-poll"></i>
                                </button>
                            </div>
                            <button type="submit" class="post-btn">Postar</button>
                        </div>
                    </form>
                </div>
            @endif
            
            <div class="posts-container">
                @forelse($postagens as $postagem)
                    <div class="post-card">
                        <div class="post-header">
                            <div class="post-user-info">
                                <div class="user-avatar">
                                    <a href="{{ route('perfil.show', $postagem->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                                        <img src="{{ $postagem->user->url_foto ? asset('storage/' . $postagem->user->url_foto) : asset('images/default-avatar.svg') }}" alt="Avatar" style="cursor: pointer;">
                                    </a>
                                </div>
                                <div class="user-details">
                                    <a href="{{ route('perfil.show', $postagem->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                                        <h3 style="cursor: pointer; margin: 0;">{{ $postagem->user->nome ?? 'Admin' }}</h3>
                                    </a>
                                    <p>{{ $postagem->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            <button class="more-btn">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                        
                        <div class="post-content">
                            <h4>{{ $postagem->titulo }}</h4>
                            <p>{{ Str::limit($postagem->conteudo, 200) }}</p>
                            @if($postagem->imagem)
                                <div class="post-image">
                                    <img src="{{ asset('storage/' . $postagem->imagem) }}" alt="Imagem da postagem">
                                </div>
                            @endif
                        </div>
                        
                        <div class="post-footer">
                            <div class="post-actions">
                                <form action="{{ route('likes.toggle', $postagem->id) }}" method="POST" class="like-form" data-post-id="{{ $postagem->id }}">
                                    @csrf
                                    <button type="submit" class="action-btn like-btn {{ $postagem->likes->where('user_id', auth()->id())->count() ? 'liked' : '' }}">
                                        <i class="fas fa-heart"></i>
                                        <span class="like-count">{{ $postagem->likes->count() }}</span>
                                    </button>
                                </form>
                                <button class="action-btn" onclick="window.location.href='{{ route('postagens.show', $postagem->id) }}'">
                                    <i class="fas fa-comment"></i>
                                    <span>{{ $postagem->comentarios->count() }}</span>
                                </button>
                                <button class="action-btn" onclick="showShareModal({{ $postagem->id }})">
                                    <i class="fas fa-share"></i>
                                </button>
                            </div>
                            <div class="post-buttons">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="post-card">
                        <div class="post-content text-center">
                            <p>Nenhuma postagem encontrada.</p>
                        </div>
                    </div>
                @endforelse
                
                @if($postagens->hasPages())
                    <div class="pagination-container">
                        {{ $postagens->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Coração vinho quando curtido */
.like-btn.liked i {
    color: #dc2626;
}

/* Animação de pulo do coração */
@keyframes heartBeat {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

.like-btn {
    transition: all 0.15s ease;
}

.like-btn:hover {
    transform: scale(1.05);
}
</style>

<!-- Modal de compartilhamento -->
<div id="shareModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 0; border-radius: 20px; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #722F37, #8b5cf6); padding: 25px 20px; text-align: center;">
            <h3 style="margin: 0; color: white; font-size: 1.3em; font-weight: 600;">Compartilhar Postagem</h3>
            <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.9); font-size: 0.9em;">Escolha uma conexão para compartilhar</p>
        </div>
        <div style="max-height: 300px; overflow-y: auto; padding: 0;">
            <div id="connections-list">
                @foreach(Auth::user()->connections()->with('connectedUser')->get() as $conexao)
                    <div style="padding: 18px 20px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center;" 
                         onclick="shareWithConnection({{ $conexao->connected_user_id }})"
                         onmouseover="this.style.background='#f8f9fa'"
                         onmouseout="this.style.background='white'">
                        <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #722F37, #8b5cf6); display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white; font-weight: 600; font-size: 1.1em;">
                            {{ substr($conexao->connectedUser->nome ?? 'C', 0, 1) }}
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #333; margin-bottom: 2px;">{{ $conexao->connectedUser->nome ?? 'Conexão' }}</div>
                            <div style="font-size: 0.85em; color: #666;">Conexão</div>
                        </div>
                        <div style="color: #722F37; font-size: 1.2em;">→</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div style="padding: 20px; border-top: 1px solid #f0f0f0; text-align: center;">
            <button type="button" onclick="hideShareModal()" style="padding: 12px 30px; background: #f8f9fa; color: #666; border: none; border-radius: 25px; cursor: pointer; font-weight: 500; transition: all 0.2s ease;" onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f8f9fa'">Cancelar</button>
        </div>
    </div>
</div>

<script>
// Funções para o modal de compartilhamento
function showShareModal(postId) {
    document.getElementById('shareModal').style.display = 'flex';
    // Armazenar o ID da postagem para usar na função de compartilhamento
    document.getElementById('shareModal').setAttribute('data-post-id', postId);
}

function hideShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

function shareWithConnection(connectionUserId) {
    var postId = document.getElementById('shareModal').getAttribute('data-post-id');
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/postagens/${postId}/compartilhar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ connection_user_id: connectionUserId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Postagem compartilhada com sucesso!');
        } else {
            alert('Erro ao compartilhar postagem.');
        }
        hideShareModal();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao compartilhar postagem.');
        hideShareModal();
    });
}

// Fechar modal ao clicar fora
document.getElementById('shareModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideShareModal();
    }
});

// AJAX para curtir/descurtir
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var postId = form.getAttribute('data-post-id');
            var url = form.action;
            var token = form.querySelector('input[name="_token"]').value;
            var countSpan = form.querySelector('.like-count');
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                var likeBtn = form.querySelector('.like-btn');
                
                // Adicionar/remover classe 'liked'
                if (data.liked) {
                    likeBtn.classList.add('liked');
                } else {
                    likeBtn.classList.remove('liked');
                }
                
                // Adicionar animação de pulo
                likeBtn.style.animation = 'none';
                likeBtn.offsetHeight; // Trigger reflow
                likeBtn.style.animation = 'heartBeat 0.3s ease-out';
                
                if (typeof data.count !== 'undefined') {
                    countSpan.textContent = data.count;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>
</body>
</html> 