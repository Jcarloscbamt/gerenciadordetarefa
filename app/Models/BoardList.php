<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardList extends Model
{
    protected $fillable = ['board_id', 'titulo', 'posicao'];

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /** Cartões da coluna, já ordenados */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class)->orderBy('posicao');
    }
}
