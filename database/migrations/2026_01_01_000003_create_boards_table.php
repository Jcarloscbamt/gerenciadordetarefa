<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Quadros (boards) — cada quadro pertence a UMA empresa (isolamento)
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('cor', 7)->default('#2563eb');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
