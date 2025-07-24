<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Conta Suspensa</title>
    <style>
        body { background: #f8f9fa; font-family: Arial, sans-serif; }
        .suspenso-container {
            max-width: 420px; margin: 80px auto; background: #fff; border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10); padding: 36px 32px; text-align: center;
        }
        .suspenso-title { color: #ff9800; font-size: 1.5em; margin-bottom: 18px; }
        .suspenso-motivo { color: #333; font-size: 1.1em; margin-bottom: 16px; }
        .suspenso-ate { color: #007bff; font-size: 1.1em; margin-bottom: 24px; }
        .suspenso-info { color: #888; font-size: 0.95em; }
    </style>
</head>
<body>
    <div class="suspenso-container">
        <div class="suspenso-title">Você foi banido temporariamente</div>
        <div class="suspenso-motivo">
            <strong>Motivo:</strong><br>
            {{ $motivo ? $motivo : 'Não informado.' }}
        </div>
        <div class="suspenso-ate">
            <strong>Você poderá voltar a utilizar o site em:</strong><br>
            {{ $ate }}<br>
            <span style="font-size:0.95em; color:#888;">(Horário de Brasília)</span>
        </div>
        <div class="suspenso-info">
            Caso acredite que isso é um engano, entre em contato com o suporte.
        </div>
    </div>
</body>
</html> 