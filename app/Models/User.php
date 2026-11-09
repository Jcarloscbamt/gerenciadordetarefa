<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'papel',
        'super_admin',
        'ativo',
        'tema',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'super_admin' => 'boolean',
            'ativo' => 'boolean',
        ];
    }

    /** Empresas às quais o usuário tem acesso */
    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Empresa::class);
    }

    /** Admin do sistema (ou super admin) */
    public function isAdmin(): bool
    {
        return $this->papel === 'admin' || $this->super_admin;
    }

    /** Super administrador: enxerga todas as empresas */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->super_admin;
    }
}
