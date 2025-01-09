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
        'leader_id'  // Añadido el nuevo campo
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Relación existente con usuarios del proceso
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Nueva relación con el líder
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // Método para verificar si un usuario es el líder
    public function isLeader(User $user): bool
    {
        return $this->leader_id === $user->id;
    }

    // Método para verificar si el proceso tiene líder
    public function hasLeader(): bool
    {
        return !is_null($this->leader_id);
    }

    // Método para asignar un líder
    public function assignLeader(User $user): bool
    {
        if (!$user->hasRole('leader')) {
            return false;
        }

        $this->leader_id = $user->id;
        return $this->save();
    }

    // Método para remover el líder
    public function removeLeader(): bool
    {
        $this->leader_id = null;
        return $this->save();
    }

    // Scope para procesos activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope para procesos con líder
    public function scopeWithLeader($query)
    {
        return $query->whereNotNull('leader_id');
    }

    // Scope para procesos sin líder
    public function scopeWithoutLeader($query)
    {
        return $query->whereNull('leader_id');
    }
}