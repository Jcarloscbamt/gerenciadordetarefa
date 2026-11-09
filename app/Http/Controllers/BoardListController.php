<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Board;
use App\Models\BoardList;
use Illuminate\Http\Request;

class BoardListController extends Controller
{
    private function autorizarBoard(Board $board): void
    {
        if ($board->empresa_id !== (int) session('empresa_atual')) {
            abort(404);
        }
        if (! auth()->user()->isAdmin() && $board->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function autorizarLista(BoardList $lista): void
    {
        $this->autorizarBoard($lista->board);
    }

    public function store(Request $request, Board $board)
    {
        $this->autorizarBoard($board);

        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
        ]);

        $posicao = (int) $board->listas()->max('posicao') + 1;
        $board->listas()->create(['titulo' => $dados['titulo'], 'posicao' => $posicao]);

        ActivityLog::registrar('criar_lista', "Adicionou a coluna \"{$dados['titulo']}\"");

        return back();
    }

    public function update(Request $request, BoardList $lista)
    {
        $this->autorizarLista($lista);

        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
        ]);

        $lista->update($dados);
        ActivityLog::registrar('editar_lista', "Renomeou coluna para \"{$dados['titulo']}\"");

        return back();
    }

    public function destroy(BoardList $lista)
    {
        $this->autorizarLista($lista);

        $titulo = $lista->titulo;
        $lista->delete();
        ActivityLog::registrar('excluir_lista', "Excluiu a coluna \"{$titulo}\"");

        return back();
    }
}
