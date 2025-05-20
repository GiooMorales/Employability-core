<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="projects-grid">
    @forelse($projetos ?? [] as $projeto)
        <div class="project-card">
            <img src="{{ $projeto->imagem ?? '/api/placeholder/300/150' }}" alt="{{ $projeto->titulo }}" class="project-image">
            <div class="project-content">
                <h3 class="project-title">{{ $projeto->titulo }}</h3>
                <p class="project-description">{{ Str::limit($projeto->descricao, 100) }}</p>
                <div class="project-tags">
                    @foreach($projeto->tags as $tag)
                        <span class="project-tag">{{ $tag->nome }}</span>
                    @endforeach
                </div>
                <div class="project-footer">
                    <a href="{{ route('projetos.show', $projeto->id) }}" class="project-link">
                        <i class="fas fa-external-link-alt"></i> Ver projeto
                    </a>
                    <div class="project-stats">
                        <div class="project-stat">
                            <i class="fas fa-eye"></i> {{ $projeto->visualizacoes }}
                        </div>
                        <div class="project-stat">
                            <i class="fas fa-star"></i> {{ $projeto->favoritos }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <p>Você ainda não tem projetos cadastrados.</p>
            <a href="{{ route('projetos.criar') }}" class="profile-action-btn edit-btn">
                <i class="fas fa-plus"></i> Adicionar projeto
            </a>
        </div>
    @endforelse
</div>
</body>
</html>
