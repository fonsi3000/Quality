<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'leader_id',
        'second_leader_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // Usuarios asignados al proceso
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Líder principal
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // Segundo líder
    public function secondLeader()
    {
        return $this->belongsTo(User::class, 'second_leader_id');
    }

    // Solicitudes de documentos asociadas al proceso
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos de utilidad
    |--------------------------------------------------------------------------
    */

    // Verificar si un usuario es líder principal
    public function isLeader(User $user): bool
    {
        return $this->leader_id === $user->id;
    }

    // Verificar si un usuario es segundo líder
    public function isSecondLeader(User $user): bool
    {
        return $this->second_leader_id === $user->id;
    }

    // Verificar si el proceso tiene al menos un líder
    public function hasAnyLeader(): bool
    {
        return !is_null($this->leader_id) || !is_null($this->second_leader_id);
    }

    // Asignar líder principal
    public function assignLeader(User $user): bool
    {
        if (!$user->hasRole('leader')) {
            return false;
        }

        $this->leader_id = $user->id;
        return $this->save();
    }

    // Asignar segundo líder
    public function assignSecondLeader(User $user): bool
    {
        if (!$user->hasRole('leader')) {
            return false;
        }

        $this->second_leader_id = $user->id;
        return $this->save();
    }

    // Remover líder principal
    public function removeLeader(): bool
    {
        $this->leader_id = null;
        return $this->save();
    }

    // Remover segundo líder
    public function removeSecondLeader(): bool
    {
        $this->second_leader_id = null;
        return $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes 
    |--------------------------------------------------------------------------
    */

    // Solo procesos activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Procesos con al menos un líder
    public function scopeWithAnyLeader($query)
    {
        return $query->whereNotNull('leader_id')->orWhereNotNull('second_leader_id');
    }

    // Procesos sin ningún líder
    public function scopeWithoutLeaders($query)
    {
        return $query->whereNull('leader_id')->whereNull('second_leader_id');
    }
}
