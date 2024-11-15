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
        'active',
        'department'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones organizacionales
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

    // Relaciones con documentos
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function responsibleFor()
    {
        return $this->hasMany(DocumentRequest::class, 'responsible_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
    }
}