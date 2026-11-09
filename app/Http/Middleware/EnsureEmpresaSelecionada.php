<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que há uma empresa selecionada na sessão e que o usuário
 * tem acesso a ela. É o coração do isolamento por empresa:
 * tudo depois deste middleware trabalha dentro da empresa escolhida.
 */
class EnsureEmpresaSelecionada
{
    public function handle(Request $request, Closure $next): Response
    {
        $empresaId = session('empresa_atual');

        if (! $empresaId) {
            return redirect()->route('empresas.selecionar');
        }

        $user = $request->user();

        // Super admin acessa qualquer empresa; os demais, só as vinculadas
        if (! $user->isSuperAdmin() && ! $user->empresas->contains('id', $empresaId)) {
            session()->forget('empresa_atual');
            return redirect()->route('empresas.selecionar')
                ->withErrors(['empresa' => 'Você não tem acesso a essa empresa.']);
        }

        $empresa = Empresa::find($empresaId);

        if (! $empresa || ! $empresa->ativo) {
            session()->forget('empresa_atual');
            return redirect()->route('empresas.selecionar')
                ->withErrors(['empresa' => 'Empresa indisponível.']);
        }

        // Disponibiliza a empresa atual para todas as views
        view()->share('empresaAtual', $empresa);

        return $next($request);
    }
}
