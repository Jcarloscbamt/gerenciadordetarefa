<!DOCTYPE html>
<html lang="pt-BR" data-tema="{{ auth()->user()->tema ?? 'claro' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Gerenciador de Tarefas')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('head')
</head>
<body>
@php($u = auth()->user())

{{-- ===== SIDEBAR (escura, altura total) ===== --}}
<div class="sidebar-bg" id="sidebarBg" onclick="toggleSidebar()"></div>
<nav class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-logo">
        <span class="logo">T</span> Gerenciador
    </a>

    <div class="sidebar-sec">Principal</div>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'ativo' : '' }}">
        <span class="ic">&#9632;</span> Dashboard
    </a>
    <a href="{{ route('boards.index') }}" class="{{ request()->routeIs('boards.*') ? 'ativo' : '' }}">
        <span class="ic">&#9783;</span> Quadros
    </a>

    @if($u->isAdmin())
        <div class="sidebar-sec">Administração</div>
        <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'ativo' : '' }}">
            <span class="ic">&#128100;</span> Usuários
        </a>
        <a href="{{ route('logs.index') }}" class="{{ request()->routeIs('logs.*') ? 'ativo' : '' }}">
            <span class="ic">&#9781;</span> Logs
        </a>
    @endif

    @if($u->isSuperAdmin())
        <div class="sidebar-sec">Super Admin</div>
        <a href="{{ route('empresas.index') }}" class="{{ request()->routeIs('empresas.index') ? 'ativo' : '' }}">
            <span class="ic">&#127970;</span> Empresas
        </a>
    @endif
</nav>

{{-- ===== TOPBAR ===== --}}
<header class="topbar">
    <button class="menu-toggle" onclick="toggleSidebar()" title="Recolher menu">&#9776;</button>

    @isset($empresaAtual)
        <span class="empresa-chip">
            <span class="dot" style="background: {{ $empresaAtual->cor }}"></span>
            <span class="nome-emp">{{ $empresaAtual->nome }}</span>
            <a href="{{ route('empresas.selecionar') }}" title="Trocar de empresa">trocar</a>
        </span>
    @endisset

    <div class="topbar-right">
        <form method="POST" action="{{ route('tema.salvar') }}" id="formTema">
            @csrf
            <select name="tema" class="tema-select" title="Tema visual"
                    onchange="document.getElementById('formTema').submit()">
                <option value="claro"  {{ $u->tema === 'claro'  ? 'selected' : '' }}>🎨 Clássico</option>
                <option value="escuro" {{ $u->tema === 'escuro' ? 'selected' : '' }}>🌙 Escuro</option>
                <option value="oceano" {{ $u->tema === 'oceano' ? 'selected' : '' }}>🌊 Oceano</option>
            </select>
        </form>
        <div class="user-box">
            <div style="text-align:right">
                <div class="nome">{{ $u->name }}</div>
                <div class="papel">
                    {{ $u->isSuperAdmin() ? 'Super Admin' : ($u->isAdmin() ? 'Administrador' : 'Usuário') }}
                </div>
            </div>
            <div class="avatar">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light btn-sm" type="submit">Sair</button>
        </form>
    </div>
</header>

{{-- ===== CONTEÚDO ===== --}}
<main class="conteudo">
    @include('partials.flash')
    @yield('conteudo')
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.6/Sortable.min.js"></script>
<script>
    // Sidebar: no desktop recolhe/expande (com memória); no mobile abre a gaveta
    function toggleSidebar() {
        if (window.innerWidth <= 860) {
            document.getElementById('sidebar').classList.toggle('aberta');
            document.getElementById('sidebarBg').classList.toggle('ativo');
        } else {
            document.body.classList.toggle('sidebar-recolhida');
            try { localStorage.setItem('sidebar_recolhida', document.body.classList.contains('sidebar-recolhida')); } catch (e) {}
        }
    }
    // Restaura o estado recolhido ao carregar (apenas desktop)
    try {
        if (localStorage.getItem('sidebar_recolhida') === 'true' && window.innerWidth > 860) {
            document.body.classList.add('sidebar-recolhida');
        }
    } catch (e) {}
    // Modais genéricos: data-abre="idModal" abre; .x ou fundo fecha
    function abrirModal(id) { document.getElementById(id).classList.add('aberto'); }
    function fecharModal(el) {
        const m = el.closest ? el.closest('.modal-bg') : document.getElementById(el);
        if (m) m.classList.remove('aberto');
    }
    document.addEventListener('click', function (e) {
        const t = e.target.closest('[data-abre]');
        if (t) { abrirModal(t.dataset.abre); }
        if (e.target.classList.contains('modal-bg')) { e.target.classList.remove('aberto'); }
    });
</script>
@stack('scripts')
</body>
</html>
