<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RegraSenha;

/**
 * Recuperação de senha por e-mail, usando o mecanismo nativo do Laravel
 * (tabela password_reset_tokens + notificação ResetPassword).
 */
class PasswordResetController extends Controller
{
    /** Tela: solicitar o link (informar e-mail) */
    public function mostrarSolicitar()
    {
        return view('auth.esqueci-senha');
    }

    /** Envia o e-mail com o link de redefinição */
    public function enviarLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']], [], ['email' => 'e-mail']);

        $status = Password::sendResetLink($request->only('email'));

        // Mesma mensagem para e-mail existente ou não (evita revelar cadastro)
        return back()->with('status', 'Se o e-mail estiver cadastrado, enviamos um link de redefinição.');
    }

    /** Tela: formulário de nova senha (a partir do link recebido) */
    public function mostrarRedefinir(Request $request, string $token)
    {
        return view('auth.redefinir-senha', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /** Grava a nova senha */
    public function redefinir(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', RegraSenha::min(6)],
        ], [], ['email' => 'e-mail', 'password' => 'senha']);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => $password, // cast 'hashed' criptografa
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PasswordReset) {
            return redirect()->route('login')->with('status', 'Senha redefinida! Faça login com a nova senha.');
        }

        return back()->withErrors(['email' => 'Link inválido ou expirado. Solicite um novo.'])->onlyInput('email');
    }
}
