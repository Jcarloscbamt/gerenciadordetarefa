<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Empresa;
use App\Models\User;
use App\Notifications\CadastroAprovado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Aprovação de autocadastros — acessível somente ao super administrador.
 * Lista usuários inativos (que se cadastraram sozinhos) e permite
 * aprová-los (ativar + definir papel + vincular a uma empresa) ou rejeitar.
 */
class AprovacaoController extends Controller
{
    public function index()
    {
        $pendentes = User::where('ativo', false)->with('empresas')->orderBy('created_at')->get();
        $empresas  = Empresa::where('ativo', true)->orderBy('nome')->get();

        return view('aprovacoes.index', compact('pendentes', 'empresas'));
    }

    public function aprovar(Request $request, User $usuario)
    {
        $dados = $request->validate([
            'papel'      => ['required', 'in:admin,usuario'],
            'empresa_id' => ['nullable', 'exists:empresas,id'], // opcional: já tem a padrão
        ]);

        $usuario->update(['ativo' => true, 'papel' => $dados['papel']]);
        if (! empty($dados['empresa_id'])) {
            $usuario->empresas()->syncWithoutDetaching([$dados['empresa_id']]);
        }

        // Avisa o usuário por e-mail (não trava a aprovação se o envio falhar)
        $avisado = true;
        try {
            $usuario->notify(new CadastroAprovado());
        } catch (\Throwable $e) {
            $avisado = false;
            Log::warning('Falha ao enviar e-mail de aprovação: '.$e->getMessage());
        }

        ActivityLog::registrar('aprovar_usuario', "Aprovou o cadastro de \"{$usuario->name}\"");

        $msg = "Usuário \"{$usuario->name}\" aprovado e ativado.";
        $msg .= $avisado ? ' Um e-mail de aviso foi enviado.' : ' (Não foi possível enviar o e-mail de aviso.)';

        return back()->with('ok', $msg);
    }

    public function rejeitar(User $usuario)
    {
        // Só permite rejeitar quem ainda está pendente (inativo)
        abort_if($usuario->ativo, 403);

        $nome = $usuario->name;
        $usuario->delete();

        ActivityLog::registrar('rejeitar_usuario', "Rejeitou o cadastro de \"{$nome}\"");

        return back()->with('ok', "Cadastro de \"{$nome}\" rejeitado e removido.");
    }
}
