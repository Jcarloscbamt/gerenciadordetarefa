@extends('layouts.app')
@section('titulo', 'Dashboard')

@section('conteudo')
    <div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p>Visão geral da empresa <strong>{{ $empresaAtual->nome }}</strong></p>
        </div>
        <a href="{{ route('boards.index') }}" class="btn btn-primary">Ver quadros</a>
    </div>

    <div class="ajuda">
        <span class="ic">&#128161;</span>
        <div>Este é o seu painel. Os números abaixo e os quadros mostram apenas os dados da empresa selecionada no topo.
            Para mudar de empresa, clique em <strong>trocar</strong> ao lado do nome dela.</div>
    </div>

    {{-- Estatísticas --}}
    <div class="grid grid-stats" style="margin-bottom:26px">
        <div class="stat">
            <div class="ic" style="background:var(--azul-claro);color:var(--azul)">&#9783;</div>
            <div class="num">{{ $totalBoards }}</div>
            <div class="lbl">Quadros</div>
        </div>
        <div class="stat">
            <div class="ic" style="background:#fef3c7;color:#d97706">&#9776;</div>
            <div class="num">{{ $totalCards }}</div>
            <div class="lbl">Tarefas no total</div>
        </div>
        <div class="stat">
            <div class="ic" style="background:#dcfce7;color:var(--verde)">&#10003;</div>
            <div class="num">{{ $cardsConcluidos }}</div>
            <div class="lbl">Concluídas</div>
        </div>
    </div>

    <div class="flex-between" style="margin-bottom:14px">
        <h2 style="font-size:17px;font-weight:700">Seus quadros</h2>
        <div class="flex">
            <button class="btn btn-light btn-sm" data-abre="modalDicas">💡 Dicas</button>
            <button class="btn btn-light btn-sm" data-abre="modalNovoBoard">+ Novo quadro</button>
        </div>
    </div>

    @if($boards->isEmpty())
        @include('boards._onboarding')
    @else
        <div class="grid grid-3">
            @foreach($boards as $board)
                <a href="{{ route('boards.show', $board) }}" class="board-card">
                    <div class="faixa" style="background: {{ $board->cor }}"></div>
                    <div class="corpo">
                        <h3>{{ $board->titulo }}</h3>
                        @if($board->descricao)<div class="desc">{{ $board->descricao }}</div>@endif
                        <div class="meta">{{ $board->listas_count }} colunas</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @include('boards._modal_novo')
    @include('boards._dicas_modal')
@endsection
