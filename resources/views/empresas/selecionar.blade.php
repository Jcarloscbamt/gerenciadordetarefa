<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selecionar empresa</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="sel-wrap">
    <div class="sel-inner">
        <div class="topo">
            <h1>Olá, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p>Selecione a empresa com que deseja trabalhar. Cada empresa tem seus próprios quadros e dados, totalmente separados.</p>
        </div>

        @if($errors->any())
            <div class="flash flash-erro" style="max-width:420px;margin:0 auto 24px">&#9888; {{ $errors->first() }}</div>
        @endif

        @forelse($empresas as $empresa)
            @php($ini = strtoupper(mb_substr($empresa->nome, 0, 1)))
        @empty
        @endforelse

        @if($empresas->isEmpty())
            <div class="card card-pad vazio" style="max-width:460px;margin:0 auto">
                <div class="ic">&#127970;</div>
                <p>Você ainda não está vinculado a nenhuma empresa.<br>Peça a um administrador para liberar seu acesso.</p>
                <form method="POST" action="{{ route('logout') }}" class="mt">
                    @csrf <button class="btn btn-light">Sair</button>
                </form>
            </div>
        @else
            <div class="empresa-grid">
                @foreach($empresas as $empresa)
                    <form method="POST" action="{{ route('empresas.definir', $empresa) }}">
                        @csrf
                        <button type="submit" class="empresa-opt" style="width:100%;border:1px solid var(--borda);cursor:pointer">
                            <div class="ini" style="background: {{ $empresa->cor }}">
                                {{ strtoupper(mb_substr($empresa->nome, 0, 1)) }}
                            </div>
                            <h3>{{ $empresa->nome }}</h3>
                            <span class="btn btn-light btn-sm">Entrar &rarr;</span>
                        </button>
                    </form>
                @endforeach
            </div>

            <div style="text-align:center" class="mt">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf <button class="btn btn-ghost">Sair do sistema</button>
                </form>
            </div>
        @endif
    </div>
</div>
</body>
</html>
