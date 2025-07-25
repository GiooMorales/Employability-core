<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <label for="email">E-mail</label>
            <input type="email" name="email" placeholder="Seu e-mail" required value="{{ old('email') }}">

            <label for="userpassword">Senha</label>
            <input type="password" name="senha" placeholder="Sua senha" required>

            <button type="submit">Entrar</button>
            <p>Não tem conta? <a href="{{ ('registrar') }}">Registrar</a></p>
        </form>
    </div>
</body>
</html>
