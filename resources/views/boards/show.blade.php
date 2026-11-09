@extends('layouts.app')
@section('titulo', $board->titulo)

@section('conteudo')
<div class="board-page" style="--board-cor: {{ $board->cor }}">
    {{-- Cabeçalho do quadro --}}
    <div class="board-topo">
        <a href="{{ route('boards.index') }}" class="voltar" title="Voltar">&larr;</a>
        <span style="width:16px;height:16px;border-radius:5px;background: {{ $board->cor }}"></span>
        <select class="board-switch" onchange="if(this.value) location.href=this.value" title="Trocar de quadro">
            @foreach($boards as $b)
                <option value="{{ route('boards.show', $b) }}" {{ $b->id === $board->id ? 'selected' : '' }}>{{ $b->titulo }}</option>
            @endforeach
        </select>
        <div style="margin-left:auto" class="flex">
            <button class="btn btn-light btn-sm" data-abre="modalEditarBoard">Editar quadro</button>
            <button class="btn btn-light btn-sm" onclick="novaColuna()">+ Coluna</button>
        </div>
    </div>

    @if($board->descricao)
        <p class="muted" style="margin:-6px 0 16px">{{ $board->descricao }}</p>
    @endif

    <div class="ajuda">
        <span class="ic">&#128161;</span>
        <div>Arraste os cartões entre as colunas para mudar o status. Clique em um cartão para editá-lo,
            ou use <strong>+ Adicionar cartão</strong> no rodapé de cada coluna.</div>
    </div>

    {{-- ===== KANBAN ===== --}}
    <div class="kanban" id="kanban">
        @foreach($board->listas as $lista)
            <div class="coluna" data-lista="{{ $lista->id }}">
                <div class="coluna-head">
                    <div class="t">
                        {{ $lista->titulo }}
                        <span class="cont">{{ $lista->cards->count() }}</span>
                    </div>
                    <div class="acoes">
                        <button title="Renomear" onclick='editarColuna({{ $lista->id }}, @js($lista->titulo))'>&#9998;</button>
                        <button title="Excluir coluna" onclick='excluirColuna({{ $lista->id }}, @js($lista->titulo))'>&times;</button>
                    </div>
                </div>

                <div class="cards" data-lista="{{ $lista->id }}">
                    @foreach($lista->cards as $card)
                        <div class="kcard"
                             data-id="{{ $card->id }}"
                             data-titulo="{{ $card->titulo }}"
                             data-descricao="{{ $card->descricao }}"
                             data-responsavel="{{ $card->responsavel_id }}"
                             data-prazo="{{ optional($card->prazo)->format('Y-m-d') }}"
                             data-etiqueta="{{ $card->etiqueta }}"
                             onclick="editarCard(this)">
                            @if($card->etiqueta)
                                <div class="etiqueta" style="background: {{ $card->etiqueta }}"></div>
                            @endif
                            <div class="kcard-top">
                                <button type="button" class="card-check {{ $card->concluido ? 'ok' : '' }}"
                                        title="Marcar como concluído"
                                        onclick="toggleConcluido(event, {{ $card->id }}, this)">{!! $card->concluido ? '&#10003;' : '' !!}</button>
                                <div class="titulo {{ $card->concluido ? 'feito' : '' }}">{{ $card->titulo }}</div>
                            </div>
                            <div class="rodape">
                                @if($card->prazo)
                                    @php($venc = $card->prazo->isPast() && ! $card->concluido)
                                    <span class="mini-badge {{ $venc ? 'prazo-vencido' : '' }}">
                                        &#128197; {{ $card->prazo->format('d/m') }}
                                    </span>
                                @endif
                                @if($card->responsavel)
                                    <span class="resp" title="{{ $card->responsavel->name }}">
                                        {{ strtoupper(mb_substr($card->responsavel->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="add-card">
                    <button onclick="novoCard({{ $lista->id }})">+ Adicionar cartão</button>
                </div>
            </div>
        @endforeach

        <div class="add-coluna">
            <button onclick="novaColuna()">+ Adicionar coluna</button>
        </div>
    </div>

</div>{{-- /board-page --}}

    {{-- ===== MODAIS ===== --}}
    @include('boards._modais', ['board' => $board, 'membros' => $membros])
@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name=csrf-token]').content;

    /* ---------- Marcar/desmarcar concluído (bolinha do cartão) ---------- */
    function toggleConcluido(e, id, btn) {
        e.stopPropagation(); // não abrir o modal de edição
        fetch('/cartoes/' + id + '/concluir', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(r => r.json()).then(d => {
            const titulo = btn.closest('.kcard-top').querySelector('.titulo');
            if (d.concluido) { btn.classList.add('ok'); btn.innerHTML = '&#10003;'; titulo.classList.add('feito'); }
            else { btn.classList.remove('ok'); btn.innerHTML = ''; titulo.classList.remove('feito'); }
        }).catch(() => location.reload());
    }

    /* ---------- Drag & drop dos cartões ---------- */
    document.querySelectorAll('.cards').forEach(function (container) {
        new Sortable(container, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'arrastando',
            onEnd: function (evt) {
                const destino = evt.to;
                const cardId = evt.item.dataset.id;
                const listaId = destino.dataset.lista;
                const ordem = Array.from(destino.querySelectorAll('.kcard')).map(c => c.dataset.id);

                fetch("{{ route('cards.mover') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ card_id: cardId, lista_id: listaId, ordem: ordem })
                }).then(r => { if (!r.ok) location.reload(); });
            }
        });
    });

    /* ---------- Cartão: novo e editar ---------- */
    const mCard = document.getElementById('modalCard');
    const fCard = document.getElementById('formCard');
    function novoCard(listaId) {
        fCard.action = "/colunas/" + listaId + "/cartoes";
        document.getElementById('cardMethod').value = 'POST';
        document.getElementById('cardTituloModal').textContent = 'Novo cartão';
        fCard.titulo.value = '';
        fCard.descricao.value = '';
        fCard.responsavel_id.value = '';
        fCard.prazo.value = '';
        fCard.etiqueta.value = '';
        document.getElementById('cardExcluir').style.display = 'none';
        abrirModal('modalCard');
    }
    function editarCard(el) {
        const id = el.dataset.id;
        fCard.action = "/cartoes/" + id;
        document.getElementById('cardMethod').value = 'PUT';
        document.getElementById('cardTituloModal').textContent = 'Editar cartão';
        fCard.titulo.value = el.dataset.titulo || '';
        fCard.descricao.value = el.dataset.descricao || '';
        fCard.responsavel_id.value = el.dataset.responsavel || '';
        fCard.prazo.value = el.dataset.prazo || '';
        fCard.etiqueta.value = el.dataset.etiqueta || '';
        const fe = document.getElementById('formExcluirCard');
        fe.action = "/cartoes/" + id;
        document.getElementById('cardExcluir').style.display = 'inline-flex';
        abrirModal('modalCard');
    }

    /* ---------- Coluna: nova, renomear, excluir ---------- */
    const fCol = document.getElementById('formColuna');
    function novaColuna() {
        fCol.action = "{{ route('listas.store', $board) }}";
        document.getElementById('colMethod').value = 'POST';
        document.getElementById('colTituloModal').textContent = 'Nova coluna';
        fCol.titulo.value = '';
        abrirModal('modalColuna');
    }
    function editarColuna(id, titulo) {
        fCol.action = "/colunas/" + id;
        document.getElementById('colMethod').value = 'PUT';
        document.getElementById('colTituloModal').textContent = 'Renomear coluna';
        fCol.titulo.value = titulo;
        abrirModal('modalColuna');
    }
    function excluirColuna(id, titulo) {
        if (!confirm('Excluir a coluna "' + titulo + '" e todos os seus cartões?')) return;
        const f = document.getElementById('formExcluirColuna');
        f.action = "/colunas/" + id;
        f.submit();
    }
</script>
@endpush
