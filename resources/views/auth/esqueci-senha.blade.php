<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar senha — Gerenciador de Tarefas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-wrap">
    <div class="login-side">
        <div class="ls-top"><span class="logo">T</span> GerenciadorTarefas</div>
        <div class="ls-mid">
            <h2>Esqueceu a senha?<br>Sem problema.</h2>
            <p>Informe seu e-mail e enviaremos um link para você criar uma nova senha com segurança.</p>
        </div>
        <div class="ls-feat">
            <div><span class="ck">&#128274;</span> Link seguro e temporário</div>
            <div><span class="ck">&#9203;</span> Expira em 60 minutos</div>
        </div>
    </div>

    <div class="login-main">
        <div class="login-box">
            <div class="marca">
                <div class="logo">T</div>
                <h1>Recuperar senha</h1>
                <p class="sub">Enviaremos um link de redefinição para seu e-mail</p>
            </div>

            @if(session('status'))
                <div class="flash flash-ok">&#10003; {{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="flash flash-erro">&#9888; {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="campo">
                    <label>E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="voce@empresa.com">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Enviar link de recuperação</button>
            </form>

            <p style="text-align:center; margin-top:20px">
                <a href="{{ route('login') }}" style="font-size:13px; font-weight:600">&larr; Voltar para o login</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
