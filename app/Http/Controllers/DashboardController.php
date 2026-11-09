<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;

class DashboardController extends Controller
{
    public function index()
    {
        $empresaId = session('empresa_atual');

        // Respeita a privacidade: usuário comum só vê os próprios quadros
        $query = Board::where('empresa_id', $empresaId)->withCount('listas')->latest();
        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        $boards = $query->get();

        $totalBoards = $boards->count();
        $boardIds = $boards->pluck('id');

        $totalCards = Card::whereHas('lista', fn ($q) => $q->whereIn('board_id', $boardIds))->count();

        $cardsConcluidos = Card::where('concluido', true)
            ->whereHas('lista', fn ($q) => $q->whereIn('board_id', $boardIds))->count();

        return view('dashboard', compact('boards', 'totalBoards', 'totalCards', 'cardsConcluidos'));
    }
}
