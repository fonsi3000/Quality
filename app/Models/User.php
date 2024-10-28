<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'unit_id',
        'process_id',
        'position_id',
        'active' // Campo agregado
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean' // Cast agregado
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // Método de alcance para filtrar usuarios activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}