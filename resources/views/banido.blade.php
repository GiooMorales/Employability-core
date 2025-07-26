<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta Banida - Employability Core</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0; 
            padding: 0; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .banido-container {
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            text-align: center; 
            max-width: 500px; 
            width: 90%;
        }
        .banido-title { 
            color: #dc3545; 
            font-size: 1.8em; 
            margin-bottom: 20px; 
            font-weight: bold;
        }
        .banido-motivo { 
            color: #333; 
            font-size: 1.1em; 
            margin-bottom: 20px; 
            line-height: 1.6;
        }
        .banido-info { 
            color: #666; 
            font-size: 0.95em; 
            line-height: 1.5;
        }
        .banido-icon {
            font-size: 4em;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="banido-container">
        <div class="banido-icon">
            <i class="fas fa-ban"></i>
        </div>
        <div class="banido-title">Conta Banida Permanentemente</div>
        @if(!empty($motivo))
            <div class="banido-motivo">
                <strong>Motivo:</strong> {{ $motivo }}
            </div>
        @endif
        <div class="banido-info">
            Sua conta foi banida permanentemente por violação dos termos de uso da plataforma.<br>
            Esta decisão é irrevogável e não pode ser contestada.
        </div>
    </div>
</body>
</html> 