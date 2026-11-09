<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criar conta — Gerenciador de Tarefas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-wrap">
    <div class="login-side">
        <div class="ls-top"><span class="logo">T</span> GerenciadorTarefas</div>
        <div class="ls-mid">
            <h2>Crie sua conta<br>em segundos.</h2>
            <p>Preencha seus dados para solicitar acesso. Um administrador irá revisar e aprovar seu cadastro.</p>
        </div>
        <div class="ls-feat">
            <div><span class="ck">&#128100;</span> Dados básicos: nome, e-mail e senha</div>
            <div><span class="ck">&#128274;</span> Aprovação por um administrador</div>
        </div>
    </div>

    <div class="login-main">
        <div class="login-box">
            <div class="marca">
                <div class="logo">T</div>
                <h1>Criar conta</h1>
                <p class="sub">Solicite seu acesso ao sistema</p>
            </div>

            @if($errors->any())
                <div class="flash flash-erro">
                    &#9888;
                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="campo">
                    <label>Nome completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Seu nome">
                </div>
                <div class="campo">
                    <label>E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="voce@empresa.com">
                </div>
                <div class="campo">
                    <label>Senha</label>
                    <input type="password" name="password" required placeholder="crie uma senha forte">
                </div>
                <div class="campo">
                    <label>Confirmar senha</label>
                    <input type="password" name="password_confirmation" required placeholder="repita a senha">
                </div>

                <div class="ajuda" style="margin-bottom:18px">
                    <span class="ic">&#128273;</span>
                    <div>A senha precisa ter no mínimo <strong>6 caracteres</strong>, com <strong>maiúscula</strong>,
                        <strong>minúscula</strong>, <strong>número</strong> e <strong>caractere especial</strong> (ex.: Senha@123).</div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Criar conta</button>
            </form>

            <p style="text-align:center; margin-top:20px">
                <a href="{{ route('login') }}" style="font-size:13px; font-weight:600">&larr; Já tenho conta</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
