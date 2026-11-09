<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    protected $fillable = ['empresa_id', 'user_id', 'titulo', 'descricao', 'cor'];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /** Dono do quadro */
    public function dono(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Colunas do quadro, já ordenadas */
    public function listas(): HasMany
    {
        return $this->hasMany(BoardList::class)->orderBy('posicao');
    }
}
