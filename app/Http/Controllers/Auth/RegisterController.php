<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Autocadastro público. O usuário se registra com dados básicos e nasce
 * INATIVO — precisa ser aprovado por um administrador antes de acessar.
 */
class RegisterController extends Controller
{
    public function mostrar()
    {
        return view('auth.cadastro');
    }

    public function cadastrar(Request $request)
    {
        $dados = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required', 'confirmed',
                Password::min(6)->mixedCase()->numbers()->symbols(),
            ],
        ], [], ['email' => 'e-mail', 'password' => 'senha']);

        $user = User::create([
            'name'        => $dados['name'],
            'email'       => $dados['email'],
            'password'    => $dados['password'], // cast 'hashed' criptografa
            'papel'       => 'usuario',
            'super_admin' => false,
            'ativo'       => true, // acesso liberado na hora (sem aprovação)
        ]);

        // Vincula automaticamente à empresa padrão (admin pode incluir outras depois)
        $empresaPadrao = Empresa::where('padrao', true)->first()
            ?? Empresa::where('ativo', true)->first();
        if ($empresaPadrao) {
            $user->empresas()->attach($empresaPadrao->id);
        }

        // Entra automaticamente após o cadastro
        Auth::login($user);

        return redirect()->route('empresas.selecionar');
    }
}
