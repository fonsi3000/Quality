<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'unit_id',
        'process_id',
        'second_process_id',
        'third_process_id',
        'fourth_process_id',
        'fifth_process_id',
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

    /*
    |--------------------------------------------------------------------------
    | Relaciones organizacionales
    |--------------------------------------------------------------------------
    */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function secondaryProcess()
    {
        return $this->belongsTo(Process::class, 'second_process_id');
    }

    public function thirdProcess()
    {
        return $this->belongsTo(Process::class, 'third_process_id');
    }

    public function fourthProcess()
    {
        return $this->belongsTo(Process::class, 'fourth_process_id');
    }

    public function fifthProcess()
    {
        return $this->belongsTo(Process::class, 'fifth_process_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones con documentos
    |--------------------------------------------------------------------------
    */
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function responsibleFor()
    {
        return $this->hasMany(DocumentRequest::class, 'responsible_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones de liderazgo
    |--------------------------------------------------------------------------
    */

    // Procesos donde es líder principal
    public function ledProcesses()
    {
        return $this->hasMany(Process::class, 'leader_id');
    }

    // Procesos donde es segundo líder
    public function secondLedProcesses()
    {
        return $this->hasMany(Process::class, 'second_leader_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Métodos personalizados
    |--------------------------------------------------------------------------
    */
    public function getFriendlyRoleName()
    {
        $roleNames = [
            'admin' => 'Líder de Calidad',
            'agent' => 'Auditor',
            'user' => 'Colaborador'
        ];

        $role = $this->roles->first();
        return $role ? ($roleNames[$role->name] ?? $role->name) : 'Sin rol';
    }
}
