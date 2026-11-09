<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Log de auditoria da empresa atual. Acessível a administradores.
 */
class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->where('empresa_id', session('empresa_atual'))
            ->latest();

        // Filtro opcional por ação
        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Lista de ações distintas para o filtro
        $acoes = ActivityLog::where('empresa_id', session('empresa_atual'))
            ->distinct()->orderBy('acao')->pluck('acao');

        return view('logs.index', compact('logs', 'acoes'));
    }
}
