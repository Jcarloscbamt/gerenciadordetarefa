{{-- Modal: novo quadro --}}
<div class="modal-bg" id="modalNovoBoard">
    <div class="modal">
        <div class="modal-head">
            <h3>Novo quadro</h3>
            <button class="x" onclick="fecharModal(this)">&times;</button>
        </div>
        <form method="POST" action="{{ route('boards.store') }}">
            @csrf
            <div class="modal-body">
                <div class="campo">
                    <label>Título *</label>
                    <input type="text" name="titulo" required placeholder="Ex.: Projeto do cliente X" autofocus>
                </div>
                <div class="campo">
                    <label>Descrição</label>
                    <textarea name="descricao" placeholder="Opcional"></textarea>
                </div>
                <div class="campo">
                    <label>Cor</label>
                    <input type="color" name="cor" value="#2563eb" style="height:42px;padding:4px">
                </div>
                <p class="muted" style="font-size:12.5px">O quadro já vem com as colunas <strong>A Fazer</strong>, <strong>Fazendo</strong> e <strong>Concluído</strong>.</p>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar quadro</button>
            </div>
        </form>
    </div>
</div>
