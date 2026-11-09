@extends('layouts.app')
@section('titulo', 'Empresas')

@section('conteudo')
    <div class="page-head">
        <div>
            <h1>Empresas</h1>
            <p>Gestão das empresas do sistema (super administrador)</p>
        </div>
        <button class="btn btn-primary" data-abre="modalNovaEmpresa">+ Nova empresa</button>
    </div>

    <div class="ajuda">
        <span class="ic">&#128161;</span>
        <div>Cada empresa é um ambiente isolado: seus quadros, usuários e logs não se misturam com os das outras.
            Use <strong>trocar</strong> no topo para alternar entre elas.</div>
    </div>

    <div class="tabela-wrap">
        <table class="tabela">
            <thead>
                <tr><th>Empresa</th><th>Usuários</th><th>Quadros</th><th>Situação</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($empresas as $empresa)
                    <tr>
                        <td>
                            <div class="flex">
                                <span class="ini" style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;background: {{ $empresa->cor }}">
                                    {{ strtoupper(mb_substr($empresa->nome,0,1)) }}
                                </span>
                                <strong>{{ $empresa->nome }}</strong>
                            </div>
                        </td>
                        <td class="muted">{{ $empresa->users_count }}</td>
                        <td class="muted">{{ $empresa->boards_count }}</td>
                        <td>
                            @if($empresa->ativo)
                                <span class="badge badge-verde">Ativa</span>
                            @else
                                <span class="badge badge-vermelho">Inativa</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            @php($dadosEmpresa = $empresa->only(['id', 'nome', 'cor', 'ativo']))
                            <button class="btn btn-light btn-sm"
                                onclick='editarEmpresa(@json($dadosEmpresa))'>Editar</button>
                            <form method="POST" action="{{ route('empresas.destroy', $empresa) }}" style="display:inline"
                                  onsubmit="return confirm('Excluir a empresa {{ $empresa->nome }} e TODOS os seus dados?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal: nova empresa --}}
    <div class="modal-bg" id="modalNovaEmpresa">
        <div class="modal" style="max-width:420px">
            <div class="modal-head"><h3>Nova empresa</h3><button class="x" onclick="fecharModal(this)">&times;</button></div>
            <form method="POST" action="{{ route('empresas.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="campo"><label>Nome *</label><input type="text" name="nome" required placeholder="Ex.: ASJ Logística"></div>
                    <div class="campo"><label>Cor de identificação</label><input type="color" name="cor" value="#2563eb" style="height:42px;padding:4px"></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: editar empresa --}}
    <div class="modal-bg" id="modalEditarEmpresa">
        <div class="modal" style="max-width:420px">
            <div class="modal-head"><h3>Editar empresa</h3><button class="x" onclick="fecharModal(this)">&times;</button></div>
            <form method="POST" id="formEditarEmpresa">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="campo"><label>Nome *</label><input type="text" name="nome" id="ede_nome" required></div>
                    <div class="campo"><label>Cor</label><input type="color" name="cor" id="ede_cor" style="height:42px;padding:4px"></div>
                    <label class="flex" style="cursor:pointer"><input type="checkbox" name="ativo" id="ede_ativo" value="1" style="width:auto"> Empresa ativa</label>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function editarEmpresa(e) {
        const f = document.getElementById('formEditarEmpresa');
        f.action = "/empresas/" + e.id;
        document.getElementById('ede_nome').value = e.nome;
        document.getElementById('ede_cor').value = e.cor;
        document.getElementById('ede_ativo').checked = !!e.ativo;
        abrirModal('modalEditarEmpresa');
    }
</script>
@endpush
