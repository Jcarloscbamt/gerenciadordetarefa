<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    protected $fillable = ['nome', 'slug', 'cor', 'ativo', 'padrao'];

    protected $casts = ['ativo' => 'boolean', 'padrao' => 'boolean'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class);
    }
}
