{{-- ===== Modal: cartão (novo/editar) ===== --}}
<div class="modal-bg" id="modalCard">
    <div class="modal">
        <div class="modal-head">
            <h3 id="cardTituloModal">Novo cartão</h3>
            <button class="x" onclick="fecharModal(this)">&times;</button>
        </div>
        <form method="POST" id="formCard">
            @csrf
            <input type="hidden" name="_method" id="cardMethod" value="POST">
            <div class="modal-body">
                <div class="campo">
                    <label>Título *</label>
                    <input type="text" name="titulo" required placeholder="O que precisa ser feito?">
                </div>
                <div class="campo">
                    <label>Descrição</label>
                    <textarea name="descricao" placeholder="Detalhes (opcional)"></textarea>
                </div>
                <div class="form-row">
                    <div class="campo">
                        <label>Responsável</label>
                        <select name="responsavel_id">
                            <option value="">— ninguém —</option>
                            @foreach($membros as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="campo">
                        <label>Prazo</label>
                        <input type="date" name="prazo">
                    </div>
                </div>
                <div class="campo">
                    <label>Etiqueta</label>
                    <select name="etiqueta">
                        <option value="">— sem etiqueta —</option>
                        <option value="#ef4444">🔴 Urgente</option>
                        <option value="#f59e0b">🟠 Importante</option>
                        <option value="#eab308">🟡 Atenção</option>
                        <option value="#16a34a">🟢 Tranquilo</option>
                        <option value="#2563eb">🔵 Informativo</option>
                        <option value="#8b5cf6">🟣 Revisão</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-danger" id="cardExcluir" style="display:none;margin-right:auto"
                        onclick="if(confirm('Excluir este cartão?')) document.getElementById('formExcluirCard').submit()">Excluir</button>
                <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
        <form method="POST" id="formExcluirCard" style="display:none">
            @csrf @method('DELETE')
        </form>
    </div>
</div>

{{-- ===== Modal: coluna (nova/renomear) ===== --}}
<div class="modal-bg" id="modalColuna">
    <div class="modal" style="max-width:380px">
        <div class="modal-head">
            <h3 id="colTituloModal">Nova coluna</h3>
            <button class="x" onclick="fecharModal(this)">&times;</button>
        </div>
        <form method="POST" id="formColuna">
            @csrf
            <input type="hidden" name="_method" id="colMethod" value="POST">
            <div class="modal-body">
                <div class="campo">
                    <label>Nome da coluna *</label>
                    <input type="text" name="titulo" required placeholder="Ex.: Em revisão">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

{{-- Form oculto para excluir coluna --}}
<form method="POST" id="formExcluirColuna" style="display:none">
    @csrf @method('DELETE')
</form>

{{-- ===== Modal: editar quadro ===== --}}
<div class="modal-bg" id="modalEditarBoard">
    <div class="modal">
        <div class="modal-head">
            <h3>Editar quadro</h3>
            <button class="x" onclick="fecharModal(this)">&times;</button>
        </div>
        <form method="POST" action="{{ route('boards.update', $board) }}">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="campo">
                    <label>Título *</label>
                    <input type="text" name="titulo" value="{{ $board->titulo }}" required>
                </div>
                <div class="campo">
                    <label>Descrição</label>
                    <textarea name="descricao">{{ $board->descricao }}</textarea>
                </div>
                <div class="campo">
                    <label>Cor</label>
                    <input type="color" name="cor" value="{{ $board->cor }}" style="height:42px;padding:4px">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-danger" style="margin-right:auto"
                        onclick="if(confirm('Excluir o quadro inteiro?')) document.getElementById('formExcluirBoard').submit()">Excluir quadro</button>
                <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
        <form method="POST" id="formExcluirBoard" action="{{ route('boards.destroy', $board) }}" style="display:none">
            @csrf @method('DELETE')
        </form>
    </div>
</div>
