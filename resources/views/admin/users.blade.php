<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Usuários - Painel Admin</title>
</head>
<body>
    <h1>Lista de Usuários</h1>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Admin?</th>
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
                        @if(!$usuario->is_admin)
                            <form action="{{ route('admin.users.promote', $usuario->id_usuarios) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">Promover a Admin</button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.demote', $usuario->id_usuarios) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit">Remover Admin</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('admin.dashboard') }}">Voltar ao painel</a>
</body>
</html> 