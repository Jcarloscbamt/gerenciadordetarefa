<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\BoardList;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    private function empresaAtual(): int
    {
        return (int) session('empresa_atual');
    }

    private function autorizarLista(BoardList $lista): void
    {
        $board = $lista->board;
        if ($board->empresa_id !== $this->empresaAtual()) {
            abort(404);
        }
        if (! auth()->user()->isAdmin() && $board->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function autorizarCard(Card $card): void
    {
        $this->autorizarLista($card->lista);
    }

    public function store(Request $request, BoardList $lista)
    {
        $this->autorizarLista($lista);

        $dados = $request->validate([
            'titulo'         => ['required', 'string', 'max:255'],
            'descricao'      => ['nullable', 'string'],
            'responsavel_id' => ['nullable', 'exists:users,id'],
            'prazo'          => ['nullable', 'date'],
            'etiqueta'       => ['nullable', 'string', 'max:20'],
        ]);

        $posicao = (int) $lista->cards()->max('posicao') + 1;

        $card = $lista->cards()->create([
            'titulo'         => $dados['titulo'],
            'descricao'      => $dados['descricao'] ?? null,
            'responsavel_id' => $dados['responsavel_id'] ?? null,
            'prazo'          => $dados['prazo'] ?? null,
            'etiqueta'       => $dados['etiqueta'] ?? null,
            'posicao'        => $posicao,
        ]);

        ActivityLog::registrar('criar_card', "Criou o cartão \"{$card->titulo}\"");

        return back();
    }

    public function update(Request $request, Card $card)
    {
        $this->autorizarCard($card);

        $dados = $request->validate([
            'titulo'         => ['required', 'string', 'max:255'],
            'descricao'      => ['nullable', 'string'],
            'responsavel_id' => ['nullable', 'exists:users,id'],
            'prazo'          => ['nullable', 'date'],
            'etiqueta'       => ['nullable', 'string', 'max:20'],
        ]);

        $card->update($dados);
        ActivityLog::registrar('editar_card', "Editou o cartão \"{$card->titulo}\"");

        return back();
    }

    public function destroy(Card $card)
    {
        $this->autorizarCard($card);

        $titulo = $card->titulo;
        $card->delete();
        ActivityLog::registrar('excluir_card', "Excluiu o cartão \"{$titulo}\"");

        return back();
    }

    /** Marca/desmarca o cartão como concluído (AJAX, pela bolinha do cartão) */
    public function concluir(Card $card)
    {
        $this->autorizarCard($card);

        $card->concluido = ! $card->concluido;
        $card->save();

        ActivityLog::registrar(
            $card->concluido ? 'concluir_card' : 'reabrir_card',
            ($card->concluido ? 'Concluiu' : 'Reabriu')." o cartão \"{$card->titulo}\""
        );

        return response()->json(['ok' => true, 'concluido' => $card->concluido]);
    }

    /**
     * Move um cartão para outra coluna e/ou reordena (chamada via AJAX
     * quando o usuário arrasta o cartão). Recebe a lista de destino e a
     * nova ordem dos cartões dessa coluna.
     */
    public function mover(Request $request)
    {
        $dados = $request->validate([
            'card_id'  => ['required', 'exists:cards,id'],
            'lista_id' => ['required', 'exists:board_lists,id'],
            'ordem'    => ['array'],
            'ordem.*'  => ['integer'],
        ]);

        $card  = Card::findOrFail($dados['card_id']);
        $lista = BoardList::findOrFail($dados['lista_id']);

        $this->autorizarCard($card);
        $this->autorizarLista($lista);

        $card->board_list_id = $lista->id;
        $card->save();

        // Reindexa as posições na coluna de destino
        foreach ($dados['ordem'] as $posicao => $id) {
            Card::where('id', $id)->update(['posicao' => $posicao]);
        }

        ActivityLog::registrar('mover_card', "Moveu o cartão \"{$card->titulo}\" para \"{$lista->titulo}\"");

        return response()->json(['ok' => true]);
    }
}
