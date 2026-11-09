@extends('layouts.app')
@section('titulo', 'Usuários')

@section('conteudo')
    <div class="page-head">
        <div>
            <h1>Usuários</h1>
            <p>Pessoas com acesso à empresa <strong>{{ $empresaAtual->nome }}</strong></p>
        </div>
        <div class="flex">
            <button class="btn btn-light" data-abre="modalVincular">&#128279; Vincular existente</button>
            <button class="btn btn-primary" data-abre="modalNovoUsuario">+ Novo usuário</button>
        </div>
    </div>

    <div class="ajuda">
        <span class="ic">&#128161;</span>
        <div>Os usuários criados aqui são vinculados a esta empresa. <strong>Desvincular</strong> apenas tira o acesso a esta
            empresa (a conta é mantida, pois a pessoa pode atender outras). <strong>Excluir conta</strong> (só super admin)
            apaga de vez e libera o e-mail para novo cadastro.</div>
    </div>

    <div class="tabela-wrap">
        <table class="tabela">
            <thead>
                <tr><th>Nome</th><th>E-mail</th><th>Papel</th><th>Situação</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td>
                            <div class="flex">
                                <span class="avatar" style="width:30px;height:30px;font-size:12px">{{ strtoupper(mb_substr($usuario->name,0,1)) }}</span>
                                <strong>{{ $usuario->name }}</strong>
                            </div>
                        </td>
                        <td class="muted">{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->super_admin)
                                <span class="badge badge-azul">Super Admin</span>
                            @elseif($usuario->papel === 'admin')
                                <span class="badge badge-azul">Administrador</span>
                            @else
                                <span class="badge badge-cinza">Usuário</span>
                            @endif
                        </td>
                        <td>
                            @if($usuario->ativo)
                                <span class="badge badge-verde">Ativo</span>
                            @else
                                <span class="badge badge-vermelho">Inativo</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            @php($dadosUsuario = $usuario->only(['id', 'name', 'email', 'papel', 'ativo']))
                            <button class="btn btn-light btn-sm"
                                onclick='editarUsuario(@json($dadosUsuario))'>Editar</button>
                            @if($usuario->id !== auth()->id())
                                <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" style="display:inline"
                                      onsubmit="return confirm('Desvincular {{ $usuario->name }} desta empresa? A conta NÃO é apagada.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-ghost btn-sm">Desvincular</button>
                                </form>
                                @if(auth()->user()->isSuperAdmin())
                                    <form method="POST" action="{{ route('usuarios.excluir', $usuario) }}" style="display:inline"
                                          onsubmit="return confirm('EXCLUIR PERMANENTEMENTE a conta de {{ $usuario->name }}?\n\nEsta ação não pode ser desfeita e libera o e-mail para novo cadastro.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Excluir conta</button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal: novo usuário --}}
    <div class="modal-bg" id="modalNovoUsuario">
        <div class="modal">
            <div class="modal-head"><h3>Novo usuário</h3><button class="x" onclick="fecharModal(this)">&times;</button></div>
            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="campo"><label>Nome *</label><input type="text" name="name" required></div>
                    <div class="campo"><label>E-mail *</label><input type="email" name="email" required></div>
                    <div class="form-row">
                        <div class="campo"><label>Senha *</label><input type="password" name="password" required placeholder="mín. 6 caracteres"></div>
                        <div class="campo">
                            <label>Papel *</label>
                            <select name="papel" required>
                                <option value="usuario">Usuário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: vincular usuário existente --}}
    <div class="modal-bg" id="modalVincular">
        <div class="modal" style="max-width:420px">
            <div class="modal-head"><h3>Vincular usuário existente</h3><button class="x" onclick="fecharModal(this)">&times;</button></div>
            <form method="POST" action="{{ route('usuarios.vincular') }}">
                @csrf
                <div class="modal-body">
                    <p class="muted" style="font-size:13px;margin-bottom:14px">
                        Dê a um usuário que já existe no sistema acesso também à empresa <strong>{{ $empresaAtual->nome }}</strong>.
                        Útil para quem atende mais de uma empresa.
                    </p>
                    @if($disponiveis->isEmpty())
                        <div class="ajuda" style="margin:0"><span class="ic">&#8505;</span>
                            <div>Todos os usuários do sistema já têm acesso a esta empresa.</div></div>
                    @else
                        <div class="campo">
                            <label>Usuário *</label>
                            <select name="user_id" required>
                                <option value="">Selecione...</option>
                                @foreach($disponiveis as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-light" onclick="fecharModal(this)">Cancelar</button>
                    @if($disponiveis->isNotEmpty())
                        <button type="submit" class="btn btn-primary">Vincular</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: editar usuário --}}
    <div class="modal-bg" id="modalEditarUsuario">
        <div class="modal">
            <div class="modal-head"><h3>Editar usuário</h3><button class="x" onclick="fecharModal(this)">&times;</button></div>
            <form method="POST" id="formEditarUsuario">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="campo"><label>Nome *</label><input type="text" name="name" id="edu_name" required></div>
                    <div class="campo"><label>E-mail *</label><input type="email" name="email" id="edu_email" required></div>
                    <div class="form-row">
                        <div class="campo">
                            <label>Papel *</label>
                            <select name="papel" id="edu_papel" required>
                                <option value="usuario">Usuário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="campo"><label>Nova senha</label><input type="password" name="password" placeholder="deixe em branco p/ manter"></div>
                    </div>
                    <label class="flex" style="cursor:pointer"><input type="checkbox" name="ativo" id="edu_ativo" value="1" style="width:auto"> Usuário ativo</label>
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
    function editarUsuario(u) {
        const f = document.getElementById('formEditarUsuario');
        f.action = "/usuarios/" + u.id;
        document.getElementById('edu_name').value = u.name;
        document.getElementById('edu_email').value = u.email;
        document.getElementById('edu_papel').value = u.papel;
        document.getElementById('edu_ativo').checked = !!u.ativo;
        abrirModal('modalEditarUsuario');
    }
</script>
@endpush
