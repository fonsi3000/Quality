<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Process;

class DocumentRequest extends Model
{
    use HasFactory;

    // Constantes para los tipos de solicitud
    const REQUEST_TYPE_CREATE = 'create';
    const REQUEST_TYPE_MODIFY = 'modify';

    // Constantes para los estados
    const STATUS_SIN_APROBAR = 'sin_aprobar';
    const STATUS_EN_ELABORACION = 'en_elaboracion';
    const STATUS_REVISION = 'revision';
    const STATUS_PUBLICADO = 'publicado';
    const STATUS_RECHAZADO = 'rechazado';

    protected $fillable = [
        'request_type',
        'user_id',
        'origin',
        'destination',
        'document_type_id',
        'document_name',
        'document_path',
        'final_document_path',
        'assigned_agent_id',
        'responsible_id',
        'status',
        'description',
        'observations',
        'version'
    ];

    protected $casts = [
        'request_type' => 'string',
        'status' => 'string',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Método para obtener todos los tipos de solicitud
    public static function getRequestTypes()
    {
        return [
            self::REQUEST_TYPE_CREATE => 'Nuevo Documento',
            self::REQUEST_TYPE_MODIFY => 'Modificación'
        ];
    }

    // Método para obtener todos los estados posibles
    public static function getStatusOptions()
    {
        return [
            self::STATUS_SIN_APROBAR => 'Sin Aprobar',
            self::STATUS_EN_ELABORACION => 'En Elaboración',
            self::STATUS_REVISION => 'En Revisión',
            self::STATUS_PUBLICADO => 'Publicado',
            self::STATUS_RECHAZADO => 'Rechazado'
        ];
    }

    // Relación con el usuario que crea la solicitud
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el tipo de documento
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    // Relación con el agente asignado
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    // Relación con el usuario responsable
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    // Scope para filtrar por estado
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope para búsquedas
    public function scopeSearch($query, $search)
    {
        return $query->where('document_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    // Scope para filtrar por agente asignado
    public function scopeByAssignedAgent($query, $agentId)
    {
        return $query->where('assigned_agent_id', $agentId);
    }

    // Métodos de verificación de estado
    public function isSinAprobar()
    {
        return $this->status === self::STATUS_SIN_APROBAR;
    }

    public function isEnElaboracion()
    {
        return $this->status === self::STATUS_EN_ELABORACION;
    }

    public function isEnRevision()
    {
        return $this->status === self::STATUS_REVISION;
    }

    public function isPublicado()
    {
        return $this->status === self::STATUS_PUBLICADO;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_RECHAZADO;
    }

    // Método para verificar si la solicitud es de creación
    public function isCreateRequest()
    {
        return $this->request_type === self::REQUEST_TYPE_CREATE;
    }

    // Método para verificar si la solicitud es de modificación
    public function isModifyRequest()
    {
        return $this->request_type === self::REQUEST_TYPE_MODIFY;
    }

    // Método para obtener la etiqueta del estado actual
    public function getStatusLabel()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    // Método para obtener la etiqueta del tipo de solicitud
    public function getRequestTypeLabel()
    {
        return self::getRequestTypes()[$this->request_type] ?? $this->request_type;
    }

    // Método para verificar si una solicitud puede ser rechazada
    public function canBeRejected()
    {
        return !in_array($this->status, [
            self::STATUS_PUBLICADO,
            self::STATUS_RECHAZADO
        ]);
    }

    // Método para verificar si una solicitud puede ser editada
    public function canBeEdited()
    {
        return !in_array($this->status, [
            self::STATUS_EN_ELABORACION,
            self::STATUS_REVISION,
            self::STATUS_PUBLICADO,
            self::STATUS_RECHAZADO
        ]);
    }

    // Método para verificar si tiene un agente asignado
    public function hasAssignedAgent()
    {
        return !is_null($this->assigned_agent_id);
    }

    // Método para verificar si puede ser asignado
    public function canBeAssigned()
    {
        return in_array($this->status, [
            self::STATUS_SIN_APROBAR,
            self::STATUS_EN_ELABORACION
        ]);
    }

    // Método para verificar si tiene documento final
    public function hasFinalDocument()
    {
        return !is_null($this->final_document_path);
    }

    // Método para verificar si puede tener documento final
    public function canHaveFinalDocument()
    {
        return in_array($this->status, [
            self::STATUS_EN_ELABORACION,
            self::STATUS_REVISION,
            self::STATUS_PUBLICADO
        ]);
    }

    // Método para incrementar la versión del documento
    public function incrementVersion()
    {
        $this->version++;
        return $this->version;
    }

    // Método para obtener la versión actual
    public function getCurrentVersion()
    {
        return $this->version;
    }
   
}