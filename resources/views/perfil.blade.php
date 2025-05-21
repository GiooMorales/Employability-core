<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/css/perfil.css') }}">
    <title>Perfil - {{ $user->nome }}</title>
</head>
<body>
@extends('layouts.app')

@section('title', 'Perfil - ' . $user->nome)

@section('content')
<!-- comentario mto legal -->
<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-cover"></div>
    <div class="profile-main">
        <div class="profile-avatar-container">
            <img src="{{ $user->url_foto ? asset('storage/' . $user->url_foto) : asset('images/default-avatar.png') }}" 
                 alt="{{ $user->nome }}" 
                 class="profile-avatar-large">
        </div>
        <div class="profile-info">
            <div class="profile-name-container">
                <h1 class="profile-name">{{ auth()->user()->nome }}</h1>
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
            </div>
            <h2 class="profile-title">{{ $user->titulo ?? '' }}</h2>
            <div class="profile-meta">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $user->cidade && $user->estado ? $user->cidade . ', ' . $user->estado : 'Localização desconhecida' }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-link"></i>
                    @if($user->link)
                        <a href="{{ $user->link }}" target="_blank" class="profile-link">{{ $user->link }}</a>
                    @else
                        <span class="text-muted">Nenhum link adicionado</span>
                    @endif
                </div>
                <div class="meta-item">
                    <span>{{ $user->profissao ?? 'Sem Profissão' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-stats-bar">
        <div class="stat">
            <div class="stat-number">{{ $estatisticas->conexoes ?? '0' }}</div>
            <div class="stat-label">Conexões</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ $estatisticas->projetos ?? '0' }}</div>
            <div class="stat-label">Projetos</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ $estatisticas->certificados ?? '0' }}</div>
            <div class="stat-label">Certificados</div>
        </div>
        <div class="stat">
            <div class="stat-number">{{ $estatisticas->contribuicoes ?? '0' }}</div>
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