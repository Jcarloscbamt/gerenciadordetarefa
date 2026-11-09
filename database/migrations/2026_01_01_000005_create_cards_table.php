<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Cartões (tarefas) — pertencem a uma coluna
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_list_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->unsignedInteger('posicao')->default(0);
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('prazo')->nullable();
            $table->string('etiqueta', 20)->nullable(); // cor da etiqueta (ex.: #ef4444)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
