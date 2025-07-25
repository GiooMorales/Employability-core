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
            @if(auth()->check() && auth()->user()->is_admin)
                <div class="post-creation-card">
                    <form action="{{ route('postagens.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="post-input-area">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->nome, 0, 2)) }}
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
                                    <img src="{{ $postagem->user->url_foto ? asset('storage/' . $postagem->user->url_foto) : asset('images/default-avatar.png') }}" alt="Avatar">
                                </div>
                                <div class="user-details">
                                    <h3>{{ $postagem->user->nome ?? 'Admin' }}</h3>
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
                                    <button type="submit" class="action-btn like-btn">
                                        <i class="fas fa-heart{{ $postagem->likes->where('user_id', auth()->id())->count() ? '' : '-o' }}"></i>
                                        <span class="like-count">{{ $postagem->likes->count() }}</span>
                                    </button>
                                </form>
                                <button class="action-btn">
                                    <i class="fas fa-comment"></i>
                                    <span>0</span>
                                </button>
                                <button class="action-btn">
                                    <i class="fas fa-share"></i>
                                    <span>0</span>
                                </button>
                            </div>
                            <div class="post-buttons">
                                <a href="{{ route('postagens.show', $postagem->id) }}" class="view-btn">Ver mais</a>
                                <div class="dropdown">
                                    <button class="share-btn dropdown-toggle">Compartilhar</button>
                                    <div class="dropdown-menu">
                                        @foreach(Auth::user()->connections()->with('connectedUser')->get() as $conexao)
                                            <form action="{{ route('postagens.share', $postagem->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="connection_user_id" value="{{ $conexao->connected_user_id }}">
                                                <button type="submit" class="dropdown-item">{{ $conexao->connectedUser->nome ?? 'Conexão' }}</button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
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

<script>
// AJAX para curtir/descurtir
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var postId = form.getAttribute('data-post-id');
            var url = form.action;
            var token = form.querySelector('input[name="_token"]').value;
            var icon = form.querySelector('i');
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
                if (data.liked) {
                    icon.classList.remove('fa-heart-o');
                    icon.classList.add('fa-heart');
                } else {
                    icon.classList.remove('fa-heart');
                    icon.classList.add('fa-heart-o');
                }
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