@extends('layouts.app')
@section('titulo', 'Quadros')

@section('conteudo')
    <div class="page-head">
        <div>
            <h1>Quadros</h1>
            <p>Quadros da empresa <strong>{{ $empresaAtual->nome }}</strong></p>
        </div>
        <div class="flex">
            <button class="btn btn-light" data-abre="modalDicas">💡 Dicas</button>
            <button class="btn btn-primary" data-abre="modalNovoBoard">+ Novo quadro</button>
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
