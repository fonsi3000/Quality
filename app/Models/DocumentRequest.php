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
    const REQUEST_TYPE_OBSOLETE = 'obsolete';

    // Constantes para los estados
    const STATUS_SIN_APROBAR = 'sin_aprobar';
    const STATUS_EN_ELABORACION = 'en_elaboracion';
    const STATUS_REVISION = 'revision';
    const STATUS_PUBLICADO = 'publicado';
    const STATUS_RECHAZADO = 'rechazado';
    const STATUS_PENDIENTE_LIDER = 'pendiente_lider';
    const STATUS_RECHAZADO_LIDER = 'rechazado_lider';
    const STATUS_OBSOLETO = 'obsoleto';

    // Campos que pueden ser asignados masivamente
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
        'leader_observations',
        'leader_approval_date',
        'version',
        'process_id',
        'reference_document_id',
        'is_public'
    ];

    // Conversión de tipos de datos
    protected $casts = [
        'request_type' => 'string',
        'status' => 'string',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'leader_approval_date' => 'datetime',
        'is_public' => 'boolean'
    ];

    // Relaciones con otros modelos

    /**
     * Relación con el usuario que crea la solicitud
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el documento de referencia
     */
    public function referenceDocument()
    {
        return $this->belongsTo(DocumentRequest::class, 'reference_document_id');
    }

    /**
     * Relación con los documentos que referencian a este
     */
    public function referencingDocuments()
    {
        return $this->hasMany(DocumentRequest::class, 'reference_document_id');
    }

    /**
     * Relación con el proceso al que pertenece
     */
    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * Relación con el tipo de documento
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Relación con el agente asignado
     */
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Relación con el usuario responsable
     */
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    // Métodos de consulta (Scopes)

    /**
     * Filtrar por estado
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Búsqueda general
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('document_name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Filtrar por agente asignado
     */
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

    public function isPendingLeaderApproval()
    {
        return $this->status === self::STATUS_PENDIENTE_LIDER;
    }

    public function isRejectedByLeader()
    {
        return $this->status === self::STATUS_RECHAZADO_LIDER;
    }

    // Métodos de verificación de tipo

    public function isCreateRequest()
    {
        return $this->request_type === self::REQUEST_TYPE_CREATE;
    }

    public function isModifyRequest()
    {
        return $this->request_type === self::REQUEST_TYPE_MODIFY;
    }

    // Métodos de obtención de etiquetas

    public static function getRequestTypes()
    {
        return [
            self::REQUEST_TYPE_CREATE => 'Nuevo Documento',
            self::REQUEST_TYPE_MODIFY => 'Modificación',
            self::REQUEST_TYPE_OBSOLETE => 'Obsoletizar'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDIENTE_LIDER => 'Pendiente por el Líder',
            self::STATUS_RECHAZADO_LIDER => 'Rechazado por Líder',
            self::STATUS_SIN_APROBAR => 'Pendiente en Aprobacion por calidad',
            self::STATUS_EN_ELABORACION => 'En Elaboración',
            self::STATUS_REVISION => 'En Revisión',
            self::STATUS_PUBLICADO => 'Publicado',
            self::STATUS_RECHAZADO => 'Rechazado',
            self::STATUS_OBSOLETO => 'Obsoleto'
        ];
    }

    public function getStatusLabel()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getRequestTypeLabel()
    {
        return self::getRequestTypes()[$this->request_type] ?? $this->request_type;
    }

    // Métodos de verificación de permisos

    public function canBeRejected()
    {
        return !in_array($this->status, [
            self::STATUS_PUBLICADO,
            self::STATUS_RECHAZADO
        ]);
    }

    public function canBeEdited()
    {
        return !in_array($this->status, [
            self::STATUS_EN_ELABORACION,
            self::STATUS_REVISION,
            self::STATUS_PUBLICADO,
            self::STATUS_RECHAZADO,
            self::STATUS_OBSOLETO
        ]);
    }

    public function canBeAssigned()
    {
        return in_array($this->status, [
            self::STATUS_SIN_APROBAR,
            self::STATUS_EN_ELABORACION
        ]);
    }

    public function canBeApprovedByLeader(User $user)
    {
        return $user->id === $this->process->leader_id;
    }

    // Métodos de gestión de documentos

    public function hasAssignedAgent()
    {
        return !is_null($this->assigned_agent_id);
    }

    public function hasFinalDocument()
    {
        return !is_null($this->final_document_path);
    }

    public function canHaveFinalDocument()
    {
        return in_array($this->status, [
            self::STATUS_EN_ELABORACION,
            self::STATUS_REVISION,
            self::STATUS_PUBLICADO
        ]);
    }

    // Métodos de gestión de versiones

    public function incrementVersion()
    {
        $this->version++;
        return $this->version;
    }

    public function getCurrentVersion()
    {
        return $this->version;
    }

    // Getters para campos específicos

    public function getLeaderObservations()
    {
        return $this->leader_observations;
    }

    public function getLeaderApprovalDate()
    {
        return $this->leader_approval_date;
    }
    public function isObsoleto()
    {
        return $this->status === self::STATUS_OBSOLETO;
    }
    public function isObsoleteRequest()
    {
        return $this->request_type === self::REQUEST_TYPE_OBSOLETE;
    }
    public function setInitialStatus()
    {
        if ($this->request_type === self::REQUEST_TYPE_CREATE) {
            $this->status = self::STATUS_SIN_APROBAR;
        } else {
            // Si es modificación u obsoletización
            $this->status = self::STATUS_PENDIENTE_LIDER;
        }
        return $this;
    }

    /**
     * Verifica si el documento tiene un documento de referencia
     */
    public function hasReferenceDocument()
    {
        return !is_null($this->reference_document_id);
    }

    /**
     * Verifica si el documento tiene documentos que lo referencian
     */
    public function hasReferencingDocuments()
    {
        return $this->referencingDocuments()->exists();
    }

    /**
     * Obtiene el documento de referencia original si existe
     */
    public function getOriginalDocument()
    {
        return $this->referenceDocument;
    }

    /**
     * Obtiene todas las versiones posteriores del documento
     */
    public function getVersionHistory()
    {
        return $this->referencingDocuments()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Verifica si el documento puede ser referenciado
     */
    public function canBeReferenced()
    {
        return $this->status === self::STATUS_PUBLICADO;
    }
}
