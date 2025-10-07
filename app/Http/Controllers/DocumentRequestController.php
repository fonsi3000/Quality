<?php

namespace App\Http\Controllers;

use App\Exports\DocumentRequestExport;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\Process;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DocumentRequestController extends Controller
{
    // Constantes existentes
    private const MESSAGE_SUCCESS_CREATE = 'Solicitud de documento creada exitosamente.';
    private const MESSAGE_SUCCESS_UPDATE = 'Solicitud actualizada exitosamente.';
    private const MESSAGE_SUCCESS_DELETE = 'Solicitud eliminada exitosamente.';
    private const MESSAGE_SUCCESS_ASSIGN = 'Responsable asignado y solicitud aprobada exitosamente.';
    private const MESSAGE_SUCCESS_REJECT = 'Solicitud rechazada exitosamente.';
    private const MESSAGE_ERROR_FILE = 'El archivo no se encuentra disponible.';
    private const MESSAGE_ERROR_GENERIC = 'Ha ocurrido un error. Por favor, intente nuevamente.';
    private const MESSAGE_SUCCESS_FINAL_DOCUMENT = 'Documento final adjuntado exitosamente.';

    // Constantes para mensajes de líder
    private const MESSAGE_SUCCESS_LEADER_APPROVE = 'Solicitud aprobada por el líder exitosamente.';
    private const MESSAGE_SUCCESS_LEADER_REJECT = 'Solicitud rechazada por el líder.';
    private const MESSAGE_ERROR_LEADER_PERMISSION = 'No tiene permisos de líder para realizar esta acción.';
    private const MESSAGE_ERROR_LEADER_PROCESS = 'El proceso no tiene un líder asignado.';
    private const MESSAGE_ERROR_INVALID_STATUS = 'Estado no válido para esta acción.';

    // Nuevas constantes para el segundo líder
    private const MESSAGE_SUCCESS_SECOND_LEADER_APPROVE = 'Solicitud aprobada por el segundo líder exitosamente.';
    private const MESSAGE_SUCCESS_SECOND_LEADER_REJECT = 'Solicitud rechazada por el segundo líder.';
    private const MESSAGE_PENDING_SECOND_LEADER = 'Solicitud aprobada por el líder principal. Ahora está pendiente de aprobación por el segundo líder.';
    private const MESSAGE_ERROR_SECOND_LEADER_PERMISSION = 'No tiene permisos de segundo líder para realizar esta acción.';

    public function index(Request $request)
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'process']);

            // Base query para los conteos
            $baseQuery = DocumentRequest::query();

            // Aplicar filtros de rol tanto al query principal como al de conteos
            if (Auth::user()->hasRole('user')) {
                $query->where('user_id', Auth::id());
                $baseQuery->where('user_id', Auth::id());
            }

            if (Auth::user()->hasRole('leader')) {
                $query->where('process_id', Auth::user()->process_id);
                $baseQuery->where('process_id', Auth::user()->process_id);
            }

            // Obtener conteo por estado excluyendo PUBLICADO
            $statusCounts = $baseQuery
                ->select('status', DB::raw('count(*) as count'))
                ->where('status', '!=', DocumentRequest::STATUS_PUBLICADO)
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Filtrar documentos publicados
            $query->whereNotIn('status', [
                DocumentRequest::STATUS_PUBLICADO,
                DocumentRequest::STATUS_OBSOLETO
            ]);

            // Aplicar búsqueda si existe un término
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('documentType', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Filtrar por estado
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Obtener resultados con paginación y mantener query string
            $documentRequests = $query->latest()->paginate(10)->withQueryString();

            // Obtener usuarios activos con roles admin o agent
            $users = User::where('active', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin']);
                })
                ->get();

            // Definir clases de estados
            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            // Obtener solo las etiquetas de estados no publicados
            $statusLabels = array_filter(
                DocumentRequest::getStatusOptions(),
                function ($key) {
                    return $key !== DocumentRequest::STATUS_PUBLICADO;
                },
                ARRAY_FILTER_USE_KEY
            );

            // Retornar vista con todos los datos necesarios
            return view('document-requests.index', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users',
                'statusCounts'
            ));
        } catch (\Exception $e) {
            Log::error('Error en index DocumentRequest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function create()
    {
        try {
            $documentTypes = DocumentType::where('is_active', true)->get();
            $users = User::where('active', true)->get();
            $processes = Process::all();


            // Cargar el usuario autenticado con sus procesos (principal y secundario)
            $user = Auth::user();

            // Pasamos el usuario a la vista también
            return view('document-requests.create', compact('documentTypes', 'users', 'user', 'processes'));
        } catch (\Exception $e) {
            Log::error('Error en create DocumentRequest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }


    public function store(Request $request)
    {
        // Log de información si se está intentando subir un archivo
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            Log::info('Intento de subida de archivo', [
                'nombre_original' => $file->getClientOriginalName(),
                'tipo_mime' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'tamaño' => $file->getSize()
            ]);
        }

        if (!$request->hasFile('document')) {
            throw new \Exception('No se ha proporcionado ningún archivo');
        }


        // Validación principal del formulario
        $validated = $request->validate([
            'document' => 'required|file|max:102400|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip',
            'description' => 'required|string',
            'document_type_id' => 'required|exists:document_types,id',
            'document_name' => 'required|string|max:255',
            'user_id' => 'required|string|max:255',
            'process_id' => 'required|string',
            'created_at' => 'required|date'
        ]);

        $process_origin = Process::where('id', '=', $validated['process_id'])->first();

        if (!$process_origin) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'El proceso no existe en los registros');
        }

        try {
            DB::beginTransaction();
            // Guardar archivo y obtener ruta
            $file = $request->file('document');
            $fileName = 'final_' . Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents/final', $fileName, 'public');



            // Datos base comunes a cualquier tipo de solicitud
            $documentData = [
                'user_id' => $validated['user_id'],
                'final_document_path' => $path,
                'description' => $validated['description'],
                // 🔁 Aquí guardamos ambos: nombre como 'origin', id como 'process_id'
                'process_id' => $validated['process_id'],
                'origin' => $process_origin->name,
                'assigned_agent_id' => Auth::user()->id,
                'document_type_id' => $validated['document_type_id'],
                'document_name' => $validated['document_name'],
                'status' => DocumentRequest::STATUS_PUBLICADO
            ];

            // Crear instancia del modelo y establecer estado inicial
            $documentRequest = new DocumentRequest($documentData);
            $documentRequest->created_at = $validated['created_at']; // fecha personalizada
            $documentRequest->save();

            DB::commit();

            // Log exitoso
            Log::info('Solicitud de documento creada exitosamente', [
                'document_request_id' => $documentRequest->id,
                'status' => $documentRequest->status,
                'user_id' => $validated['user_id'],
                'process_id' => $validated['process_id'],
                'assigned_agent_id' => Auth::user()->id,
                'created_at' => $validated['created_at']
            ]);

            return redirect()
                ->route('documents.published')
                ->with('success', self::MESSAGE_SUCCESS_CREATE);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Error al crear solicitud de documento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }


    public function show(DocumentRequest $documentRequest)
    {
        try {
            $documentRequest->load(['user', 'documentType', 'responsible', 'process']);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $documentRequest->id,
                        'request_type' => $documentRequest->request_type,
                        'document_name' => $documentRequest->document_name,
                        'status' => $documentRequest->status,
                        'document_type' => [
                            'id' => $documentRequest->documentType->id,
                            'name' => $documentRequest->documentType->name
                        ],
                        'origin' => $documentRequest->origin,
                        'destination' => $documentRequest->destination,
                        'description' => $documentRequest->description,
                        'observations' => $documentRequest->observations,
                        'leader_observations' => $documentRequest->leader_observations,
                        'second_leader_observations' => $documentRequest->second_leader_observations,
                        'leader_approval_date' => $documentRequest->leader_approval_date ?
                            $documentRequest->leader_approval_date->format('d/m/Y H:i') : null,
                        'second_leader_approval_date' => $documentRequest->second_leader_approval_date ?
                            $documentRequest->second_leader_approval_date->format('d/m/Y H:i') : null,
                        'user' => [
                            'id' => $documentRequest->user->id,
                            'name' => $documentRequest->user->name
                        ],
                        'process' => [
                            'id' => $documentRequest->process->id,
                            'name' => $documentRequest->process->name
                        ],
                        'created_at' => $documentRequest->created_at->format('d/m/Y H:i'),
                        'updated_at' => $documentRequest->updated_at->format('d/m/Y H:i'),
                        'document_path' => $documentRequest->document_path,
                        'final_document_path' => $documentRequest->final_document_path,
                    ]
                ]);
            }

            return view('document-requests.show', compact('documentRequest'));
        } catch (\Exception $e) {
            Log::error('Error en show DocumentRequest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'documentRequest' => $documentRequest->id
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cargar los detalles de la solicitud'
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error al cargar los detalles de la solicitud');
        }
    }

    public function edit(DocumentRequest $documentRequest)
    {
        try {
            if (!$documentRequest->canBeEdited()) {
                throw new \Exception('Esta solicitud no puede ser editada en su estado actual.');
            }

            // Verificar si el usuario es admin o el creador de la solicitud
            if (
                !Auth::user()->hasRole('admin') &&
                Auth::id() !== $documentRequest->user_id
            ) {
                abort(403, 'No tienes permiso para editar esta solicitud.');
            }

            $documentTypes = DocumentType::where('is_active', true)->get();
            $processes = Process::all(); // Agregar todos los procesos

            return view('document-requests.edit', compact('documentRequest', 'documentTypes', 'processes'));
        } catch (\Exception $e) {
            Log::error('Error en edit DocumentRequest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'documentRequest' => $documentRequest->id
            ]);
            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }


    public function update(Request $request, DocumentRequest $documentRequest)
    {
        $validated = $request->validate([
            'request_type' => 'required|in:create,modify',
            'document_type_id' => 'required|exists:document_types,id',
            'document_name' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'nullable|file|max:102400|mimes:pdf,doc,docx,xls,xlsx',
            'created_at' => 'required|date', // Validación para la fecha de creación personalizada
        ]);

        try {
            DB::beginTransaction();

            // Validar si puede ser editado normalmente
            if (!$documentRequest->canBeEdited()) {
                // Si está publicado, solo un admin puede editarlo
                if (!$documentRequest->isPublicado() || !Auth::user()->hasRole('admin')) {
                    throw new \Exception('Esta solicitud no puede ser editada en su estado actual.');
                }
            }

            // Procesamiento del archivo cargado
            if ($request->hasFile('document')) {
                $newFilePath = $this->handleFileStorage($request->file('document'));

                if ($documentRequest->isPublicado()) {
                    // Solo el rol admin puede reemplazar el documento publicado
                    if (!Auth::user()->hasRole('admin')) {
                        throw new \Exception('Solo los administradores pueden actualizar documentos publicados.');
                    }

                    // Eliminar el archivo final anterior si existe
                    if ($documentRequest->final_document_path) {
                        Storage::disk('public')->delete($documentRequest->final_document_path);
                    }

                    $validated['final_document_path'] = $newFilePath;
                } else {
                    // Eliminar el archivo de documento base si existe
                    if ($documentRequest->document_path) {
                        Storage::disk('public')->delete($documentRequest->document_path);
                    }

                    $validated['document_path'] = $newFilePath;
                }
            }

            // Actualización general de campos
            $documentRequest->update([
                'request_type' => $validated['request_type'],
                'document_type_id' => $validated['document_type_id'],
                'document_name' => $validated['document_name'],
                'description' => $validated['description'],
                'document_path' => $validated['document_path'] ?? $documentRequest->document_path,
                'final_document_path' => $validated['final_document_path'] ?? $documentRequest->final_document_path,
            ]);

            // Actualizar la fecha de creación (no está en $fillable)
            $documentRequest->created_at = $validated['created_at'];
            $documentRequest->save();

            DB::commit();

            Log::info('Solicitud de documento actualizada exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'new_status' => $documentRequest->status,
                'created_at' => $validated['created_at'],
                'final_document_updated' => isset($validated['final_document_path']) ? true : false
            ]);

            $redirectRoute = match ($documentRequest->status) {
                DocumentRequest::STATUS_PUBLICADO => 'documents.published',
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'documents.pending-leader',
                DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER => 'documents.pending-leader',
                default => 'documents.requests.index'
            };

            return redirect()
                ->route($redirectRoute)
                ->with('success', self::MESSAGE_SUCCESS_UPDATE);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }

            Log::error('Error al actualizar DocumentRequest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'documentRequest' => $documentRequest->id
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }


    public function destroy(DocumentRequest $documentRequest, Request $request)
    {
        try {
            DB::beginTransaction();

            // Eliminar archivos asociados
            if ($documentRequest->document_path) {
                Storage::disk('public')->delete($documentRequest->document_path);
            }

            if ($documentRequest->final_document_path) {
                Storage::disk('public')->delete($documentRequest->final_document_path);
            }

            $documentRequest->delete();

            DB::commit();

            Log::info('Solicitud de documento eliminada exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            // Verificar la URL de referencia
            $previousUrl = url()->previous();

            // Si viene de la vista index, mantener en esa vista
            if (str_contains($previousUrl, route('documents.requests.index'))) {
                return redirect()->route('documents.requests.index')
                    ->with('success', self::MESSAGE_SUCCESS_DELETE);
            }

            // Si no, usar la lógica original del match
            $redirectRoute = match ($documentRequest->status) {
                DocumentRequest::STATUS_PUBLICADO => 'documents.published',
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'documents.pending-leader',
                DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER => 'documents.pending-leader',
                default => 'documents.requests.index'
            };

            return redirect()
                ->route($redirectRoute)
                ->with('success', self::MESSAGE_SUCCESS_DELETE);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar DocumentRequest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'documentRequest' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function downloadDocument(DocumentRequest $documentRequest)
    {
        try {
            if (
                !$documentRequest->document_path ||
                !Storage::disk('public')->exists($documentRequest->document_path)
            ) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }


            // Limpiar el nombre del documento para evitar caracteres especiales
            $baseName  = $documentRequest->document_name;
            $extension = pathinfo($documentRequest->document_path, PATHINFO_EXTENSION);
            $filename = $baseName . '.' . $extension;

            return Storage::disk('public')->download($documentRequest->document_path, $filename);
        } catch (\Exception $e) {
            Log::error('Error al descargar documento', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al descargar el documento: ' . $e->getMessage());
        }
    }


    public function previewDocument(DocumentRequest $documentRequest)
    {
        try {
            if (!$documentRequest->document_path || !Storage::disk('public')->exists($documentRequest->document_path)) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }

            $path = Storage::disk('public')->path($documentRequest->document_path);
            $contentType = mime_content_type($path);
            $extension = pathinfo($documentRequest->document_path, PATHINFO_EXTENSION);

            return response()->file($path, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . $documentRequest->document_name . '.' . $extension . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al previsualizar documento', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al previsualizar el documento: ' . $e->getMessage());
        }
    }

    public function downloadFinalDocument(DocumentRequest $documentRequest)
    {
        try {
            if (
                !$documentRequest->final_document_path ||
                !Storage::disk('public')->exists($documentRequest->final_document_path)
            ) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }

            $extension = pathinfo($documentRequest->final_document_path, PATHINFO_EXTENSION);
            $originalName = $documentRequest->document_name;
            $filename = $originalName . '.' . $extension;

            return Storage::disk('public')->download($documentRequest->final_document_path, $filename);
        } catch (\Exception $e) {
            Log::error('Error al descargar documento final', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al descargar el documento: ' . $e->getMessage());
        }
    }

    public function previewFinalDocument(DocumentRequest $documentRequest)
    {
        try {
            if (
                !$documentRequest->final_document_path ||
                !Storage::disk('public')->exists($documentRequest->final_document_path)
            ) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }

            $path = Storage::disk('public')->path($documentRequest->final_document_path);
            $contentType = mime_content_type($path);
            $extension = pathinfo($documentRequest->final_document_path, PATHINFO_EXTENSION);

            return response()->file($path, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="final_' . $documentRequest->document_name . '.' . $extension . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al previsualizar documento final', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al previsualizar el documento: ' . $e->getMessage());
        }
    }

    public function attachFinalDocument(Request $request, DocumentRequest $documentRequest)
    {
        $validated = $request->validate([
            'final_document' => 'required|file|max:102400|mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip',
            'observations' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            if ($documentRequest->status !== DocumentRequest::STATUS_EN_ELABORACION) {
                throw new \Exception('Solo se puede adjuntar el documento final cuando está en elaboración.');
            }

            if ($documentRequest->final_document_path) {
                Storage::disk('public')->delete($documentRequest->final_document_path);
            }

            $file = $request->file('final_document');
            $fileName = 'final_' . Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents/final', $fileName, 'public');

            $documentRequest->update([
                'final_document_path' => $path,
                'status' => DocumentRequest::STATUS_REVISION,
                'observations' => $validated['observations']
            ]);

            DB::commit();

            Log::info('Documento final adjuntado exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->with('success', self::MESSAGE_SUCCESS_FINAL_DOCUMENT);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Error al adjuntar documento final', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function updateStatus(Request $request, DocumentRequest $documentRequest)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:' . implode(',', [
                    DocumentRequest::STATUS_SIN_APROBAR,
                    DocumentRequest::STATUS_EN_ELABORACION,
                    DocumentRequest::STATUS_REVISION,
                    DocumentRequest::STATUS_PUBLICADO,
                    DocumentRequest::STATUS_RECHAZADO,
                    DocumentRequest::STATUS_PENDIENTE_LIDER,
                    DocumentRequest::STATUS_RECHAZADO_LIDER
                ])
            ],
            'observations' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el cambio de estado sea válido según el flujo
            $this->validateStatusTransition($documentRequest->status, $validated['status']);

            $oldStatus = $documentRequest->status;

            $documentRequest->update([
                'status' => $validated['status'],
                'observations' => $validated['observations'] ?? $documentRequest->observations
            ]);

            DB::commit();

            Log::info('Estado de documento actualizado exitosamente', [
                'document_request_id' => $documentRequest->id,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->with('success', 'Estado actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar estado', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }
    // Quiero que cuando edite un document que este en estado 
    public function assign(Request $request, DocumentRequest $documentRequest)
    {
        $validated = $request->validate([
            'assigned_agent_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            if (!$documentRequest->canBeAssigned()) {
                throw new \Exception('Esta solicitud no puede ser asignada en su estado actual.');
            }

            // Verificar que el usuario asignado tenga el rol correcto
            $assignedUser = User::whereId($validated['assigned_agent_id'])
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin']);
                })
                ->first();

            if (!$assignedUser) {
                throw new \Exception('El usuario asignado debe tener rol de administrador.');
            }

            // Verificar si el agente pertenece al mismo proceso o es admin  
            if (
                !$assignedUser->hasRole('admin') &&
                $assignedUser->process_id !== $documentRequest->process_id
            ) {
                throw new \Exception('El agente debe pertenecer al mismo proceso del documento.');
            }

            $documentRequest->update([
                'assigned_agent_id' => $validated['assigned_agent_id'],
                'status' => DocumentRequest::STATUS_EN_ELABORACION,
            ]);

            DB::commit();

            Log::info('Solicitud asignada exitosamente', [
                'request_id' => $documentRequest->id,
                'assigned_agent_id' => $validated['assigned_agent_id'],
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('success', self::MESSAGE_SUCCESS_ASSIGN);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al asignar agente', [
                'error' => $e->getMessage(),
                'request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: 'Error al asignar el agente.');
        }
    }

    public function reject(Request $request, DocumentRequest $documentRequest)
    {
        if (!$documentRequest->canBeRejected()) {
            return redirect()
                ->back()
                ->with('error', 'Esta solicitud no puede ser rechazada en su estado actual.');
        }

        $validated = $request->validate([
            'observations' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            Log::info('Intentando rechazar solicitud', [
                'request_id' => $documentRequest->id,
                'current_status' => $documentRequest->status,
                'observations' => $validated['observations'],
                'user_id' => Auth::id()
            ]);

            $documentRequest->status = DocumentRequest::STATUS_RECHAZADO;
            $documentRequest->observations = $validated['observations'];
            $documentRequest->save();

            DB::commit();

            Log::info('Solicitud rechazada exitosamente', [
                'request_id' => $documentRequest->id,
                'new_status' => $documentRequest->status,
                'observations' => $documentRequest->observations,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('success', self::MESSAGE_SUCCESS_REJECT);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al rechazar solicitud', [
                'error' => $e->getMessage(),
                'request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function approve(Request $request, DocumentRequest $documentRequest)
    {
        if ($documentRequest->status !== DocumentRequest::STATUS_REVISION) {
            return redirect()
                ->back()
                ->with('error', 'Solo se pueden aprobar documentos en estado de revisión.');
        }

        $validated = $request->validate([
            'observations' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Verificar si el usuario tiene permisos para aprobar
            if (!Auth::user()->hasRole(['admin', 'quality_leader'])) {
                throw new \Exception('No tiene permisos para aprobar documentos.');
            }

            Log::info('Tipo de solicitud recibido:', [
                'request_type' => $documentRequest->request_type,
                'document_id' => $documentRequest->id
            ]);

            // Manejar la aprobación según el tipo de solicitud
            switch ($documentRequest->request_type) {
                case 'create':
                    $documentRequest->update([
                        'status' => DocumentRequest::STATUS_PENDIENTE_LIDER,
                        'observations' => $validated['observations'] ?? $documentRequest->observations,
                        'responsible_id' => Auth::id()
                    ]);
                    $message = 'Documento enviado para aprobación del líder.';
                    break;

                case 'modify':
                    if (!$documentRequest->reference_document_id) {
                        throw new \Exception('No se encontró el documento de referencia.');
                    }

                    $originalDocument = DocumentRequest::findOrFail($documentRequest->reference_document_id);

                    if ($originalDocument->status !== DocumentRequest::STATUS_PUBLICADO) {
                        throw new \Exception('El documento original debe estar publicado para poder ser modificado.');
                    }

                    // Guardar la versión del documento original antes de obsoletizarlo
                    $previousVersion = $originalDocument->getCurrentVersion();

                    // Cambiar el estado del documento original a obsoleto
                    $originalDocument->update([
                        'status' => DocumentRequest::STATUS_OBSOLETO
                    ]);

                    // Publicar la nueva versión incrementando el número de versión
                    $documentRequest->version = $previousVersion + 1;
                    $documentRequest->update([
                        'status' => DocumentRequest::STATUS_PUBLICADO,
                        'observations' => $validated['observations'] ?? $documentRequest->observations,
                        'responsible_id' => Auth::id()
                    ]);

                    Log::info('Documento modificado y versionado', [
                        'document_id' => $documentRequest->id,
                        'previous_version' => $previousVersion,
                        'new_version' => $documentRequest->version,
                        'original_document_id' => $originalDocument->id
                    ]);

                    $message = 'Documento modificado y publicado exitosamente como versión ' . $documentRequest->version;
                    break;

                case 'obsolete':
                    if (!$documentRequest->reference_document_id) {
                        throw new \Exception('No se encontró el documento de referencia.');
                    }

                    $originalDocument = DocumentRequest::findOrFail($documentRequest->reference_document_id);

                    if ($originalDocument->status !== DocumentRequest::STATUS_PUBLICADO) {
                        throw new \Exception('El documento debe estar publicado para poder ser obsoletizado.');
                    }

                    // Cambiar el estado del documento original a obsoleto
                    $originalDocument->update([
                        'status' => DocumentRequest::STATUS_OBSOLETO
                    ]);

                    // Marcar la solicitud como completada
                    $documentRequest->update([
                        'status' => DocumentRequest::STATUS_PENDIENTE_LIDER,
                        'observations' => $validated['observations'] ?? $documentRequest->observations,
                        'responsible_id' => Auth::id()
                    ]);

                    $message = 'Documento enviado para aprobación del líder de obsoletización.';
                    break;

                default:
                    throw new \Exception('Tipo de solicitud no válido. Tipo recibido: ' . $documentRequest->request_type);
            }

            DB::commit();

            Log::info('Documento procesado exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'request_type' => $documentRequest->request_type,
                'observations' => $validated['observations'] ?? null
            ]);

            return redirect()
                ->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al aprobar documento', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'request_type' => $documentRequest->request_type,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function returnToProgress(Request $request, DocumentRequest $documentRequest)
    {
        if ($documentRequest->status !== DocumentRequest::STATUS_REVISION) {
            return redirect()
                ->back()
                ->with('error', 'Solo se pueden devolver documentos en estado de revisión.');
        }

        $validated = $request->validate([
            'observations' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Verificar si el usuario tiene permisos para devolver el documento
            if (!Auth::user()->hasRole(['admin', 'quality_leader'])) {
                throw new \Exception('No tiene permisos para devolver documentos a elaboración.');
            }

            $documentRequest->update([
                'status' => DocumentRequest::STATUS_EN_ELABORACION,
                'observations' => $validated['observations']
            ]);

            DB::commit();

            Log::info('Documento devuelto a elaboración exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'observations' => $validated['observations']
            ]);

            return redirect()
                ->back()
                ->with('success', 'Documento devuelto a elaboración exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al devolver documento a elaboración', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function leaderApprove(Request $request, DocumentRequest $documentRequest)
    {
        try {
            // Validar que el documento esté pendiente de aprobación
            if ($documentRequest->status !== DocumentRequest::STATUS_PENDIENTE_LIDER) {
                throw new \Exception('El documento no está pendiente de aprobación del líder.');
            }

            // Validar que el usuario sea el líder del proceso
            if (!$this->validateLeaderPermissions($documentRequest)) {
                throw new \Exception(self::MESSAGE_ERROR_LEADER_PERMISSION);
            }

            $validated = $request->validate([
                'observations' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            // Verificar si el proceso tiene un segundo líder asignado
            if ($documentRequest->process->second_leader_id) {
                // Si hay segundo líder, pasa a pendiente de aprobación por el segundo líder
                $newStatus = DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER;
                $message = self::MESSAGE_PENDING_SECOND_LEADER;
            } else {
                // Si no hay segundo líder, determinar el estado según el tipo de solicitud
                $newStatus = match ($documentRequest->request_type) {
                    'create' => DocumentRequest::STATUS_PUBLICADO,
                    'modify', 'obsolete' => DocumentRequest::STATUS_SIN_APROBAR,
                    default => throw new \Exception('Tipo de solicitud no válido')
                };
                $message = self::MESSAGE_SUCCESS_LEADER_APPROVE;
            }

            $updateData = [
                'status' => $newStatus,
                'leader_observations' => $validated['observations'],
                'leader_approval_date' => now()
            ];

            // Si es un nuevo documento y pasa a publicado, actualizar campos adicionales si es necesario
            if ($documentRequest->request_type === 'create' && $newStatus === DocumentRequest::STATUS_PUBLICADO) {
                $updateData['version'] = 1; // Primera versión para documentos nuevos
                // Aquí puedes agregar otros campos necesarios para documentos publicados
            }

            $documentRequest->update($updateData);

            DB::commit();

            Log::info('Documento aprobado por líder exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id,
                'request_type' => $documentRequest->request_type,
                'new_status' => $newStatus,
                'observations' => $validated['observations'] ?? null
            ]);

            return redirect()
                ->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en leaderApprove', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function leaderReject(Request $request, DocumentRequest $documentRequest)
    {
        try {
            // Validar que el documento esté en un estado que pueda ser rechazado por un líder
            if (!in_array($documentRequest->status, [
                DocumentRequest::STATUS_PENDIENTE_LIDER,
                DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER
            ])) {
                throw new \Exception('El documento no está en un estado que pueda ser rechazado por un líder.');
            }

            // Validar que el usuario sea el líder correspondiente según el estado
            if (!$this->validateLeaderPermissions($documentRequest)) {
                throw new \Exception(self::MESSAGE_ERROR_LEADER_PERMISSION);
            }

            $validated = $request->validate([
                'observations' => 'required|string|max:1000',
            ]);

            DB::beginTransaction();

            // Identificar qué líder está rechazando
            $isPrimaryLeader = $documentRequest->status === DocumentRequest::STATUS_PENDIENTE_LIDER;

            // Determinar el estado según el tipo de solicitud y el líder que rechaza
            if ($isPrimaryLeader) {
                $newStatus = match ($documentRequest->request_type) {
                    'create' => DocumentRequest::STATUS_REVISION,
                    'modify', 'obsolete' => DocumentRequest::STATUS_RECHAZADO_LIDER,
                    default => throw new \Exception('Tipo de solicitud no válido')
                };

                $documentRequest->update([
                    'status' => $newStatus,
                    'leader_observations' => $validated['observations'],
                    'leader_approval_date' => now()
                ]);
            } else {
                // Si es el segundo líder quien rechaza, determinar estado según tipo de solicitud
                if ($documentRequest->request_type === 'create') {
                    // Para documentos de CREACIÓN, el rechazo del segundo líder los envía a REVISION
                    $documentRequest->update([
                        'status' => DocumentRequest::STATUS_REVISION,
                        'second_leader_observations' => $validated['observations'],
                        'second_leader_approval_date' => now()
                    ]);
                } else {
                    // Para otros tipos de documentos, sigue yendo a RECHAZADO_LIDER
                    $documentRequest->update([
                        'status' => DocumentRequest::STATUS_RECHAZADO_LIDER,
                        'second_leader_observations' => $validated['observations'],
                        'second_leader_approval_date' => now()
                    ]);
                }
            }

            DB::commit();

            Log::info('Documento rechazado por líder', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id,
                'request_type' => $documentRequest->request_type,
                'rejected_by' => $isPrimaryLeader ? 'líder principal' : 'segundo líder',
                'new_status' => $documentRequest->status,
                'observations' => $validated['observations']
            ]);

            return redirect()
                ->back()
                ->with('success', self::MESSAGE_SUCCESS_LEADER_REJECT);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en leaderReject', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }

    /**
     * Procesar la aprobación por parte del segundo líder
     */
    public function secondLeaderApprove(Request $request, DocumentRequest $documentRequest)
    {
        try {
            // Validar que el documento esté pendiente de aprobación por el segundo líder
            if ($documentRequest->status !== DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER) {
                throw new \Exception('El documento no está pendiente de aprobación del segundo líder.');
            }

            // Validar que el usuario sea el segundo líder del proceso
            if (!$this->validateLeaderPermissions($documentRequest)) {
                throw new \Exception(self::MESSAGE_ERROR_SECOND_LEADER_PERMISSION);
            }

            $validated = $request->validate([
                'observations' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            // Determinar el siguiente estado según el tipo de solicitud
            $newStatus = match ($documentRequest->request_type) {
                'create' => DocumentRequest::STATUS_PUBLICADO,
                'modify', 'obsolete' => DocumentRequest::STATUS_SIN_APROBAR,
                default => throw new \Exception('Tipo de solicitud no válido')
            };

            // Crear un arreglo con los datos a actualizar
            $updateData = [
                'status' => $newStatus,
                'second_leader_observations' => $validated['observations'],
                'second_leader_approval_date' => now()
            ];

            // Si es un nuevo documento y pasa a publicado, actualizar versión
            if ($documentRequest->request_type === 'create' && $newStatus === DocumentRequest::STATUS_PUBLICADO) {
                $updateData['version'] = 1; // Primera versión para documentos nuevos
            }

            $documentRequest->update($updateData);

            DB::commit();

            Log::info('Documento aprobado por segundo líder exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id,
                'request_type' => $documentRequest->request_type,
                'new_status' => $newStatus,
                'observations' => $validated['observations'] ?? null
            ]);

            return redirect()
                ->back()
                ->with('success', self::MESSAGE_SUCCESS_SECOND_LEADER_APPROVE);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en secondLeaderApprove', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }

    /**
     * Muestra documentos pendientes de aprobación por cualquiera de los líderes
     */
    public function pendingLeaderApproval()
    {
        try {
            // 1. Construir la consulta base con todas las relaciones necesarias
            $query = DocumentRequest::with([
                'user',
                'documentType',
                'responsible',
                'assignedAgent',
                'process'
            ])->whereIn('status', [
                DocumentRequest::STATUS_PENDIENTE_LIDER,
                DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER
            ]);

            // 2. Filtrar por procesos donde el usuario es alguno de los líderes
            if (!Auth::user()->hasPermissionTo('admin.only')) {
                // Filtrar procesos donde el usuario es líder principal o segundo líder
                $processesAsPrimaryLeader = Process::where('leader_id', Auth::id())->pluck('id')->toArray();
                $processesAsSecondLeader = Process::where('second_leader_id', Auth::id())->pluck('id')->toArray();

                if (empty($processesAsPrimaryLeader) && empty($processesAsSecondLeader)) {
                    return redirect()
                        ->route('documents.requests.index')
                        ->with('error', 'No tiene permisos de líder en ningún proceso.');
                }

                // Filtrar por rol y estado para mostrar sólo documentos relevantes al usuario
                $query->where(function ($q) use ($processesAsPrimaryLeader, $processesAsSecondLeader) {
                    // Como líder principal, mostrar documentos en estado pendiente de líder principal de sus procesos
                    if (!empty($processesAsPrimaryLeader)) {
                        $q->where(function ($subq) use ($processesAsPrimaryLeader) {
                            $subq->whereIn('process_id', $processesAsPrimaryLeader)
                                ->where('status', DocumentRequest::STATUS_PENDIENTE_LIDER);
                        });
                    }

                    // Como segundo líder, mostrar documentos en estado pendiente de segundo líder de sus procesos
                    if (!empty($processesAsSecondLeader)) {
                        $q->orWhere(function ($subq) use ($processesAsSecondLeader) {
                            $subq->whereIn('process_id', $processesAsSecondLeader)
                                ->where('status', DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER);
                        });
                    }
                });
            }

            // 3. Obtener los documentos paginados
            $documentRequests = $query->latest()->paginate(10);

            // 4. Obtener usuarios activos con roles admin o agent
            $users = User::where('active', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'agent']);
                })
                ->get();

            // 5. Definir clases de estilo para los estados
            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            // 6. Obtener las etiquetas de estado del modelo
            $statusLabels = DocumentRequest::getStatusOptions();

            // 7. Obtener los tipos de documento activos
            $documentTypes = DocumentType::where('is_active', true)->get();

            // 8. Obtener los procesos activos
            $processes = Process::where('active', true)->get();

            // 9. Determinar qué tipo de líder es el usuario para cada documento
            foreach ($documentRequests as $doc) {
                if ($doc->process->leader_id === Auth::id()) {
                    $doc->userLeaderType = 'primary';
                } elseif ($doc->process->second_leader_id === Auth::id()) {
                    $doc->userLeaderType = 'secondary';
                } else {
                    $doc->userLeaderType = null;
                }
            }

            // 10. Retornar la vista con todas las variables necesarias
            return view('documents.pending-leader', compact(
                'documentRequests',
                'users',
                'statusClasses',
                'statusLabels',
                'documentTypes',
                'processes'
            ));
        } catch (\Exception $e) {
            Log::error('Error en pendingLeaderApproval', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'process_id' => Auth::user()->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('documents.requests.index')
                ->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }


    public function inProgress()
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent', 'process'])
                ->where('status', DocumentRequest::STATUS_EN_ELABORACION);

            // Filtrar por proceso si el usuario no es admin
            if (!Auth::user()->hasRole('admin')) {
                $query->where('process_id', Auth::user()->process_id);
            }

            $documentRequests = $query->latest()->paginate(10);

            $users = User::where('active', true)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'agent']);
                })->get();

            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            $statusLabels = DocumentRequest::getStatusOptions();

            return view('documents.in-progress', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users'
            ));
        } catch (\Exception $e) {
            Log::error('Error en inProgress DocumentRequest', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'process_id' => Auth::user()->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function inReview()
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent', 'process'])
                ->where('status', DocumentRequest::STATUS_REVISION);

            // Filtrar por proceso si el usuario no es admin
            if (!Auth::user()->hasRole('admin')) {
                $query->where('process_id', Auth::user()->process_id);
            }

            $documentRequests = $query->latest()->paginate(10);

            $users = User::where('active', true)->get();

            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            $statusLabels = DocumentRequest::getStatusOptions();

            return view('documents.in-review', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users'
            ));
        } catch (\Exception $e) {
            Log::error('Error en inReview DocumentRequest', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'process_id' => Auth::user()->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function published(Request $request)
    {
        try {
            $query = DocumentRequest::with([
                'user',
                'documentType',
                'responsible',
                'assignedAgent',
                'process'
            ]);

            // Verificar si es admin o agent
            if (Auth::user()->hasRole(['admin', 'agent'])) {
                $query->where('status', DocumentRequest::STATUS_PUBLICADO);
            } else {
                // Usuario regular: Mostrar documentos públicos O documentos de su proceso principal o secundario
                $query->where('status', DocumentRequest::STATUS_PUBLICADO)
                    ->where(function ($q) {
                        $q->where('is_public', true)
                            ->orWhereIn('process_id', [
                                Auth::user()->process_id,
                                Auth::user()->second_process_id,
                            ]);
                    });
            }

            // Filtro de búsqueda por texto
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('documentType', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Filtro por tipo de documento
            if ($request->filled('document_type_id') && $request->document_type_id != 'all') {
                $query->where('document_type_id', $request->document_type_id);
            }

            // Filtro por proceso
            if ($request->filled('process_id') && $request->process_id != 'all') {
                $query->where('process_id', $request->process_id);
            }

            // Filtro por público/privado
            if ($request->has('is_public') && $request->is_public != 'all') {
                $query->where('is_public', $request->is_public);
            }

            // Filtrar por rango de fechas
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $documentRequests = $query->latest()->paginate(10);
            $documentTypes = DocumentType::where('is_active', true)->get();
            $processes = Process::where('active', true)->get();
            $users = User::where('active', true)->get();

            // Clases de estado actualizadas
            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_OBSOLETO => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
            ];

            $statusLabels = DocumentRequest::getStatusOptions();
            $canManageDocuments = Auth::user()->can('admin');

            return view('documents.published', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users',
                'documentTypes',
                'processes',
                'canManageDocuments'
            ));
        } catch (\Exception $e) {
            Log::error('Error en published DocumentRequest', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'process_id' => Auth::user()->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent', 'process']);

            // Filtrar por proceso si el usuario no es admin
            if (!Auth::user()->hasRole('admin')) {
                $query->where(function ($q) {
                    $q->where('process_id', Auth::user()->process_id)
                        ->orWhere('process_id', Auth::user()->second_process_id);
                });
            }

            // Aplicar búsqueda general
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('documentType', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('process', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Filtrar por estado
            if ($request->has('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }

            // Filtrar por tipo de documento
            if ($request->has('document_type_id') && $request->document_type_id != 'all') {
                $query->where('document_type_id', $request->document_type_id);
            }

            // Filtrar por rango de fechas
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $documentRequests = $query->latest()->paginate(10);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('documents.partials.document-list', compact('documentRequests'))->render(),
                    'pagination' => view('documents.partials.pagination', compact('documentRequests'))->render(),
                ]);
            }

            $users = User::where('active', true)->get();
            $documentTypes = DocumentType::where('is_active', true)->get();
            $processes = Process::where('active', true)->get();
            $statusLabels = DocumentRequest::getStatusOptions();

            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            return view('documents.search', compact(
                'documentRequests',
                'users',
                'documentTypes',
                'processes',
                'statusLabels',
                'statusClasses'
            ));
        } catch (\Exception $e) {
            Log::error('Error en search DocumentRequest', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'process_id' => Auth::user()->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => self::MESSAGE_ERROR_GENERIC
                ], 500);
            }

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }


    public function statistics()
    {
        try {
            $query = DocumentRequest::query();

            // Filtrar por proceso si el usuario no es admin
            if (!Auth::user()->hasRole('admin')) {
                $query->where('process_id', Auth::user()->process_id);
            }

            // Estadísticas por estado
            $statsByStatus = (clone $query)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->pluck('total', 'status')
                ->toArray();

            // Estadísticas por tipo de documento
            $statsByType = (clone $query)
                ->select('document_type_id', DB::raw('count(*) as total'))
                ->groupBy('document_type_id')
                ->with('documentType')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->documentType->name => $item->total];
                })
                ->toArray();

            // Documentos por mes (últimos 12 meses)
            $documentsByMonth = (clone $query)
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('count(*) as total')
                )
                ->whereYear('created_at', '>=', now()->subYear()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->month => $item->total];
                })
                ->toArray();

            // Estadísticas por proceso
            $statsByProcess = (clone $query)
                ->select('process_id', DB::raw('count(*) as total'))
                ->groupBy('process_id')
                ->with('process')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->process->name => $item->total];
                })
                ->toArray();

            // Tiempos promedio de aprobación por estado
            $avgTimeByStatus = (clone $query)
                ->select(
                    'status',
                    DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
                )
                ->whereNotNull('updated_at')
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status => round($item->avg_hours, 2)];
                })
                ->toArray();

            // Estadísticas de aprobación de líderes
            $leaderStats = (clone $query)
                ->select(
                    DB::raw('COUNT(CASE WHEN status = "sin_aprobar" THEN 1 END) as approved'),
                    DB::raw('COUNT(CASE WHEN status = "rechazado_lider" THEN 1 END) as rejected'),
                    DB::raw('AVG(CASE WHEN leader_approval_date IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, created_at, leader_approval_date) 
                        END) as avg_response_time')
                )
                ->whereIn('status', ['sin_aprobar', 'rechazado_lider'])
                ->first();

            // Porcentaje de documentos por estado
            $totalDocuments = array_sum($statsByStatus);
            $percentageByStatus = array_map(function ($total) use ($totalDocuments) {
                return $totalDocuments > 0 ? round(($total / $totalDocuments) * 100, 2) : 0;
            }, $statsByStatus);

            $statusLabels = DocumentRequest::getStatusOptions();

            // Obtener lista de procesos para filtros si es admin
            $processes = Auth::user()->hasRole('admin')
                ? Process::where('active', true)->get()
                : Process::where('id', Auth::user()->process_id)->get();

            return view('documents.statistics', compact(
                'statsByStatus',
                'statsByType',
                'documentsByMonth',
                'statsByProcess',
                'avgTimeByStatus',
                'leaderStats',
                'percentageByStatus',
                'statusLabels',
                'processes'
            ));
        } catch (\Exception $e) {
            Log::error('Error en statistics DocumentRequest', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'process_id' => Auth::user()->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    /**
     * Maneja el almacenamiento de archivos
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string
     */
    private function handleFileStorage($file, string $folder = 'documents'): string
    {
        try {
            // Generar nombre único para el archivo
            $fileName = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Verificar y crear el directorio si no existe
            $path = $folder . '/' . date('Y/m');
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }

            // Almacenar el archivo
            $filePath = $file->storeAs($path, $fileName, 'public');

            if (!$filePath) {
                throw new \Exception('Error al guardar el archivo');
            }

            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error en handleFileStorage', [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Elimina un archivo existente
     *
     * @param string|null $path
     * @return void
     */
    private function deleteExistingFile(?string $path): void
    {
        try {
            if ($path && Storage::disk('public')->exists($path)) {
                if (!Storage::disk('public')->delete($path)) {
                    Log::warning('No se pudo eliminar el archivo', [
                        'path' => $path,
                        'user_id' => Auth::id()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo existente', [
                'error' => $e->getMessage(),
                'path' => $path,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            // No relanzamos la excepción para evitar interrumpir el flujo
        }
    }

    /**
     * Valida los permisos del líder para un documento
     * 
     * @param DocumentRequest $documentRequest
     * @return bool
     */
    private function validateLeaderPermissions(DocumentRequest $documentRequest): bool
    {
        try {
            // Obtener el proceso del documento
            $process = $documentRequest->process;

            if (!$process) {
                Log::warning('Proceso no encontrado para el documento', [
                    'document_request_id' => $documentRequest->id,
                    'process_id' => $documentRequest->process_id
                ]);
                return false;
            }

            // Verificar si el documento está en estado pendiente de aprobación por líder principal
            if ($documentRequest->isPendingLeaderApproval()) {
                // En estado pendiente de líder principal, verificar que el usuario sea el líder principal
                if ($process->leader_id !== Auth::id()) {
                    Log::info('Usuario no es líder principal del proceso', [
                        'user_id' => Auth::id(),
                        'process_id' => $process->id,
                        'leader_id' => $process->leader_id
                    ]);
                    return false;
                }
                return true;
            }
            // Verificar si el documento está en estado pendiente de aprobación por segundo líder
            elseif ($documentRequest->isPendingSecondLeaderApproval()) {
                // En estado pendiente de segundo líder, verificar que el usuario sea el segundo líder
                if ($process->second_leader_id !== Auth::id()) {
                    Log::info('Usuario no es segundo líder del proceso', [
                        'user_id' => Auth::id(),
                        'process_id' => $process->id,
                        'second_leader_id' => $process->second_leader_id
                    ]);
                    return false;
                }
                return true;
            }

            // Si el documento no está en un estado de aprobación por líder, no tiene permisos
            return false;
        } catch (\Exception $e) {
            Log::error('Error en validateLeaderPermissions', [
                'error' => $e->getMessage(),
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Valida la transición entre estados
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @throws \Exception
     */
    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $allowedTransitions = [
            DocumentRequest::STATUS_PENDIENTE_LIDER => [
                DocumentRequest::STATUS_RECHAZADO_LIDER,
                DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER,
                DocumentRequest::STATUS_SIN_APROBAR, // Solo si no hay segundo líder
                DocumentRequest::STATUS_PUBLICADO    // Solo si no hay segundo líder y es creación
            ],
            DocumentRequest::STATUS_PENDIENTE_SEGUNDO_LIDER => [
                DocumentRequest::STATUS_RECHAZADO_LIDER,
                DocumentRequest::STATUS_SIN_APROBAR,
                DocumentRequest::STATUS_PUBLICADO // Solo si es creación
            ],
            DocumentRequest::STATUS_RECHAZADO_LIDER => [
                DocumentRequest::STATUS_PENDIENTE_LIDER
            ],
            DocumentRequest::STATUS_SIN_APROBAR => [
                DocumentRequest::STATUS_EN_ELABORACION,
                DocumentRequest::STATUS_RECHAZADO
            ],
            DocumentRequest::STATUS_EN_ELABORACION => [
                DocumentRequest::STATUS_REVISION,
                DocumentRequest::STATUS_RECHAZADO
            ],
            DocumentRequest::STATUS_REVISION => [
                DocumentRequest::STATUS_PUBLICADO,
                DocumentRequest::STATUS_EN_ELABORACION,
                DocumentRequest::STATUS_RECHAZADO,
                DocumentRequest::STATUS_PENDIENTE_LIDER // Añadida para el nuevo flujo de creación
            ],
            DocumentRequest::STATUS_RECHAZADO => [
                DocumentRequest::STATUS_PENDIENTE_LIDER
            ]
        ];

        // Verificar si la transición está permitida
        if (
            !isset($allowedTransitions[$currentStatus]) ||
            !in_array($newStatus, $allowedTransitions[$currentStatus])
        ) {
            Log::warning('Intento de transición de estado no permitida', [
                'current_status' => $currentStatus,
                'new_status' => $newStatus,
                'user_id' => Auth::id()
            ]);

            throw new \Exception('La transición de estado solicitada no está permitida.');
        }
    }

    /**
     * Verifica si un proceso tiene líder asignado
     *
     * @param int $processId
     * @return bool
     */
    private function verifyProcessHasLeader(int $processId): bool
    {
        try {
            $process = Process::find($processId);

            if (!$process) {
                Log::warning('Proceso no encontrado', [
                    'process_id' => $processId
                ]);
                return false;
            }

            if (!$process->leader_id) {
                Log::warning('Proceso sin líder asignado', [
                    'process_id' => $processId
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error al verificar líder del proceso', [
                'error' => $e->getMessage(),
                'process_id' => $processId,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function masterdocument(Request $request)
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent', 'process'])
                ->whereIn('status', [
                    DocumentRequest::STATUS_OBSOLETO,
                    DocumentRequest::STATUS_PUBLICADO,
                    DocumentRequest::STATUS_RECHAZADO
                ]);

            // Filtrar por proceso si el usuario no es admin
            if (!Auth::user()->hasRole('admin')) {
                $query->where(function ($q) {
                    $q->where('process_id', Auth::user()->process_id)
                        ->orWhere('process_id', Auth::user()->second_process_id);
                });
            } else if ($request->has('process_id') && $request->process_id != 'all') {
                // Filtro de proceso para admin (si selecciona un proceso específico)
                $query->where('process_id', $request->process_id);
            }

            // Aplicar búsqueda
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('documentType', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('user', function ($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Filtrar por tipo de documento
            if ($request->has('document_type_id') && $request->document_type_id != 'all') {
                $query->where('document_type_id', $request->document_type_id);
            }

            // Filtrar por rango de fechas
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $documentRequests = $query->latest()->paginate(10);
            $documentTypes = DocumentType::where('is_active', true)->get();
            $processes = Process::where('active', true)->get();
            $users = User::where('active', true)->get();

            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_OBSOLETO => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
            ];

            $statusLabels = DocumentRequest::getStatusOptions();

            return view('documents.masterdocument', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users',
                'documentTypes',
                'processes'
            ));
        } catch (\Exception $e) {
            Log::error('Error en masterdocument DocumentRequest', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'process_id' => Auth::user()->process_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function exportExcel()
    {
        return Excel::download(new DocumentRequestExport, 'documentMaster.xlsx');
    }

    public function toggleVisibility(DocumentRequest $documentRequest)  // Cambiado de $request a $documentRequest
    {
        try {
            $oldState = $documentRequest->is_public;

            $documentRequest->update([
                'is_public' => !$documentRequest->is_public
            ]);

            Log::info('Visibilidad del documento actualizada', [
                'document_id' => $documentRequest->id,
                'old_state' => $oldState ? 'público' : 'privado',
                'new_state' => $documentRequest->is_public ? 'público' : 'privado'
            ]);

            return redirect()->back()->with(
                'success',
                $documentRequest->is_public ?
                    'El documento ahora es público.' :
                    'El documento ahora es privado.'
            );
        } catch (\Exception $e) {
            Log::error('Error al cambiar visibilidad del documento', [
                'document_id' => $documentRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'No se pudo cambiar la visibilidad del documento.');
        }
    }

    public function getProcessLeaders(Request $request)
    {
        try {
            $processId = $request->input('process_id');

            if (!$processId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID de proceso no proporcionado'
                ], 400);
            }

            $process = Process::with(['leader', 'secondLeader'])->find($processId);

            if (!$process) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proceso no encontrado'
                ], 404);
            }

            $leaders = [];

            if ($process->leader) {
                $leaders[] = [
                    'id' => $process->leader->id,
                    'name' => $process->leader->name,
                    'role' => 'Líder Principal'
                ];
            }

            if ($process->secondLeader) {
                $leaders[] = [
                    'id' => $process->secondLeader->id,
                    'name' => $process->secondLeader->name,
                    'role' => 'Segundo Líder'
                ];
            }

            return response()->json([
                'success' => true,
                'leaders' => $leaders
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener líderes del proceso', [
                'error' => $e->getMessage(),
                'process_id' => $request->input('process_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los líderes'
            ], 500);
        }
    }
}
