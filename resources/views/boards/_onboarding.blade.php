{{-- Estado vazio acolhedor (onboarding) — mostrado quando não há quadros --}}
<div class="onboarding">
    <div class="ob-hero">
        <div class="ob-emoji">🗂️</div>
        <h2>Vamos criar seu primeiro quadro!</h2>
        <p>Um quadro organiza suas tarefas em colunas — por padrão <strong>A Fazer</strong>, <strong>Fazendo</strong> e
            <strong>Concluído</strong>. Comece por um modelo abaixo ou crie do zero.</p>
        <button class="btn btn-primary" data-abre="modalNovoBoard">+ Criar quadro do zero</button>
    </div>

    <div class="ob-grid">
        <button class="ob-modelo" onclick="usarModelo('Rotina Financeira')">
            <span class="emo">💰</span>
            <strong>Rotina Financeira</strong>
            <span class="muted">Pagamentos, conciliações, fechamento</span>
        </button>
        <button class="ob-modelo" onclick="usarModelo('Meus Projetos')">
            <span class="emo">🚀</span>
            <strong>Projetos</strong>
            <span class="muted">Acompanhe entregas e prazos</span>
        </button>
        <button class="ob-modelo" onclick="usarModelo('Tarefas Pessoais')">
            <span class="emo">✅</span>
            <strong>Tarefas Pessoais</strong>
            <span class="muted">Organize o seu dia a dia</span>
        </button>
        <button class="ob-modelo" onclick="usarModelo('Atendimentos')">
            <span class="emo">📞</span>
            <strong>Atendimentos</strong>
            <span class="muted">Chamados e solicitações</span>
        </button>
    </div>

    <div class="ob-passos">
        <h3>Como funciona em 3 passos</h3>
        <div class="ob-passo"><span class="n">1</span><div><strong>Crie um quadro</strong><br><span class="muted">Ele já vem com as colunas prontas.</span></div></div>
        <div class="ob-passo"><span class="n">2</span><div><strong>Adicione cartões</strong><br><span class="muted">Cada cartão é uma tarefa, com responsável, prazo e etiqueta.</span></div></div>
        <div class="ob-passo"><span class="n">3</span><div><strong>Arraste entre as colunas</strong><br><span class="muted">Mova o cartão conforme o andamento, ou marque como concluído na bolinha.</span></div></div>
    </div>
</div>

<script>
    // Abre o modal de novo quadro já com um título sugerido
    function usarModelo(titulo) {
        abrirModal('modalNovoBoard');
        const campo = document.querySelector('#modalNovoBoard input[name=titulo]');
        if (campo) { campo.value = titulo; campo.focus(); }
    }
</script>
