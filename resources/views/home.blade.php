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
                            <div class="stat-number">254</div>
                            <div class="stat-label">Conexões</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">42</div>
                            <div class="stat-label">Projetos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">18</div>
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
            <div class="post-creation">
                <textarea class="post-input" placeholder="Digite algo para publicar..."></textarea>
                <div class="post-actions">
                    <div class="post-tools">
                        <div class="post-tool"><i class="fas fa-image"></i><span>Imagem</span></div>
                        <div class="post-tool"><i class="fas fa-code"></i><span>Código</span></div>
                        <div class="post-tool"><i class="fas fa-link"></i><span>Link</span></div>
                    </div>
                    <button class="publish-btn">Publicar</button>
                </div>
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

</body>
</html> 