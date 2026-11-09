<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /** Exibe a tela de login */
    public function mostrar()
    {
        return view('auth.login');
    }

    /** Processa o login */
    public function entrar(Request $request)
    {
        $dados = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [], ['email' => 'e-mail', 'password' => 'senha']);

        if (Auth::attempt($dados, $request->boolean('remember'))) {
            // Bloqueia usuários inativos (desativados por um administrador)
            if (! $request->user()->ativo) {
                Auth::logout();
                return back()->withErrors(['email' => 'Sua conta está inativa. Contate um administrador.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();
            ActivityLog::registrar('login', 'Entrou no sistema');

            return redirect()->route('empresas.selecionar');
        }

        return back()
            ->withErrors(['email' => 'E-mail ou senha incorretos.'])
            ->onlyInput('email');
    }

    /** Encerra a sessão */
    public function sair(Request $request)
    {
        ActivityLog::registrar('logout', 'Saiu do sistema');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
