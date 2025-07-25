@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('postagens.index') }}" class="btn btn-link">&larr; Voltar para postagens</a>
    <div class="card mb-4">
        <div class="card-body">
            <h2>{{ $postagem->titulo }}</h2>
            <p class="text-muted">Por {{ $postagem->user->nome ?? 'Admin' }} em {{ $postagem->created_at->format('d/m/Y H:i') }}</p>
            @if($postagem->imagem)
                <img src="{{ asset('storage/' . $postagem->imagem) }}" alt="Imagem da postagem" class="img-fluid mb-2" style="max-width:400px;">
            @endif
            <p>{{ $postagem->conteudo }}</p>
            <div class="mb-2">
                <form action="{{ route('likes.toggle', $postagem->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        Curtir ({{ $postagem->likes->count() }})
                    </button>
                </form>
                <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#compartilharModal">Compartilhar no chat</button>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Comentários ({{ $postagem->comentarios->count() }})</div>
        <div class="card-body">
            @auth
            <form action="{{ route('comentarios.store', $postagem->id) }}" method="POST" id="comentario-form">
                @csrf
                <div class="mb-2">
                    <textarea name="conteudo" class="form-control" rows="2" placeholder="Escreva um comentário..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Comentar</button>
            </form>
            <hr>
            @endauth
            <div id="comentarios-lista">
            @forelse($postagem->comentarios as $comentario)
                <div class="mb-2">
                    <strong>{{ $comentario->user->nome ?? 'Usuário' }}</strong> <span class="text-muted">{{ $comentario->created_at->diffForHumans() }}</span>
                    <p>{{ $comentario->conteudo }}</p>
                </div>
            @empty
                <p>Seja o primeiro a comentar!</p>
            @endforelse
            </div>
        </div>
    </div>
</div>

@php
    $conexoes = Auth::user()->connections()->with('connectedUser')->get();
@endphp

<!-- Modal Compartilhar -->
<div class="modal fade" id="compartilharModal" tabindex="-1" aria-labelledby="compartilharModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('postagens.share', $postagem->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="compartilharModalLabel">Compartilhar com uma conexão</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="connection_user_id" class="form-label">Escolha a conexão:</label>
            <select name="connection_user_id" id="connection_user_id" class="form-control" required>
              <option value="">Selecione...</option>
              @foreach($conexoes as $conexao)
                <option value="{{ $conexao->connected_user_id }}">{{ $conexao->connectedUser->nome ?? 'Conexão' }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Compartilhar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('comentario-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var url = form.action;
            var token = form.querySelector('input[name="_token"]').value;
            var conteudo = form.querySelector('textarea[name="conteudo"]').value;
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
                    novo.className = 'mb-2';
                    novo.innerHTML = `<strong>${data.user ? data.user.nome : 'Você'}</strong> <span class='text-muted'>agora mesmo</span><p>${data.conteudo}</p>`;
                    lista.prepend(novo);
                    form.querySelector('textarea[name="conteudo"]').value = '';
                }
            });
        });
    }
});
</script>
@endsection 