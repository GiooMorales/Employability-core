<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="activity-feed">
    @forelse($atividades ?? [] as $atividade)
        <div class="activity-item">
            <div class="activity-icon">
                <i class="fas {{ $atividade->icone }}"></i>
            </div>
            <div class="activity-content">
                <div class="activity-header">
                    <span class="activity-text">{{ $atividade->descricao }}</span>
                </div>
                <div class="activity-time">{{ $atividade->created_at->diffForHumans() }}</div>
                @if($atividade->detalhes)
                    <div class="activity-details">
                        {{ $atividade->detalhes }}
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p>Nenhuma atividade registrada.</p>
    @endforelse
</div>
</body>
</html>
