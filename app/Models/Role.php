<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * Campos asignables masivamente.
     */
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Un rol puede pertenecer a muchos usuarios.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}