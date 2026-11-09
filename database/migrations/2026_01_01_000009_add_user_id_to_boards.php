<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boards', function (Blueprint $table) {
            // Dono do quadro. Usuário comum só vê os seus; admin vê todos da empresa.
            $table->foreignId('user_id')->nullable()->after('empresa_id')
                ->constrained('users')->nullOnDelete();
        });

        // Atribui os quadros já existentes ao super administrador
        $adminId = DB::table('users')->where('super_admin', true)->value('id');
        if ($adminId) {
            DB::table('boards')->whereNull('user_id')->update(['user_id' => $adminId]);
        }
    }

    public function down(): void
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
