@extends('layouts.app')

@section('content')
<div class="instagram-feed">
    <h2 style="text-align:center; color:#262626; font-weight:700; margin-bottom:32px;">Postagens dos Admins</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(auth()->check() && auth()->user()->is_admin)
        <a href="{{ route('postagens.create') }}" class="btn btn-primary mb-3">Nova Postagem</a>
    @endif
    @forelse($postagens as $postagem)
        <div class="instagram-card">
            <div class="instagram-card-header">
                <a href="{{ route('perfil.show', $postagem->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                    <img src="{{ $postagem->user->url_foto ? asset('storage/' . $postagem->user->url_foto) : asset('images/default-avatar.svg') }}" class="instagram-avatar" alt="avatar" style="cursor: pointer;">
                </a>
                <div>
                    <a href="{{ route('perfil.show', $postagem->user->id_usuarios) }}" style="text-decoration: none; color: inherit;">
                        <span class="instagram-username" style="cursor: pointer;">{{ $postagem->user->nome ?? 'Admin' }}</span>
                    </a>
                    <span class="instagram-date">{{ $postagem->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @if($postagem->imagem)
                <img src="{{ asset('storage/' . $postagem->imagem) }}" class="instagram-card-img" alt="Imagem da postagem">
            @endif
            <div class="instagram-card-content">
                <p style="font-weight:600;">{{ $postagem->titulo }}</p>
                <p>{{ Str::limit($postagem->conteudo, 200) }}</p>
            </div>
            <div class="instagram-actions">
                <form action="{{ route('likes.toggle', $postagem->id) }}" method="POST" style="display:inline;" class="like-form" data-post-id="{{ $postagem->id }}">
                    @csrf
                    <button type="submit" class="like-btn{{ $postagem->likes->where('user_id', auth()->id())->count() ? ' liked' : '' }}">
                        <i class="fa fa-heart{{ $postagem->likes->where('user_id', auth()->id())->count() ? '' : '-o' }}"></i>
                    </button>
                    <span class="instagram-likes like-count">{{ $postagem->likes->count() }}</span>
                </form>
                <a href="{{ route('postagens.show', $postagem->id) }}" class="comment-btn" title="Ver comentários"><i class="fa fa-comment-o"></i></a>
                <div class="dropdown d-inline-block">
                    <button class="share-btn dropdown-toggle" type="button" id="dropdownCompartilhar{{ $postagem->id }}" data-bs-toggle="dropdown" aria-expanded="false" title="Compartilhar no chat">
                        <i class="fa fa-paper-plane-o"></i>
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
        </div>
    @empty
        <p style="text-align:center; color:#999;">Nenhuma postagem encontrada.</p>
    @endforelse
    <div class="mt-3">
        {{ $postagens->links() }}
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
// AJAX para curtir/descurtir igual home
if (typeof document !== 'undefined') {
    document.querySelectorAll('.like-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var postId = form.getAttribute('data-post-id');
            var url = form.action;
            var token = form.querySelector('input[name="_token"]').value;
            var icon = form.querySelector('i');
            var countSpan = form.querySelector('.like-count');
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
                if (data.liked) {
                    icon.classList.remove('fa-heart-o');
                    icon.classList.add('fa-heart');
                    icon.style.color = '#c0392b';
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
}
</script>
@endsection 