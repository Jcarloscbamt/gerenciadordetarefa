<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    protected $fillable = [
        'board_list_id',
        'titulo',
        'descricao',
        'posicao',
        'responsavel_id',
        'prazo',
        'etiqueta',
        'concluido',
    ];

    protected $casts = [
        'prazo' => 'date',
        'concluido' => 'boolean',
    ];

    public function lista(): BelongsTo
    {
        return $this->belongsTo(BoardList::class, 'board_list_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }
}
