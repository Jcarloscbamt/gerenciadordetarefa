<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Empresa;
use Illuminate\Http\Request;

/**
 * Seleção/troca da empresa ativa. Super admin vê todas as empresas;
 * usuário comum vê apenas as que estão vinculadas a ele.
 */
class EmpresaSwitchController extends Controller
{
    public function selecionar(Request $request)
    {
        $user = $request->user();

        $empresas = $user->isSuperAdmin()
            ? Empresa::where('ativo', true)->orderBy('nome')->get()
            : $user->empresas()->where('ativo', true)->orderBy('nome')->get();

        return view('empresas.selecionar', compact('empresas'));
    }

    public function definir(Request $request, Empresa $empresa)
    {
        $user = $request->user();

        // Valida acesso à empresa escolhida
        if (! $user->isSuperAdmin() && ! $user->empresas->contains('id', $empresa->id)) {
            abort(403);
        }

        if (! $empresa->ativo) {
            return back()->withErrors(['empresa' => 'Empresa inativa.']);
        }

        session(['empresa_atual' => $empresa->id]);
        ActivityLog::registrar('trocar_empresa', "Selecionou a empresa \"{$empresa->nome}\"");

        return redirect()->route('dashboard');
    }
}
