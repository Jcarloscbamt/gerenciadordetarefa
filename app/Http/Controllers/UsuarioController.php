<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Gestão de usuários DA EMPRESA ATUAL. Acessível a administradores.
 * Os usuários criados aqui são automaticamente vinculados à empresa atual.
 */
class UsuarioController extends Controller
{
    private function empresa(): Empresa
    {
        return Empresa::findOrFail(session('empresa_atual'));
    }

    public function index()
    {
        $empresa = $this->empresa();
        $usuarios = $empresa->users()->orderBy('name')->get();

        // Usuários que ainda NÃO têm acesso a esta empresa (para vincular)
        $disponiveis = User::whereNotIn('id', $usuarios->pluck('id'))
            ->orderBy('name')->get();

        return view('usuarios.index', compact('usuarios', 'disponiveis'));
    }

    /** Vincula um usuário já existente à empresa atual */
    public function vincular(Request $request)
    {
        $dados = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($dados['user_id']);
        $user->empresas()->syncWithoutDetaching([$this->empresa()->id]);

        ActivityLog::registrar('vincular_usuario', "Vinculou \"{$user->name}\" à empresa");

        return back()->with('ok', "Usuário \"{$user->name}\" vinculado à empresa.");
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(6)],
            'papel'    => ['required', 'in:admin,usuario'],
        ]);

        $user = User::create([
            'name'     => $dados['name'],
            'email'    => $dados['email'],
            'password' => $dados['password'], // o cast 'hashed' criptografa
            'papel'    => $dados['papel'],
        ]);

        // Vincula à empresa atual
        $user->empresas()->syncWithoutDetaching([$this->empresa()->id]);

        ActivityLog::registrar('criar_usuario', "Criou o usuário \"{$user->name}\"");

        return back()->with('ok', 'Usuário criado e vinculado à empresa.');
    }

    public function update(Request $request, User $usuario)
    {
        // Só permite editar quem pertence à empresa atual
        abort_unless($usuario->empresas->contains('id', $this->empresa()->id), 404);

        $dados = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,'.$usuario->id],
            'papel'    => ['required', 'in:admin,usuario'],
            'password' => ['nullable', Password::min(6)],
            'ativo'    => ['nullable', 'boolean'],
        ]);

        $usuario->name  = $dados['name'];
        $usuario->email = $dados['email'];
        $usuario->papel = $dados['papel'];
        $usuario->ativo = $request->boolean('ativo');

        if (! empty($dados['password'])) {
            $usuario->password = $dados['password'];
        }

        $usuario->save();
        ActivityLog::registrar('editar_usuario', "Editou o usuário \"{$usuario->name}\"");

        return back()->with('ok', 'Usuário atualizado.');
    }

    public function destroy(User $usuario)
    {
        abort_unless($usuario->empresas->contains('id', $this->empresa()->id), 404);

        // Não remove o usuário do sistema: apenas desvincula da empresa atual
        $usuario->empresas()->detach($this->empresa()->id);
        ActivityLog::registrar('desvincular_usuario', "Desvinculou \"{$usuario->name}\" da empresa");

        return back()->with('ok', 'Usuário desvinculado desta empresa (a conta foi mantida).');
    }

    /** Exclui a conta PERMANENTEMENTE (libera o e-mail). Só super admin. */
    public function excluir(User $usuario)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        abort_if($usuario->id === auth()->id(), 403); // não excluir a si mesmo

        $nome = $usuario->name;
        $usuario->delete(); // cascata remove vínculos de empresa

        ActivityLog::registrar('excluir_usuario', "Excluiu a conta de \"{$nome}\"");

        return back()->with('ok', "Conta de \"{$nome}\" excluída permanentemente.");
    }
}
