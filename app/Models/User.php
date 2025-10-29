<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the role associated with the user.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(RolesModel::class, 'roles_id');
    }

    /**
     * Determine if the user has a specific permission through their role.
     */
    public function hasPermission(string $permiso): bool
    {
        $rol = $this->rol;

        if (!$rol) {
            return false;
        }

        if ($rol->tienePermiso('acceso_total')) {
            return true;
        }

        return $rol->tienePermiso($permiso);
    }

    /**
     * Determine if the user has any permission from the provided list.
     *
     * @param  array<int, string>  $permisos
     */
    public function hasAnyPermission(array $permisos): bool
    {
        $rol = $this->rol;

        if (!$rol) {
            return false;
        }

        if ($rol->tienePermiso('acceso_total')) {
            return true;
        }

        foreach ($permisos as $permiso) {
            if ($rol->tienePermiso($permiso)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
