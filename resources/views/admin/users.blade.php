<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários - Painel Admin</title>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container-admin {
            max-width: 1100px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(80,0,30,0.10);
            padding: 36px 32px 32px 32px;
        }
        h1 {
            color: #800032;
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 2em;
            text-align: center;
        }
        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 28px;
        }
        .search-form input[type="text"] {
            padding: 8px 14px;
            border-radius: 6px 0 0 6px;
            border: 1px solid #b08ca6;
            font-size: 1em;
            width: 260px;
            outline: none;
        }
        .search-form button {
            padding: 8px 18px;
            border-radius: 0 6px 6px 0;
            border: none;
            background: #800032;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-form button:hover {
            background: #a8324a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            background: #fff;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
        }
        th {
            background: #f3e6eb;
            color: #800032;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
        }
        tr:nth-child(even) { background: #f9f3f6; }
        tr:hover { background: #fbe9f0; }
        td {
            border-bottom: 1px solid #ececec;
            vertical-align: middle;
        }
        .admin-btn {
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            font-size: 1em;
            cursor: pointer;
            margin: 2px 2px;
            transition: background 0.2s, color 0.2s;
        }
        .admin-btn.promover { background: #800032; color: #fff; }
        .admin-btn.promover:hover { background: #a8324a; }
        .admin-btn.remover { background: #ffc107; color: #800032; }
        .admin-btn.remover:hover { background: #e0a800; color: #fff; }
        .admin-btn.banir { background: #a8324a; color: #fff; }
        .admin-btn.banir:hover { background: #800032; }
        .admin-btn.desbanir { background: #43a047; color: #fff; }
        .admin-btn.desbanir:hover { background: #2e7031; }
        .admin-btn.suspender { background: #b08ca6; color: #fff; }
        .admin-btn.suspender:hover { background: #800032; color: #fff; }
        .admin-btn.unsuspender { background: #43a047; color: #fff; }
        .admin-btn.unsuspender:hover { background: #2e7031; }
        .admin-btn.detalhes { background: #f3e6eb; color: #800032; }
        .admin-btn.detalhes:hover { background: #e0b7c2; }
        .status-banido { color: #a8324a; font-weight: bold; }
        .status-suspenso { color: #b08ca6; font-weight: bold; }
        .status-ativo { color: #43a047; }
        .voltar-link {
            display: inline-block;
            margin-top: 18px;
            color: #800032;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .voltar-link:hover { color: #a8324a; text-decoration: underline; }
        @media (max-width: 800px) {
            .container-admin { padding: 12px 2vw; }
            table, th, td { font-size: 0.97em; }
        }
        @media (max-width: 600px) {
            .container-admin { padding: 2px 0; }
            table, th, td { font-size: 0.93em; }
        }
    </style>
</head>
<body>
<div class="container-admin">
    <h1>Lista de Usuários</h1>
    <form method="GET" action="{{ route('admin.users') }}" class="search-form">
        <input type="text" name="q" placeholder="Pesquisar por nome ou email" value="{{ $query ?? '' }}">
        <button type="submit">Pesquisar</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Admin?</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id_usuarios }}</td>
                    <td>{{ $usuario->nome }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->is_admin ? 'Sim' : 'Não' }}</td>
                    <td>
                        @if(!empty($usuario->banido) && $usuario->banido)
                            <span class="status-banido">Banido</span>
                        @elseif(!empty($usuario->suspenso_ate) && $usuario->suspenso_ate > now())
                            <span class="status-suspenso">
                                Suspenso até {{ \Carbon\Carbon::parse($usuario->suspenso_ate)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                            </span>
                        @else
                            <span class="status-ativo">Ativo</span>
                        @endif
                    </td>
                    <td>
                        @if(!$usuario->is_admin)
                            <form action="{{ route('admin.users.promote', $usuario->id_usuarios) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="admin-btn promover">Promover a Admin</button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.demote', $usuario->id_usuarios) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="admin-btn remover">Remover Admin</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.users.ban', $usuario->id_usuarios) }}" method="POST" style="display:inline;" onsubmit="return false;">
                            @csrf
                            <button type="button" class="admin-btn banir" onclick="abrirModalBanir({{ $usuario->id_usuarios }})">Banir</button>
                        </form>
                        @if(!empty($usuario->banido) && $usuario->banido)
                            <form action="{{ route('admin.users.unban', $usuario->id_usuarios) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="admin-btn desbanir">Desbanir</button>
                            </form>
                        @endif
                        @if(!empty($usuario->suspenso_ate) && $usuario->suspenso_ate > now())
                            <form action="{{ route('admin.users.unsuspend', $usuario->id_usuarios) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="admin-btn unsuspender">Tirar da Suspensão</button>
                            </form>
                            <button type="button" class="admin-btn detalhes" onclick="abrirDetalhesSuspensao({{ $usuario->id_usuarios }}, '{{ $usuario->motivo ? addslashes($usuario->motivo) : 'Não informado' }}', '{{ $usuario->updated_at ? \Carbon\Carbon::parse($usuario->updated_at)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') : '-' }}', '{{ \Carbon\Carbon::parse($usuario->suspenso_ate)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}')">Detalhes da Suspensão</button>
                        @else
                            <button type="button" class="admin-btn suspender" onclick="abrirModalSuspender({{ $usuario->id_usuarios }})">Suspender</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('admin.dashboard') }}" class="voltar-link">&larr; Voltar ao painel</a>
</div>
<!-- Modal de Suspensão -->
<style>
#modalSuspender {
    display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.4); z-index: 1000; align-items: center; justify-content: center;
}
#modalSuspender .modal-content {
    background: #fff; padding: 32px 32px 24px 32px; border-radius: 16px;
    min-width: 340px; max-width: 95vw; box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    position: relative;
    display: flex; flex-direction: column;
}
#modalSuspender h3 {
    margin-top: 0; margin-bottom: 18px; font-size: 1.3em; color: #007bff; text-align: center;
}
#modalSuspender table {
    width: 100%; margin-bottom: 18px;
}
#modalSuspender td {
    padding: 6px 8px;
}
#modalSuspender input[type=number], #modalSuspender textarea {
    padding: 6px; border-radius: 6px; border: 1px solid #ccc; width: 100%; box-sizing: border-box;
    font-size: 1em;
}
#modalSuspender textarea { resize: vertical; min-height: 40px; }
#modalSuspender .modal-actions {
    text-align: right; margin-top: 10px;
}
#modalSuspender button {
    padding: 7px 18px; border: none; border-radius: 6px; font-size: 1em; cursor: pointer;
    margin-left: 8px;
}
#modalSuspender button[type=button] { background: #eee; color: #333; }
#modalSuspender button[type=submit] { background: #ff9800; color: #fff; }
#modalSuspender button[type=button]:hover { background: #ddd; }
#modalSuspender button[type=submit]:hover { background: #e68900; }
</style>
<div id="modalSuspender">
    <div class="modal-content">
        <form id="formSuspender" method="POST">
            @csrf
            <h3>Suspender Usuário</h3>
            <table>
                <tr>
                    <td style="width: 80px;">Dias:</td>
                    <td><input type="number" name="dias" min="0" value="0" required></td>
                </tr>
                <tr>
                    <td>Horas:</td>
                    <td><input type="number" name="horas" min="0" max="23" value="0" required></td>
                </tr>
                <tr>
                    <td>Minutos:</td>
                    <td><input type="number" name="minutos" min="0" max="59" value="0" required></td>
                </tr>
                <tr>
                    <td>Motivo:</td>
                    <td><textarea name="motivo" rows="2" placeholder="Motivo da suspensão..."></textarea></td>
                </tr>
            </table>
            <div class="modal-actions">
                <button type="button" onclick="fecharModalSuspender()">Cancelar</button>
                <button type="submit">Confirmar Suspensão</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Detalhes Suspensão -->
<style>
#modalDetalhesSuspensao {
    display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.4); z-index: 1100; align-items: center; justify-content: center;
}
#modalDetalhesSuspensao .modal-content {
    background: #fff; padding: 28px 28px 18px 28px; border-radius: 14px;
    min-width: 320px; max-width: 90vw; box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    position: relative; display: flex; flex-direction: column;
}
#modalDetalhesSuspensao h3 { margin-top: 0; margin-bottom: 14px; color: #007bff; text-align: center; }
#modalDetalhesSuspensao .detalhe { margin-bottom: 10px; color: #333; }
#modalDetalhesSuspensao .detalhe strong { color: #555; }
#modalDetalhesSuspensao .modal-actions { text-align: right; margin-top: 10px; }
#modalDetalhesSuspensao button { padding: 7px 18px; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; background: #eee; color: #333; }
#modalDetalhesSuspensao button:hover { background: #ddd; }
</style>
<div id="modalDetalhesSuspensao">
    <div class="modal-content">
        <h3>Detalhes da Suspensão</h3>
        <div class="detalhe"><strong>Motivo:</strong> <span id="detalheMotivo"></span></div>
        <div class="detalhe"><strong>Início da Suspensão:</strong> <span id="detalheInicio"></span></div>
        <div class="detalhe"><strong>Fim da Suspensão:</strong> <span id="detalheFim"></span></div>
        <div class="modal-actions">
            <button type="button" onclick="fecharDetalhesSuspensao()">Fechar</button>
        </div>
    </div>
</div>
<!-- Modal Banir Usuário -->
<style>
#modalBanir {
    display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.4); z-index: 1200; align-items: center; justify-content: center;
}
#modalBanir .modal-content {
    background: #fff; padding: 28px 28px 18px 28px; border-radius: 14px;
    min-width: 320px; max-width: 90vw; box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    position: relative; display: flex; flex-direction: column;
}
#modalBanir h3 { margin-top: 0; margin-bottom: 14px; color: #d32f2f; text-align: center; }
#modalBanir .modal-actions { text-align: right; margin-top: 10px; }
#modalBanir button { padding: 7px 18px; border: none; border-radius: 6px; font-size: 1em; cursor: pointer; margin-left: 8px; }
#modalBanir button[type=button] { background: #eee; color: #333; }
#modalBanir button[type=submit] { background: #d32f2f; color: #fff; }
#modalBanir button[type=button]:hover { background: #ddd; }
#modalBanir button[type=submit]:hover { background: #b71c1c; }
#modalBanir textarea { padding: 6px; border-radius: 6px; border: 1px solid #ccc; width: 100%; box-sizing: border-box; font-size: 1em; resize: vertical; min-height: 40px; }
</style>
<div id="modalBanir">
    <div class="modal-content">
        <form id="formBanir" method="POST">
            @csrf
            <h3>Banir Usuário</h3>
            <div style="margin-bottom: 16px;">
                <label for="motivoBanir"><strong>Motivo do banimento:</strong></label><br>
                <textarea name="motivo" id="motivoBanir" rows="2" placeholder="Motivo do banimento..." required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="fecharModalBanir()">Cancelar</button>
                <button type="submit">Confirmar Banimento</button>
            </div>
        </form>
    </div>
</div>
<script>
let usuarioIdSuspender = null;
function abrirModalSuspender(id) {
    usuarioIdSuspender = id;
    const modal = document.getElementById('modalSuspender');
    const form = document.getElementById('formSuspender');
    form.action = '/admin/users/' + id + '/suspend';
    form.dias.value = 0;
    form.horas.value = 0;
    form.minutos.value = 0;
    form.motivo.value = '';
    modal.style.display = 'flex';
}
function fecharModalSuspender() {
    document.getElementById('modalSuspender').style.display = 'none';
}
window.onclick = function(event) {
    const modal = document.getElementById('modalSuspender');
    if (event.target === modal) fecharModalSuspender();
}
function abrirDetalhesSuspensao(id, motivo, inicio, fim) {
    document.getElementById('detalheMotivo').innerText = motivo;
    document.getElementById('detalheInicio').innerText = inicio;
    document.getElementById('detalheFim').innerText = fim;
    document.getElementById('modalDetalhesSuspensao').style.display = 'flex';
}
function fecharDetalhesSuspensao() {
    document.getElementById('modalDetalhesSuspensao').style.display = 'none';
}
window.onclick = function(event) {
    const modal = document.getElementById('modalDetalhesSuspensao');
    if (event.target === modal) fecharDetalhesSuspensao();
}
function abrirModalBanir(id) {
    const modal = document.getElementById('modalBanir');
    const form = document.getElementById('formBanir');
    form.action = '/admin/users/' + id + '/ban';
    form.motivo.value = '';
    modal.style.display = 'flex';
}
function fecharModalBanir() {
    document.getElementById('modalBanir').style.display = 'none';
}
window.onclick = function(event) {
    const modalBanir = document.getElementById('modalBanir');
    if (event.target === modalBanir) fecharModalBanir();
    // já existe para outros modais
    const modalDetalhes = document.getElementById('modalDetalhesSuspensao');
    if (event.target === modalDetalhes) fecharDetalhesSuspensao();
    const modalSuspender = document.getElementById('modalSuspender');
    if (event.target === modalSuspender) fecharModalSuspender();
}
</script>
</body>
</html> 