<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Board;
use App\Models\Empresa;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    /** Garante empresa correta + privacidade (usuário comum só vê os próprios) */
    private function autorizar(Board $board): void
    {
        if ($board->empresa_id !== (int) session('empresa_atual')) {
            abort(404);
        }
        if (! auth()->user()->isAdmin() && $board->user_id !== auth()->id()) {
            abort(403, 'Este quadro pertence a outro usuário.');
        }
    }

    public function index()
    {
        $query = Board::where('empresa_id', session('empresa_atual'))
            ->withCount('listas')->latest();

        // Usuário comum vê só os quadros dele; admin/super vê todos da empresa
        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $boards = $query->get();

        return view('boards.index', compact('boards'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo'    => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'cor'       => ['nullable', 'string', 'max:7'],
        ]);

        $board = Board::create([
            'empresa_id' => session('empresa_atual'),
            'user_id'    => auth()->id(), // dono do quadro
            'titulo'     => $dados['titulo'],
            'descricao'  => $dados['descricao'] ?? null,
            'cor'        => $dados['cor'] ?? '#2563eb',
        ]);

        // Colunas padrão (Kanban clássico)
        foreach (['A Fazer', 'Fazendo', 'Concluído'] as $i => $titulo) {
            $board->listas()->create(['titulo' => $titulo, 'posicao' => $i]);
        }

        ActivityLog::registrar('criar_board', "Criou o quadro \"{$board->titulo}\"");

        return redirect()->route('boards.show', $board);
    }

    public function show(Board $board)
    {
        $this->autorizar($board);

        $board->load(['listas.cards.responsavel']);

        // Membros da empresa (para atribuir responsável)
        $membros = Empresa::find(session('empresa_atual'))
            ->users()->orderBy('name')->get();

        // Quadros para o seletor de troca (respeitando a privacidade)
        $boardsQuery = Board::where('empresa_id', session('empresa_atual'))->orderBy('titulo');
        if (! auth()->user()->isAdmin()) {
            $boardsQuery->where('user_id', auth()->id());
        }
        $boards = $boardsQuery->get(['id', 'titulo']);

        return view('boards.show', compact('board', 'membros', 'boards'));
    }

    public function update(Request $request, Board $board)
    {
        $this->autorizar($board);

        $dados = $request->validate([
            'titulo'    => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'cor'       => ['nullable', 'string', 'max:7'],
        ]);

        $board->update($dados);
        ActivityLog::registrar('editar_board', "Editou o quadro \"{$board->titulo}\"");

        return back()->with('ok', 'Quadro atualizado.');
    }

    public function destroy(Board $board)
    {
        $this->autorizar($board);

        $titulo = $board->titulo;
        $board->delete();
        ActivityLog::registrar('excluir_board', "Excluiu o quadro \"{$titulo}\"");

        return redirect()->route('boards.index')->with('ok', 'Quadro excluído.');
    }
}
