<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/perfil.css') }}">
    <title>Perfil - {{ $usuario->nome }}</title>
</head>
<body>
@extends('layouts.app')

@section('title', 'Perfil - ' . $usuario->nome)

@section('content')
<!-- comentario mto legal -->
<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-cover"></div>
    <div class="profile-main">
        <div class="profile-avatar-container">
            <img src="{{ $usuario->url_foto ? asset('storage/' . $usuario->url_foto) : asset('images/default-avatar.png') }}" 
                 alt="{{ $usuario->nome }}" 
                 class="profile-avatar-large">
        </div>
        <div class="profile-info">
            <div class="profile-name-container">
                <h1 class="profile-name">{{ $usuario->nome }}</h1>
                @if($usuario->id_usuarios === auth()->id())
                <div class="profile-actions">
                    <button class="profile-action-btn edit-btn">
                        <a href="{{ route('editar') }}">
                            <i class="fas fa-pen"></i> Editar perfil
                        </a>
                    </button>
                    <button class="profile-action-btn share-btn">
                        <i class="fas fa-share-alt"></i> Compartilhar
                    </button>
                </div>
                @endif
            </div>
            <h2 class="profile-title">{{ $usuario->titulo ?? '' }}</h2>
            <div class="profile-meta">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $usuario->cidade && $usuario->estado ? $usuario->cidade . ', ' . $usuario->estado : 'Localização desconhecida' }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-link"></i>
                    @if($usuario->link)
                        <a href="{{ $usuario->link }}" target="_blank" class="profile-link">{{ $usuario->link }}</a>
                    @else
                        <span class="text-muted">Nenhum link adicionado</span>
                    @endif
                </div>
                <div class="meta-item">
                    <span>{{ $usuario->profissao ?? 'Sem Profissão' }}</span>
                </div>

                @if(Auth::id() !== $usuario->id_usuarios)
                    <div class="meta-item connection-actions">
                        {{-- Botão de Conectar --}}
                        @if($statusConexao === null)
                            <form action="{{ route('connections.send', $usuario->id_usuarios) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-connect">
                                    <i class="fas fa-user-plus"></i> Conectar
                                </button>
                            </form>
                        @elseif($statusConexao === 'pendente')
                            <button class="btn btn-pending" disabled>
                                <i class="fas fa-clock"></i> Solicitação enviada
                            </button>
                        @elseif($statusConexao === 'aceita')
                            <button class="btn btn-connected" disabled>
                                <i class="fas fa-check"></i> Conectado
                            </button>
                        @elseif($statusConexao === 'recusada')
                            <button class="btn btn-rejected" disabled>
                                <i class="fas fa-times"></i> Solicitação recusada
                            </button>
                        @endif

                        {{-- Botão de Compartilhar --}}
                        <button class="btn btn-share" onclick="compartilharPerfil()">
                            <i class="fas fa-share-alt"></i> Compartilhar
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="profile-stats-bar">
        <div class="stat">
            <div class="stat-number">{{ $estatisticas['conexoes'] ?? '0' }}</div>
            <div class="stat-label">Conexões</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ $estatisticas['projetos'] ?? '0' }}</div>
            <div class="stat-label">Projetos</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ $estatisticas['certificados'] ?? '0' }}</div>
            <div class="stat-label">Certificados</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ $estatisticas['contribuicoes'] ?? '0' }}</div>
            <div class="stat-label">Contribuições</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="profile-tabs">
        <div class="profile-tab active" data-tab="sobre">Sobre</div>
        <div class="profile-tab" data-tab="projetos">Projetos</div>
        <div class="profile-tab" data-tab="certificados">Certificados</div>
        <div class="profile-tab" data-tab="repositorios">Repositórios</div>
    </div>

    <!-- Tab Content -->
    <div id="sobre" class="tab-content active">
        @include('sobre')
    </div>
    
    <div id="projetos" class="tab-content">
        @include('projetos')
    </div>
    
    <div id="certificados" class="tab-content">
        @include('certificados')
    </div>
    
    <div id="repositorios" class="tab-content">
        @include('repositorios')
    </div>
</div>

<style>
.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
}

.connection-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
}

.btn {
    padding: 8px 16px;
    border-radius: 20px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn i {
    font-size: 14px;
}

.btn-connect {
    background-color: #0a66c2;
    color: white;
}

.btn-connect:hover {
    background-color: #004182;
}

.btn-pending {
    background-color: #ffd700;
    color: #000;
}

.btn-connected {
    background-color: #28a745;
    color: white;
}

.btn-rejected {
    background-color: #dc3545;
    color: white;
}

.btn-share {
    background-color: #f8f9fa;
    color: #0a66c2;
    border: 1px solid #0a66c2;
}

.btn-share:hover {
    background-color: #e8f0fe;
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>

<script>
function compartilharPerfil() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function() {
        alert('Link do perfil copiado!');
    }, function() {
        alert('Não foi possível copiar o link.');
    });
}
</script>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gerenciamento das abas do perfil
        const tabs = document.querySelectorAll('.profile-tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove a classe active de todas as abas
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(tc => tc.classList.remove('active'));
                
                // Adiciona a classe active na aba clicada
                this.classList.add('active');
                
                // Ativa o conteúdo correspondente
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    });
</script>
@endsection

</body>
</html>