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
        <div class="about-item-content" style="white-space: pre-line;">
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
        @forelse($experiencias ?? [] as $exp)
            <div class="experience-item" style="background: #f8f9fa; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.04); padding: 18px 20px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: bold; font-size: 18px; color: #0a66c2;">{{ $exp->empresa_nome }}</div>
                        <div style="font-size: 16px; color: #333;">{{ $exp->cargo }}</div>
                        <div style="font-size: 14px; color: #666; margin-top: 2px;">
                            {{ $exp->data_inicio ? $exp->data_inicio->format('M/Y') : '' }} -
                            {{ $exp->atual ? 'Presente' : ($exp->data_fim ? $exp->data_fim->format('M/Y') : '---') }}
                        </div>
                        <div style="margin-top: 8px; color: #444;"><strong>Descrição:</strong> {{ $exp->descricao }}</div>
                        @if($exp->conquistas)
                        <div style="margin-top: 8px; color: #444;"><strong>Conquistas:</strong> {{ $exp->conquistas }}</div>
                        @endif
                        <div style="margin-top: 8px;">
                            @if($exp->tipo)
                                <span style="background: #e0e7ef; color: #0a66c2; border-radius: 12px; padding: 4px 12px; font-size: 13px; margin-right: 5px;">{{ $exp->tipo }}</span>
                            @endif
                            @if($exp->modalidade)
                                <span style="background: #e0e7ef; color: #0a66c2; border-radius: 12px; padding: 4px 12px; font-size: 13px;">{{ $exp->modalidade }}</span>
                            @endif
                        </div>
                    </div>
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
