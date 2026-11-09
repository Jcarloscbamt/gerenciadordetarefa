@extends('layouts.app')
@section('titulo', 'Aprovações')

@section('conteudo')
    <div class="page-head">
        <div>
            <h1>Aprovações de cadastro</h1>
            <p>Usuários que se cadastraram e aguardam liberação de acesso</p>
        </div>
    </div>

    <div class="ajuda">
        <span class="ic">&#128161;</span>
        <div>Quem se cadastra pela tela pública nasce <strong>inativo</strong> e não consegue entrar até ser aprovado aqui.
            Ao aprovar, defina o <strong>papel</strong> e a <strong>empresa</strong> de acesso. Rejeitar remove o cadastro.</div>
    </div>

    @if($pendentes->isEmpty())
        <div class="card card-pad vazio">
            <div class="ic">&#9989;</div>
            <p>Nenhum cadastro pendente no momento.</p>
        </div>
    @else
        <div class="grid grid-3">
            @foreach($pendentes as $p)
                <div class="card card-pad">
                    <div class="flex" style="margin-bottom:14px">
                        <span class="avatar" style="width:40px;height:40px;font-size:16px">{{ strtoupper(mb_substr($p->name,0,1)) }}</span>
                        <div>
                            <div style="font-weight:700">{{ $p->name }}</div>
                            <div class="muted" style="font-size:12.5px">{{ $p->email }}</div>
                        </div>
                        <span class="badge badge-vermelho" style="margin-left:auto">Pendente</span>
                    </div>
                    <div class="muted" style="font-size:12px;margin-bottom:10px">
                        Cadastrado em {{ $p->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div style="font-size:12.5px;margin-bottom:14px">
                        Empresa:
                        @forelse($p->empresas as $emp)
                            <span class="badge badge-azul">{{ $emp->nome }}</span>
                        @empty
                            <span class="badge badge-cinza">nenhuma</span>
                        @endforelse
                    </div>

                    {{-- Aprovar: ativa e define papel (empresa já vem da padrão) --}}
                    <form method="POST" action="{{ route('aprovacoes.aprovar', $p) }}">
                        @csrf
                        <div class="form-row">
                            <div class="campo" style="margin-bottom:12px">
                                <label>Papel</label>
                                <select name="papel" required>
                                    <option value="usuario">Usuário</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <div class="campo" style="margin-bottom:12px">
                                <label>Empresa extra (opcional)</label>
                                <select name="empresa_id">
                                    <option value="">— manter a atual —</option>
                                    @foreach($empresas as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm btn-block">&#10003; Aprovar acesso</button>
                    </form>

                    {{-- Rejeitar: remove o cadastro --}}
                    <form method="POST" action="{{ route('aprovacoes.rejeitar', $p) }}" style="margin-top:8px"
                          onsubmit="return confirm('Rejeitar e excluir o cadastro de {{ $p->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-sm btn-block">Rejeitar cadastro</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection
