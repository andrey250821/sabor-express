<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\Role;
use App\Models\Pedido;
use App\Models\AsignacionDelivery;
use App\Models\Calificacion;
use App\Models\Notificacion;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Campos asignables magit add .sivamente.
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'google_id',
        'foto_perfil',
        'telefono',
        'password',
        'estado',
    ];

    /**
     * URL pública de la foto de perfil.
     *
     * Puede contener una ruta local almacenada en el disco público
     * o una URL externa, por ejemplo la foto proporcionada por Google.
     */
    public function getFotoPerfilUrlAttribute(): ?string
    {
        if (!$this->foto_perfil) {
            return null;
        }

        if (Str::startsWith($this->foto_perfil, ['http://', 'https://'])) {
            return $this->foto_perfil;
        }

        return Storage::disk('public')->url($this->foto_perfil);
    }

    /**
     * Campos ocultos.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Un usuario pertenece a un rol.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Un cliente puede realizar muchos pedidos.
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Pedidos cuya preparación inició este cocinero.
     */
    public function pedidosCocina(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cocinero_id');
    }

    /**
     * Un delivery puede tener muchas asignaciones.
     */
    public function asignacionesDelivery(): HasMany
    {
        return $this->hasMany(AsignacionDelivery::class, 'delivery_id');
    }

    /**
     * Un usuario puede realizar muchas calificaciones.
     */
    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    /**
     * Un usuario puede recibir muchas notificaciones.
     */
    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }
}
