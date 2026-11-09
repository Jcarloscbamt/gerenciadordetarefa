<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'empresa_id', 'acao', 'descricao', 'ip'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Atalho para registrar uma ação no log de auditoria.
     * Usa o usuário autenticado, a empresa selecionada na sessão e o IP da requisição.
     */
    public static function registrar(string $acao, string $descricao): void
    {
        static::create([
            'user_id'    => auth()->id(),
            'empresa_id' => session('empresa_atual'),
            'acao'       => $acao,
            'descricao'  => $descricao,
            'ip'         => request()->ip(),
        ]);
    }
}
