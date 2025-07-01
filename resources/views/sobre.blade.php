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
            {{ $usuario->bio ?? 'Adicione uma descrição sobre você.' }}
        </div>
    </div>

    <div class="about-section">
        <div class="section-title">
            <span>Habilidades</span>
        </div>
        <div class="skills-container">
            @forelse($habilidades ?? [] as $habilidade)
                <div class="skill-tag">
                    {{ $habilidade->nome }}
                    <span class="skill-level">{{ $habilidade->nivel }}</span>
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
            <div class="experience-item" style="margin-bottom: 25px; background: #f8f9fa; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); padding: 18px 20px;">
                <div class="experience-header" style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="flex:1;">
                        <div class="experience-company" style="font-weight: bold; font-size: 18px; color: #0a66c2;">{{ $experiencia->empresa_nome }}</div>
                        <div class="experience-position" style="font-size: 16px; color: #333;">{{ $experiencia->cargo }}</div>
                        <div class="experience-period" style="font-size: 14px; color: #666; margin-top: 2px;">
                            {{ $experiencia->data_inicio ? $experiencia->data_inicio->format('d/m/Y') : '' }}
                            -
                            {{ $experiencia->atual ?? false ? 'Presente' : ($experiencia->data_fim ? $experiencia->data_fim->format('d/m/Y') : '---') }}
                        </div>
                    </div>
                    <div style="margin-left: 20px; text-align: right;">
                        @if(!empty($experiencia->tipo))
                            <span style="display: inline-block; background: #e0e7ef; color: #0a66c2; border-radius: 12px; padding: 4px 12px; font-size: 13px; margin-bottom: 4px;">{{ ucfirst($experiencia->tipo) }}</span><br>
                        @endif
                        @if(!empty($experiencia->modalidade))
                            <span style="display: inline-block; background: #e0e7ef; color: #0a66c2; border-radius: 12px; padding: 4px 12px; font-size: 13px;">{{ ucfirst($experiencia->modalidade) }}</span>
                        @endif
                    </div>
                </div>
                <div class="experience-description" style="margin-top: 10px; color: #444;">
                    <strong>Descrição:</strong> {{ $experiencia->descricao }}
                </div>
                @if(!empty($experiencia->conquistas))
                <div class="experience-achievements" style="margin-top: 8px; color: #444;">
                    <strong>Conquistas:</strong> {{ $experiencia->conquistas }}
                </div>
                @endif
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
            <div class="education-item" style="margin-bottom: 22px; background: #f8f9fa; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); padding: 18px 20px; display: flex; align-items: flex-start;">
                <div class="education-info" style="flex:1;">
                    <div class="education-school" style="font-weight: bold; font-size: 18px; color: #0a66c2;">{{ $formacao->instituicao }}</div>
                    <div class="education-degree" style="font-size: 16px; color: #333; margin-bottom: 2px;">{{ $formacao->curso }}</div>
                    @if($formacao->nivel || $formacao->situacao)
                        <div class="education-extra" style="font-size: 14px; color: #666; margin-bottom: 2px;">
                            <strong>Nível:</strong> {{ $formacao->nivel ?? '-' }} |
                            <strong>Situação:</strong> {{ $formacao->situacao ?? '-' }}
                        </div>
                    @endif
                    <div class="education-period" style="font-size: 14px; color: #666;">
                        {{ $formacao->data_inicio ? $formacao->data_inicio->format('d/m/Y') : '' }}
                        -
                        {{ $formacao->data_fim ? $formacao->data_fim->format('d/m/Y') : 'Presente' }}
                    </div>
                </div>
            </div>
        @empty
            <p>Nenhuma formação cadastrada.</p>
        @endforelse
    </div>
</div>
</body>
</html>
