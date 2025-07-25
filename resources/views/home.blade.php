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
        <div class="left-sidebar">
            <div class="profile-card">
                <div class="profile-cover"></div>
                <div class="profile-info">
                <img src="{{ Auth::user()->url_foto ? asset('storage/' . Auth::user()->url_foto) : asset('images/default-avatar.png') }}" alt="{{ Auth::user()->nome }}" class="user-avatar">
                    
                    <h3 class="profile-name">{{ $nome }}</h3>
                    <p class="profile-title">{{ $trab_atual }}</p>
                    <div class="profile-stats">
                        <div class="stat">
                            <div class="stat-number">{{ $estatisticas['conexoes'] ?? 0 }}</div>
                            <div class="stat-label">Conexões</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">{{ $estatisticas['projetos'] ?? 0 }}</div>
                            <div class="stat-label">Projetos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">{{ $estatisticas['contribuicoes'] ?? 0 }}</div>
                            <div class="stat-label">Contribuições</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="trending-card">
                <h3 class="card-title">Meus Repositórios</h3>
                @for($i = 0; $i < 2; $i++)
                    <div class="repo-card">
                        <div class="repo-header">
                            <div class="repo-name"></div>
                            <div class="repo-visibility"></div>
                        </div>
                        <div class="repo-description"></div>
                        <div class="repo-stats">
                            <div class="repo-stat"><i class="fas fa-star"></i></div>
                            <div class="repo-stat"><i class="fas fa-code-branch"></i></div>
                            <div class="repo-stat"><i class="fas fa-code"></i></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div class="feed">
            @if(auth()->check() && auth()->user()->is_admin)
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>Criar nova postagem</h4>
                        <form action="{{ route('postagens.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="titulo" class="form-control" placeholder="Título" required value="{{ old('titulo') }}">
                                @error('titulo')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-2">
                                <textarea name="conteudo" class="form-control" rows="3" placeholder="Conteúdo" required>{{ old('conteudo') }}</textarea>
                                @error('conteudo')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-2">
                                <input type="file" name="imagem" class="form-control">
                                @error('imagem')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-success">Publicar</button>
                        </form>
                    </div>
                </div>
            @endif
            {{-- Feed de postagens dos admins --}}
            <h2>Postagens dos Admins</h2>
            <div class="list-group">
                @forelse($postagens as $postagem)
                    <div class="list-group-item mb-3">
                        <h4>{{ $postagem->titulo }}</h4>
                        <p class="text-muted">Por {{ $postagem->user->nome ?? 'Admin' }} em {{ $postagem->created_at->format('d/m/Y H:i') }}</p>
                        @if($postagem->imagem)
                            <img src="{{ asset('storage/' . $postagem->imagem) }}" alt="Imagem da postagem" class="img-fluid mb-2" style="max-width:300px;">
                        @endif
                        <p>{{ Str::limit($postagem->conteudo, 200) }}</p>
                        <a href="{{ route('postagens.show', $postagem->id) }}" class="btn btn-outline-primary btn-sm">Ver mais</a>
                        <form action="{{ route('likes.toggle', $postagem->id) }}" method="POST" style="display:inline;" class="like-form" data-post-id="{{ $postagem->id }}">
                            @csrf
                            <button type="submit" class="like-btn" style="background: none; border: none; padding: 0;">
                                <i class="fa fa-heart{{ $postagem->likes->where('user_id', auth()->id())->count() ? '' : '-o' }}" style="font-size: 1.5rem; color: {{ $postagem->likes->where('user_id', auth()->id())->count() ? '#722F37' : '#fff' }}; text-shadow: 0 0 2px #aaa;"></i>
                            </button>
                            <span class="like-count" style="margin-left: 4px; color: #722F37; font-weight: bold;">{{ $postagem->likes->count() }}</span>
                        </form>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" id="dropdownCompartilhar{{ $postagem->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                Compartilhar no chat
                            </button>
                            <div class="dropdown-menu p-3" aria-labelledby="dropdownCompartilhar{{ $postagem->id }}" style="min-width: 250px;">
                                <input type="text" class="form-control mb-2" placeholder="Buscar conexão..." onkeyup="filtrarConexoes(this, 'lista-conexoes-{{ $postagem->id }}')">
                                <div id="lista-conexoes-{{ $postagem->id }}" style="max-height: 200px; overflow-y: auto;">
                                    @foreach(Auth::user()->connections()->with('connectedUser')->get() as $conexao)
                                        <form action="{{ route('postagens.share', $postagem->id) }}" method="POST" class="mb-1">
                                            @csrf
                                            <input type="hidden" name="connection_user_id" value="{{ $conexao->connected_user_id }}">
                                            <button type="submit" class="dropdown-item">{{ $conexao->connectedUser->nome ?? 'Conexão' }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>Nenhuma postagem encontrada.</p>
                @endforelse
            </div>
            <div class="mt-3">
                {{ $postagens->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="postModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Publicação</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-content">
            <!-- conteúdo do post -->
        </div>
    </div>
</div>
@endsection

<script>
function filtrarConexoes(input, listaId) {
    var filtro = input.value.toLowerCase();
    var lista = document.getElementById(listaId);
    if (!lista) return;
    var itens = lista.getElementsByTagName('button');
    for (var i = 0; i < itens.length; i++) {
        var nome = itens[i].textContent || itens[i].innerText;
        itens[i].parentElement.style.display = nome.toLowerCase().includes(filtro) ? '' : 'none';
    }
}
</script>
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
            // Feedback visual imediato (opcional)
            icon.classList.add('fa-spin');
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
                // Atualiza o coração e o número de curtidas
                if (data.liked) {
                    icon.classList.remove('fa-heart-o');
                    icon.classList.add('fa-heart');
                    icon.style.color = '#722F37';
                } else {
                    icon.classList.remove('fa-heart');
                    icon.classList.add('fa-heart-o');
                    icon.style.color = '#fff';
                }
                if (typeof data.count !== 'undefined') {
                    countSpan.textContent = data.count;
                }
            })
            .finally(() => {
                icon.classList.remove('fa-spin');
            });
        });
    });
});
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.like-btn:hover i {
    color: #a94442 !important;
    transition: color 0.2s;
}
.like-btn i {
    transition: color 0.2s;
}
</style>
</body>
</html> 