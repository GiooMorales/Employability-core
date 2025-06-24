<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="grid-cols-1">
    <div class="about-section">
        <div class="section-title">
            <span>Sobre mim</span>
        </div>
        <div class="about-item-content">
            {{ $usuario->sobre ?? 'Adicione uma descrição sobre você.' }}
        </div>
    </div>

    <div class="about-section">
        <div class="section-title">
            <span>Habilidades</span>
        </div>
        <div class="skills-container">
            @forelse($habilidades ?? [] as $habilidade)
                @php
                    $softSkills = ['Comunicação', 'Trabalho em Equipe', 'Liderança', 'Resolução de Problemas', 'Gestão de Tempo', 'Adaptabilidade', 'Criatividade', 'Pensamento Crítico'];
                    $isSoftSkill = in_array($habilidade->nome, $softSkills);
                @endphp
                <div class="skill-tag">
                    {{ $habilidade->nome }}
                    @if(!$isSoftSkill)
                        <span class="skill-level">{{ $habilidade->nivel }}</span>
                    @endif
                </div>
            @empty
                <p>Nenhuma habilidade cadastrada.</p>
            @endforelse
        </div>
    </div>

    <div class="about-section">
        <div class="section-title">
            <span>Experiência</span>
        </div>
        @forelse($experiencias ?? [] as $experiencia)
            <div class="experience-item">
                <div class="experience-header">
                    <img src="{{ $experiencia->logo ?? '/api/placeholder/32/32' }}" alt="{{ $experiencia->empresa }}" class="experience-logo">
                    <div>
                        <div class="experience-company">{{ $experiencia->empresa }}</div>
                        <div class="experience-position">{{ $experiencia->cargo }}</div>
                        <div class="experience-period">{{ $experiencia->data_inicio->format('M Y') }} - {{ $experiencia->data_fim ? $experiencia->data_fim->format('M Y') : 'Presente' }}</div>
                    </div>
                </div>
                <div class="experience-description">
                    {{ $experiencia->descricao }}
                </div>
            </div>
        @empty
            <p>Nenhuma experiência cadastrada.</p>
        @endforelse
    </div>

    <div class="about-section">
        <div class="section-title">
            <span>Formação</span>
        </div>
        @forelse($formacoes ?? [] as $formacao)
            <div class="education-item">
                <img src="{{ $formacao->logo ?? '/api/placeholder/48/48' }}" alt="{{ $formacao->instituicao }}" class="education-logo">
                <div class="education-info">
                    <div class="education-school">{{ $formacao->instituicao }}</div>
                    <div class="education-degree">{{ $formacao->curso }}</div>
                    <div class="education-period">{{ $formacao->data_inicio->format('Y') }} - {{ $formacao->data_fim ? $formacao->data_fim->format('Y') : 'Presente' }}</div>
                </div>
            </div>
        @empty
            <p>Nenhuma formação cadastrada.</p>
        @endforelse
    </div>
</div>
</body>
</html>
