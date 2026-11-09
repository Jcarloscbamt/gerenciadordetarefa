<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/** Salva o tema visual escolhido pelo usuário (fica na conta dele). */
class TemaController extends Controller
{
    public function salvar(Request $request)
    {
        $dados = $request->validate([
            'tema' => ['required', 'in:claro,escuro,oceano'],
        ]);

        $user = $request->user();
        $user->tema = $dados['tema'];
        $user->save();

        return back();
    }
}
