<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('padrao')->default(false)->after('ativo');
        });

        // Garante uma empresa padrão (onde novos cadastros entram automaticamente)
        if (! DB::table('empresas')->where('padrao', true)->exists()) {
            DB::table('empresas')->insert([
                'nome'       => 'Geral',
                'slug'       => 'geral',
                'cor'        => '#4f46e5',
                'ativo'      => true,
                'padrao'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('padrao');
        });
    }
};
