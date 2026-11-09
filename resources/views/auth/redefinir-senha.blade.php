<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nova senha — Gerenciador de Tarefas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-wrap">
    <div class="login-side">
        <div class="ls-top"><span class="logo">T</span> GerenciadorTarefas</div>
        <div class="ls-mid">
            <h2>Crie uma nova senha.</h2>
            <p>Escolha uma senha forte, com pelo menos 6 caracteres. Depois é só entrar normalmente.</p>
        </div>
        <div class="ls-feat">
            <div><span class="ck">&#128273;</span> Sua conta, protegida</div>
        </div>
    </div>

    <div class="login-main">
        <div class="login-box">
            <div class="marca">
                <div class="logo">T</div>
                <h1>Definir nova senha</h1>
                <p class="sub">Digite e confirme sua nova senha</p>
            </div>

            @if($errors->any())
                <div class="flash flash-erro">&#9888; {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="campo">
                    <label>E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required readonly>
                </div>
                <div class="campo">
                    <label>Nova senha</label>
                    <input type="password" name="password" required autofocus placeholder="mínimo 6 caracteres">
                </div>
                <div class="campo">
                    <label>Confirmar nova senha</label>
                    <input type="password" name="password_confirmation" required placeholder="repita a senha">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Redefinir senha</button>
            </form>

            <p style="text-align:center; margin-top:20px">
                <a href="{{ route('login') }}" style="font-size:13px; font-weight:600">&larr; Voltar para o login</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
