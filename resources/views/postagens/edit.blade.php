@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Postagem</h1>
    <form action="{{ route('postagens.update', $postagem->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required value="{{ old('titulo', $postagem->titulo) }}">
            @error('titulo')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="conteudo" class="form-label">Conteúdo</label>
            <textarea name="conteudo" id="conteudo" class="form-control" rows="6" required>{{ old('conteudo', $postagem->conteudo) }}</textarea>
            @error('conteudo')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem (opcional)</label>
            @if($postagem->imagem)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $postagem->imagem) }}" alt="Imagem atual" style="max-width:200px;">
                </div>
            @endif
            <input type="file" name="imagem" id="imagem" class="form-control">
            @error('imagem')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="{{ route('postagens.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection 