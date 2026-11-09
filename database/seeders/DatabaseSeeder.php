<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Super administrador (enxerga todas as empresas)
        $super = User::create([
            'name'        => 'Administrador',
            'email'       => 'admin@sistema.test',
            'password'    => 'senha12345',  // cast 'hashed' criptografa
            'papel'       => 'admin',
            'super_admin' => true,
            'ativo'       => true,
        ]);

        // Empresa de exemplo
        $empresa = Empresa::create([
            'nome'  => 'Minha Empresa',
            'slug'  => 'minha-empresa',
            'cor'   => '#2563eb',
            'ativo' => true,
        ]);

        // Segunda empresa (demonstra o isolamento)
        $empresa2 = Empresa::create([
            'nome'  => 'Cliente Exemplo',
            'slug'  => 'cliente-exemplo',
            'cor'   => '#16a34a',
            'ativo' => true,
        ]);

        // Super admin vinculado às duas (também poderia acessar sem vínculo)
        $super->empresas()->attach([$empresa->id, $empresa2->id]);

        // Usuário comum da primeira empresa
        $usuario = User::create([
            'name'     => 'João da Silva',
            'email'    => 'joao@sistema.test',
            'password' => 'senha12345',
            'papel'    => 'usuario',
            'ativo'    => true,
        ]);
        $usuario->empresas()->attach($empresa->id);

        // Quadro de exemplo com colunas e cartões
        $board = Board::create([
            'empresa_id' => $empresa->id,
            'titulo'     => 'Projeto Exemplo',
            'descricao'  => 'Quadro de demonstração do sistema.',
            'cor'        => '#2563eb',
        ]);

        $aFazer    = $board->listas()->create(['titulo' => 'A Fazer', 'posicao' => 0]);
        $fazendo   = $board->listas()->create(['titulo' => 'Fazendo', 'posicao' => 1]);
        $concluido = $board->listas()->create(['titulo' => 'Concluído', 'posicao' => 2]);

        $aFazer->cards()->createMany([
            ['titulo' => 'Levantar requisitos', 'posicao' => 0, 'etiqueta' => '#ef4444'],
            ['titulo' => 'Definir cronograma', 'posicao' => 1, 'responsavel_id' => $usuario->id, 'prazo' => now()->addDays(5)],
        ]);
        $fazendo->cards()->create([
            'titulo' => 'Desenvolver protótipo', 'posicao' => 0, 'etiqueta' => '#f59e0b',
        ]);
        $concluido->cards()->create([
            'titulo' => 'Reunião inicial', 'posicao' => 0, 'etiqueta' => '#16a34a',
        ]);
    }
}
