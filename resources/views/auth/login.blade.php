<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar — Gerenciador de Tarefas</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-wrap">
    {{-- Painel esquerdo (marketing) --}}
    <div class="login-side">
        <div class="ls-top">
            <span class="logo">T</span> GerenciadorTarefas
        </div>
        <div class="ls-mid">
            <h2>Organize suas tarefas com clareza e controle.</h2>
            <p>Quadros no estilo Kanban, múltiplas empresas isoladas, controle de acesso e histórico completo de atividades — tudo em um só lugar.</p>
        </div>
        <div class="ls-feat">
            <div><span class="ck">&#10003;</span> Quadros, colunas e cartões arrastáveis</div>
            <div><span class="ck">&#10003;</span> Separação total por empresa</div>
            <div><span class="ck">&#10003;</span> Logs de auditoria de cada ação</div>
        </div>
    </div>

    {{-- Formulário --}}
    <div class="login-main">
        <div class="login-box">
            <div class="marca">
                <div class="logo">T</div>
                <h1>Bem-vindo de volta</h1>
                <p class="sub">Entre com suas credenciais para continuar</p>
            </div>

            @if(session('status'))
                <div class="flash flash-ok">&#10003; {{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="flash flash-erro">&#9888; {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="campo">
                    <label>E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="voce@empresa.com">
                </div>
                <div class="campo">
                    <label>Senha</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <div class="login-link">
                    <a href="{{ route('password.request') }}">Esqueci minha senha</a>
                </div>
                <label class="flex" style="font-size:13px; margin-bottom:18px; cursor:pointer">
                    <input type="checkbox" name="remember" style="width:auto"> Manter conectado
                </label>
                <button type="submit" class="btn btn-primary btn-block">Entrar</button>
            </form>

            <p style="text-align:center; font-size:13px; margin-top:22px">
                Não tem conta? <a href="{{ route('register') }}" style="font-weight:600">Criar agora</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
