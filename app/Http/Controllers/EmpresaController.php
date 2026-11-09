<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * CRUD de empresas — acessível somente ao super administrador.
 */
class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::withCount(['users', 'boards'])->orderBy('nome')->get();

        return view('empresas.index', compact('empresas'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cor'  => ['nullable', 'string', 'max:7'],
        ]);

        $empresa = Empresa::create([
            'nome'  => $dados['nome'],
            'slug'  => $this->slugUnico($dados['nome']),
            'cor'   => $dados['cor'] ?? '#2563eb',
            'ativo' => true,
        ]);

        // Vincula o super admin que criou, para que ele possa selecioná-la
        $request->user()->empresas()->syncWithoutDetaching([$empresa->id]);

        ActivityLog::registrar('criar_empresa', "Criou a empresa \"{$empresa->nome}\"");

        return back()->with('ok', 'Empresa criada.');
    }

    public function update(Request $request, Empresa $empresa)
    {
        $dados = $request->validate([
            'nome'  => ['required', 'string', 'max:255'],
            'cor'   => ['nullable', 'string', 'max:7'],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $empresa->update([
            'nome'  => $dados['nome'],
            'cor'   => $dados['cor'] ?? $empresa->cor,
            'ativo' => $request->boolean('ativo'),
        ]);

        ActivityLog::registrar('editar_empresa', "Editou a empresa \"{$empresa->nome}\"");

        return back()->with('ok', 'Empresa atualizada.');
    }

    public function destroy(Empresa $empresa)
    {
        $nome = $empresa->nome;
        $empresa->delete(); // cascata remove boards/listas/cards da empresa
        ActivityLog::registrar('excluir_empresa', "Excluiu a empresa \"{$nome}\"");

        // Se a empresa excluída era a ativa, limpa a sessão
        if ((int) session('empresa_atual') === $empresa->id) {
            session()->forget('empresa_atual');
        }

        return back()->with('ok', 'Empresa excluída.');
    }

    private function slugUnico(string $nome): string
    {
        $base = Str::slug($nome);
        $slug = $base;
        $i = 1;
        while (Empresa::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
