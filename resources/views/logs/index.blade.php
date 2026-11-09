@extends('layouts.app')
@section('titulo', 'Logs')

@section('conteudo')
    <div class="page-head">
        <div>
            <h1>Logs de atividade</h1>
            <p>Auditoria das ações na empresa <strong>{{ $empresaAtual->nome }}</strong></p>
        </div>
    </div>

    <div class="ajuda">
        <span class="ic">&#128161;</span>
        <div>Cada ação importante (login, criação, edição, exclusão, movimentação de cartões) fica registrada aqui
            com o usuário, o horário e o endereço IP. Use o filtro para encontrar um tipo de ação.</div>
    </div>

    <form method="GET" class="flex" style="margin-bottom:16px">
        <select name="acao" class="campo" style="margin:0;max-width:240px" onchange="this.form.submit()">
            <option value="">Todas as ações</option>
            @foreach($acoes as $a)
                <option value="{{ $a }}" {{ request('acao') === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        @if(request('acao'))
            <a href="{{ route('logs.index') }}" class="btn btn-light btn-sm">Limpar</a>
        @endif
    </form>

    <div class="tabela-wrap">
        <table class="tabela">
            <thead>
                <tr><th>Data/Hora</th><th>Usuário</th><th>Ação</th><th>Descrição</th><th>IP</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="muted" style="white-space:nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? '—' }}</td>
                        <td><span class="badge badge-cinza">{{ $log->acao }}</span></td>
                        <td>{{ $log->descricao }}</td>
                        <td class="muted">{{ $log->ip }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="vazio" style="padding:30px">Nenhum registro ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="flex mt" style="gap:8px">
            @if($logs->onFirstPage())
                <span class="btn btn-light btn-sm" style="opacity:.5">&larr; Anterior</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="btn btn-light btn-sm">&larr; Anterior</a>
            @endif
            <span class="muted" style="font-size:13px">Página {{ $logs->currentPage() }} de {{ $logs->lastPage() }}</span>
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="btn btn-light btn-sm">Próxima &rarr;</a>
            @else
                <span class="btn btn-light btn-sm" style="opacity:.5">Próxima &rarr;</span>
            @endif
        </div>
    @endif
@endsection
