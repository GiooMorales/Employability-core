<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <div class="form-container">
        <h2>Registrar Conta</h2>

        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registrar.store') }}">

            @csrf
            <label>Nome</label>
            <input type="text" name="username" placeholder="Seu nome" value="{{ old('username') }}" required>

            <label>E-mail</label>
            <input type="email" name="email" placeholder="Seu e-mail" value="{{ old('email') }}" required>

            <label>Senha</label>
            <input type="password" name="userpassword" placeholder="Sua senha" required>

            <button type="submit">Registrar</button>
            <p>Já tem uma conta? <a href="{{ route('login.page') }}">Fazer login</a></p>
        </form>
    </div>
</body>
</html>
