{{-- Modal de dicas de uso (abre pelo botão "Dicas") --}}
<div class="modal-bg" id="modalDicas">
    <div class="modal" style="max-width:540px">
        <div class="modal-head">
            <h3>💡 Dicas de uso</h3>
            <button class="x" onclick="fecharModal(this)">&times;</button>
        </div>
        <div class="modal-body">
            <div class="dica-item"><span class="emo">🗂️</span><div><strong>Quadros</strong><br>
                <span class="muted">Crie um quadro para cada contexto (financeiro, projetos, pessoal). Clique em <em>+ Novo quadro</em>.</span></div></div>
            <div class="dica-item"><span class="emo">📊</span><div><strong>Colunas</strong><br>
                <span class="muted">Representam o status. Você pode renomear (✎), excluir (×) ou adicionar novas colunas.</span></div></div>
            <div class="dica-item"><span class="emo">📝</span><div><strong>Cartões</strong><br>
                <span class="muted">Cada cartão é uma tarefa. Clique para editar: descrição, responsável, prazo e etiqueta colorida.</span></div></div>
            <div class="dica-item"><span class="emo">✋</span><div><strong>Arrastar e soltar</strong><br>
                <span class="muted">Segure um cartão e arraste para outra coluna para mudar o status. A posição é salva automaticamente.</span></div></div>
            <div class="dica-item"><span class="emo">✅</span><div><strong>Concluir</strong><br>
                <span class="muted">Clique na bolinha à esquerda do cartão para marcar como concluído (fica verde e riscado).</span></div></div>
            <div class="dica-item"><span class="emo">🔒</span><div><strong>Privacidade</strong><br>
                <span class="muted">Seus quadros são só seus — outros usuários não veem o que você cria.</span></div></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-primary" onclick="fecharModal(this)">Entendi!</button>
        </div>
    </div>
</div>
