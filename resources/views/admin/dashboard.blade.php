<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin</title>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #800032;
            margin-top: 60px;
            margin-bottom: 18px;
            font-size: 2.2em;
            font-weight: 700;
            text-align: center;
        }
        p {
            color: #a8324a;
            font-size: 1.1em;
            margin-bottom: 36px;
            text-align: center;
        }
        .stats-grid {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }
        .stat-card {
            background: #f3e6eb;
            border-radius: 12px;
            padding: 48px 70px;
            min-width: 220px;
            box-shadow: 0 2px 8px rgba(80,0,30,0.07);
            cursor: pointer;
            transition: box-shadow 0.2s, background 0.2s;
            text-align: center;
            border: 2px solid #b08ca6;
        }
        .stat-card:hover {
            box-shadow: 0 4px 16px rgba(80,0,30,0.15);
            background: #e0b7c2;
        }
        .stat-title {
            font-size: 1.25em;
            color: #800032;
            margin-bottom: 14px;
            font-weight: 600;
        }
        .stat-value {
            font-size: 2.7em;
            color: #a8324a;
            font-weight: bold;
        }
        @media (max-width: 700px) {
            .stat-card { padding: 32px 10vw; min-width: 120px; }
        }
    </style>
</head>
<body>
    <h1>Bem-vindo ao Painel de Administração!</h1>
    <p>Somente administradores podem ver esta página.</p>
    <div class="stats-grid">
        <div class="stat-card" onclick="window.location.href='{{ route('admin.users') }}'">
            <div class="stat-title">Total de Usuários</div>
            <div class="stat-value">{{ $totalUsuarios }}</div>
        </div>
    </div>
</body>
</html> 