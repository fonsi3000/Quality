<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

    // Nuevas constantes para mensajes de líder
    private const MESSAGE_SUCCESS_LEADER_APPROVE = 'Solicitud aprobada por el líder exitosamente.';
    private const MESSAGE_SUCCESS_LEADER_REJECT = 'Solicitud rechazada por el líder.';
    private const MESSAGE_ERROR_LEADER_PERMISSION = 'No tiene permisos de líder para realizar esta acción.';
    private const MESSAGE_ERROR_LEADER_PROCESS = 'El proceso no tiene un líder asignado.';
    private const MESSAGE_ERROR_INVALID_STATUS = 'Estado no válido para esta acción.';

    public function index(Request $request)
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'process']);

            // Filtrar documentos publicados
            $query->where('status', '!=', DocumentRequest::STATUS_PUBLICADO);

            // Filtrar por usuario regular
            if (Auth::user()->hasRole('user')) {
                $query->where('user_id', Auth::id());
            }

            // Filtrar por líder de proceso
            if (Auth::user()->hasRole('leader')) {
                $query->where('process_id', Auth::user()->process_id);
            }

            // Aplicar búsqueda
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhereHas('documentType', function($q) use ($searchTerm) {
                          $q->where('name', 'like', "%{$searchTerm}%");
                      })
                      ->orWhereHas('user', function($q) use ($searchTerm) {
                          $q->where('name', 'like', "%{$searchTerm}%");
                      });
                });
            }

            // Filtrar por estado
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $documentRequests = $query->latest()->paginate(10);
            
            $users = User::where('active', true)
                ->whereHas('roles', function($query) {
                    $query->whereIn('name', ['admin', 'agent']);
                })
                ->get();

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

            return view('document-requests.index', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users'
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
            Log::info('Iniciando creación de solicitud de documento', [
                'user_id' => Auth::id(),
                'user_process' => Auth::user()->process_id ?? 'No tiene proceso'
            ]);

            $documentTypes = DocumentType::where('is_active', true)->get();
            Log::info('Tipos de documentos obtenidos', [
                'count' => $documentTypes->count()
            ]);

            // Obtener documentos publicados con filtro de proceso
            $query = DocumentRequest::with(['documentType'])
                ->where('status', DocumentRequest::STATUS_PUBLICADO);

            // Si es usuario regular, filtrar por proceso
            if (Auth::user()->hasRole('user')) {
                $query->whereHas('user', function($q) {
                    $q->where('process_id', Auth::user()->process_id);
                });
            }

            $publishedDocuments = $query->get();
            
            Log::info('Documentos publicados obtenidos', [
                'count' => $publishedDocuments->count(),
                'process_id' => Auth::user()->process_id ?? 'N/A'
            ]);
            
            return view('document-requests.create', compact('documentTypes', 'publishedDocuments'));

        } catch (\Exception $e) {
            Log::error('Error en create DocumentRequest', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function store(Request $request)
    {
        Log::info('Iniciando store de DocumentRequest', [
            'request_data' => $request->except('document'),
            'has_file' => $request->hasFile('document'),
            'user_id' => Auth::id()
        ]);

        try {
            DB::beginTransaction();

            // 1. Validación inicial
            $validated = $request->validate([
                'request_type' => 'required|in:create,modify',
                'document' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx',
                'description' => 'required|string',
                'document_type_id' => $request->request_type === 'create' ? 'required|exists:document_types,id' : 'nullable',
                'document_name' => $request->request_type === 'create' ? 'required|string|max:255' : 'nullable',
                'existing_document_id' => $request->request_type === 'modify' ? 'required|exists:document_requests,id' : 'nullable',
            ]);

            Log::info('Validación inicial exitosa', ['validated_data' => $validated]);

            // 2. Verificación de proceso y líder
            $userProcess = Auth::user()->process;
            if (!$userProcess) {
                throw new \Exception('Usuario no tiene un proceso asignado.');
            }

            if (!$userProcess->leader_id) {
                Log::warning('Proceso sin líder asignado', [
                    'process_id' => $userProcess->id,
                    'user_id' => Auth::id()
                ]);
                throw new \Exception(self::MESSAGE_ERROR_LEADER_PROCESS);
            }

            // 3. Manejo del archivo
            if (!$request->hasFile('document')) {
                throw new \Exception('No se ha proporcionado ningún archivo');
            }

            $path = $this->handleFileStorage($request->file('document'));
            Log::info('Archivo almacenado correctamente', ['path' => $path]);

            // 4. Preparación de datos
            $documentData = [
                'request_type' => $validated['request_type'],
                'user_id' => Auth::id(),
                'document_path' => $path,
                'description' => $validated['description'],
                'status' => DocumentRequest::STATUS_PENDIENTE_LIDER,
                'process_id' => $userProcess->id,
                'origin' => $userProcess->name,
                'version' => '1.0', // Agregar versión inicial
            ];

            // 5. Procesamiento según tipo de solicitud
            if ($validated['request_type'] === 'modify') {
                $existingDocument = DocumentRequest::findOrFail($validated['existing_document_id']);
                
                if ($existingDocument->status !== DocumentRequest::STATUS_PUBLICADO) {
                    throw new \Exception('El documento seleccionado no está disponible para modificación');
                }

                // Incrementar versión para modificación
                $newVersion = floatval($existingDocument->version) + 0.1;
                
                $documentData = array_merge($documentData, [
                    'document_type_id' => $existingDocument->document_type_id,
                    'document_name' => $existingDocument->document_name,
                    'reference_document_id' => $existingDocument->id,
                    'version' => number_format($newVersion, 1)
                ]);
            } else {
                $documentData = array_merge($documentData, [
                    'document_type_id' => $validated['document_type_id'],
                    'document_name' => $validated['document_name'],
                ]);
            }

            // 6. Crear el registro
            $documentRequest = DocumentRequest::create($documentData);

            DB::commit();

            Log::info('Solicitud de documento creada exitosamente', [
                'document_request_id' => $documentRequest->id,
                'type' => $validated['request_type'],
                'user_id' => Auth::id(),
                'process_id' => $userProcess->id
            ]);

            return redirect()
                ->route('documents.requests.index')
                ->with('success', self::MESSAGE_SUCCESS_CREATE);

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                Log::info('Archivo temporal eliminado', ['path' => $path]);
            }

            Log::error('Error al crear solicitud de documento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'request_data' => $request->except('document')
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
                        'leader_approval_date' => $documentRequest->leader_approval_date ? 
                            $documentRequest->leader_approval_date->format('d/m/Y H:i') : null,
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
            // Verificar si el usuario puede editar el documento
            if (!$documentRequest->canBeEdited()) {
                return redirect()->back()->with('error', 'No se puede editar el documento en su estado actual.');
            }

            $documentTypes = DocumentType::where('is_active', true)->get();
            $users = User::where('active', true)->get();

            return view('document-requests.edit', compact('documentRequest', 'documentTypes', 'users'));
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
            'document' => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx'
        ]);

        try {
            DB::beginTransaction();

            // Verificar si el documento puede ser editado
            if (!$documentRequest->canBeEdited()) {
                throw new \Exception('No se puede editar el documento en su estado actual.');
            }

            // Manejar el archivo si se proporciona uno nuevo
            if ($request->hasFile('document')) {
                // Eliminar documento anterior si existe
                if ($documentRequest->document_path) {
                    Storage::disk('public')->delete($documentRequest->document_path);
                }

                $path = $this->handleFileStorage($request->file('document'));
                $validated['document_path'] = $path;
            }

            // Si el documento estaba aprobado o en otro estado avanzado
            // volver a estado pendiente de líder
            if (in_array($documentRequest->status, [
                DocumentRequest::STATUS_SIN_APROBAR,
                DocumentRequest::STATUS_EN_ELABORACION,
                DocumentRequest::STATUS_REVISION,
                DocumentRequest::STATUS_PUBLICADO
            ])) {
                $validated['status'] = DocumentRequest::STATUS_PENDIENTE_LIDER;
                $validated['leader_approval_date'] = null;
            }

            $documentRequest->update($validated);

            DB::commit();

            Log::info('Solicitud de documento actualizada exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'new_status' => $documentRequest->status
            ]);

            return redirect()
                ->route('documents.requests.index')
                ->with('success', self::MESSAGE_SUCCESS_UPDATE);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Eliminar el nuevo archivo si se subió y hubo error
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
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

    public function destroy(DocumentRequest $documentRequest)
    {
        try {
            DB::beginTransaction();

            // Verificar si el usuario tiene permisos para eliminar
            if (!Auth::user()->hasRole(['admin']) && 
                $documentRequest->user_id !== Auth::id()) {
                throw new \Exception('No tiene permisos para eliminar esta solicitud.');
            }

            // Verificar el estado del documento
            if (in_array($documentRequest->status, [
                DocumentRequest::STATUS_EN_ELABORACION,
                DocumentRequest::STATUS_REVISION,
                DocumentRequest::STATUS_PUBLICADO
            ])) {
                throw new \Exception('No se puede eliminar el documento en su estado actual.');
            }

            // Eliminar archivos asociados si existen
            if ($documentRequest->document_path) {
                Storage::disk('public')->delete($documentRequest->document_path);
            }

            if ($documentRequest->final_document_path) {
                Storage::disk('public')->delete($documentRequest->final_document_path);
            }

            // Eliminar el registro
            $documentRequest->delete();

            DB::commit();

            Log::info('Solicitud de documento eliminada exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('documents.requests.index')
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
            if (!$documentRequest->document_path || !Storage::disk('public')->exists($documentRequest->document_path)) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }

            return Storage::disk('public')->download(
                $documentRequest->document_path,
                $documentRequest->document_name . '.' . pathinfo($documentRequest->document_path, PATHINFO_EXTENSION)
            );

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
            
            return response()->file($path, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . $documentRequest->document_name . '"'
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
            if (!$documentRequest->final_document_path || 
                !Storage::disk('public')->exists($documentRequest->final_document_path)) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }

            return Storage::disk('public')->download(
                $documentRequest->final_document_path,
                'final_' . $documentRequest->document_name . '.' . 
                pathinfo($documentRequest->final_document_path, PATHINFO_EXTENSION)
            );

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
            if (!$documentRequest->final_document_path || 
                !Storage::disk('public')->exists($documentRequest->final_document_path)) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }

            $path = Storage::disk('public')->path($documentRequest->final_document_path);
            $contentType = mime_content_type($path);
            
            return response()->file($path, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="final_' . $documentRequest->document_name . '"'
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
            'final_document' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx',
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
                ->whereHas('roles', function($query) {
                    $query->whereIn('name', ['admin', 'agent']);
                })
                ->first();

            if (!$assignedUser) {
                throw new \Exception('El usuario asignado debe tener rol de administrador o agente.');
            }

            // Verificar si el agente pertenece al mismo proceso o es admin
            if (!$assignedUser->hasRole('admin') && 
                $assignedUser->process_id !== $documentRequest->process_id) {
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

            $documentRequest->update([
                'status' => DocumentRequest::STATUS_PUBLICADO,
                'observations' => $validated['observations'] ?? $documentRequest->observations,
                'responsible_id' => Auth::id()
            ]);

            DB::commit();

            Log::info('Documento aprobado exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'observations' => $validated['observations'] ?? null
            ]);

            return redirect()
                ->back()
                ->with('success', 'Documento aprobado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al aprobar documento', [
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

    private function validateStatusTransition($currentStatus, $newStatus)
    {
        $allowedTransitions = [
            DocumentRequest::STATUS_PENDIENTE_LIDER => [
                DocumentRequest::STATUS_RECHAZADO_LIDER,
                DocumentRequest::STATUS_SIN_APROBAR
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
                DocumentRequest::STATUS_RECHAZADO
            ],
            DocumentRequest::STATUS_RECHAZADO => [
                DocumentRequest::STATUS_PENDIENTE_LIDER
            ]
        ];

        if (!isset($allowedTransitions[$currentStatus]) || 
            !in_array($newStatus, $allowedTransitions[$currentStatus])) {
            throw new \Exception('La transición de estado no está permitida.');
        }
    }

    public function pendingLeaderApproval()
    {
        try {
            // Obtener los procesos donde el usuario actual es líder
            $userProcessesAsLeader = Process::where('leader_id', Auth::id())->pluck('id');
            
            if ($userProcessesAsLeader->isEmpty()) {
                return redirect()
                    ->route('documents.requests.index')
                    ->with('error', 'No tiene permisos de líder en ningún proceso.');
            }

            $query = DocumentRequest::with(['user', 'documentType', 'process'])
                ->where('status', DocumentRequest::STATUS_PENDIENTE_LIDER)
                ->whereIn('process_id', $userProcessesAsLeader);

            $documentRequests = $query->latest()->paginate(10);

            $statusClasses = [
                DocumentRequest::STATUS_PENDIENTE_LIDER => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                DocumentRequest::STATUS_RECHAZADO_LIDER => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            return view('document-requests.pending-leader', compact(
                'documentRequests',
                'statusClasses'
            ));

        } catch (\Exception $e) {
            Log::error('Error en pendingLeaderApproval', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('documents.requests.index')
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

            $documentRequest->update([
                'status' => DocumentRequest::STATUS_SIN_APROBAR,
                'leader_observations' => $validated['observations'],
                'leader_approval_date' => now()
            ]);

            DB::commit();

            Log::info('Documento aprobado por líder exitosamente', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id,
                'observations' => $validated['observations'] ?? null
            ]);

            return redirect()
                ->back()
                ->with('success', self::MESSAGE_SUCCESS_LEADER_APPROVE);

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
            // Validar que el documento esté pendiente de aprobación
            if ($documentRequest->status !== DocumentRequest::STATUS_PENDIENTE_LIDER) {
                throw new \Exception('El documento no está pendiente de aprobación del líder.');
            }

            // Validar que el usuario sea el líder del proceso
            if (!$this->validateLeaderPermissions($documentRequest)) {
                throw new \Exception(self::MESSAGE_ERROR_LEADER_PERMISSION);
            }

            $validated = $request->validate([
                'observations' => 'required|string|max:1000',
            ]);

            DB::beginTransaction();

            $documentRequest->update([
                'status' => DocumentRequest::STATUS_RECHAZADO_LIDER,
                'leader_observations' => $validated['observations'],
                'leader_approval_date' => now()
            ]);

            DB::commit();

            Log::info('Documento rechazado por líder', [
                'document_request_id' => $documentRequest->id,
                'user_id' => Auth::id(),
                'process_id' => $documentRequest->process_id,
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
     * Valida que el usuario actual sea el líder del proceso al que pertenece el documento
     */
    private function validateLeaderPermissions(DocumentRequest $documentRequest): bool
    {
        // Obtener el proceso del documento
        $process = Process::find($documentRequest->process_id);
        
        if (!$process) {
            return false;
        }

        // Verificar si el usuario actual es el líder del proceso
        return $process->leader_id === Auth::id();
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
                ->whereHas('roles', function($query) {
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
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent', 'process'])
                ->where('status', DocumentRequest::STATUS_PUBLICADO);

            // Filtrar por proceso si el usuario no es admin
            if (!Auth::user()->hasRole('admin')) {
                $query->where('process_id', Auth::user()->process_id);
            }

            // Aplicar búsqueda
            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('documentType', function($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('user', function($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            // Filtrar por tipo de documento
            if ($request->has('document_type_id') && $request->document_type_id != 'all') {
                $query->where('document_type_id', $request->document_type_id);
            }

            $documentRequests = $query->latest()->paginate(10);
            $documentTypes = DocumentType::where('is_active', true)->get();
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

            return view('documents.published', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users',
                'documentTypes'
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
                $query->where('process_id', Auth::user()->process_id);
            }

            // Aplicar búsqueda general
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('documentType', function($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('user', function($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('process', function($q) use ($searchTerm) {
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
                ->select('status', 
                    DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
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
            $percentageByStatus = array_map(function($total) use ($totalDocuments) {
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

            // Verificar si el usuario actual es el líder del proceso
            if ($process->leader_id !== Auth::id()) {
                Log::info('Usuario no es líder del proceso', [
                    'user_id' => Auth::id(),
                    'process_id' => $process->id,
                    'leader_id' => $process->leader_id
                ]);
                return false;
            }

            return true;

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
                DocumentRequest::STATUS_SIN_APROBAR
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
                DocumentRequest::STATUS_RECHAZADO
            ],
            DocumentRequest::STATUS_RECHAZADO => [
                DocumentRequest::STATUS_PENDIENTE_LIDER
            ]
        ];

        // Verificar si la transición está permitida
        if (!isset($allowedTransitions[$currentStatus]) || 
            !in_array($newStatus, $allowedTransitions[$currentStatus])) {
                
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

}


/**
    *controller viejo
*/

<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Process;


class DocumentRequestController extends Controller
{
    private const MESSAGE_SUCCESS_CREATE = 'Solicitud de documento creada exitosamente.';
    private const MESSAGE_SUCCESS_UPDATE = 'Solicitud actualizada exitosamente.';
    private const MESSAGE_SUCCESS_DELETE = 'Solicitud eliminada exitosamente.';
    private const MESSAGE_SUCCESS_ASSIGN = 'Responsable asignado y solicitud aprobada exitosamente.';
    private const MESSAGE_SUCCESS_REJECT = 'Solicitud rechazada exitosamente.';
    private const MESSAGE_ERROR_FILE = 'El archivo no se encuentra disponible.';
    private const MESSAGE_ERROR_GENERIC = 'Ha ocurrido un error. Por favor, intente nuevamente.';
    private const MESSAGE_SUCCESS_FINAL_DOCUMENT = 'Documento final adjuntado exitosamente.';

    public function index(Request $request)
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible']);

            // Filter out published documents
            $query->where('status', '!=', DocumentRequest::STATUS_PUBLICADO);

            // Filter for regular users to see only their requests
            if (Auth::user()->hasRole('user')) {
                $query->where('user_id', Auth::id());
            }

            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhereHas('documentType', function($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('user', function($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
                });
            }

            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $documentRequests = $query->latest()->paginate(10);
            $users = User::where('active', true)
            ->whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'agent']);
            })
            ->get();

            $statusClasses = [
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            $statusLabels = DocumentRequest::getStatusOptions();

            return view('document-requests.index', compact(
                'documentRequests',
                'statusClasses',
                'statusLabels',
                'users'
            ));
        } catch (\Exception $e) {
            Log::error('Error en index DocumentRequest', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function inProgress()
    {
        try {
            $documentRequests = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent'])
                ->where('status', DocumentRequest::STATUS_EN_ELABORACION)
                ->latest()
                ->paginate(10);

            $users = User::where('active', true)->get();

            $statusClasses = [
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
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    // En el controlador:
    public function create()
    {
        try {
            $documentTypes = DocumentType::where('is_active', true)->get();
            $users = User::where('active', true)->get();
            
            $publishedDocuments = DocumentRequest::with(['documentType'])
                ->where('status', DocumentRequest::STATUS_PUBLICADO)
                ->where(function($query) {
                    if (Auth::user()->hasRole('user')) {
                        $query->whereHas('user', function($q) {
                            $q->where('process_id', Auth::user()->process_id);
                        });
                    }
                })
                ->get();
            
            return view('document-requests.create', compact('documentTypes', 'users', 'publishedDocuments'));
        } catch (\Exception $e) {
            Log::error('Error en create DocumentRequest', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function store(Request $request)
    {
        // 1. Validación mejorada
        $validated = $request->validate([
            'request_type' => 'required|in:create,modify',
            'document' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx',
            'description' => 'required|string',
            'document_type_id' => $request->request_type === 'create' ? 'required|exists:document_types,id' : 'nullable',
            'document_name' => $request->request_type === 'create' ? 'required|string|max:255' : 'nullable',
            'existing_document_id' => $request->request_type === 'modify' ? 'required|exists:document_requests,id' : 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // 2. Verificar y procesar el archivo
            if (!$request->hasFile('document')) {
                throw new \Exception('No se ha proporcionado ningún archivo');
            }

            $path = $this->handleFileStorage($request->file('document'));

            // 3. Preparar datos base
            $documentData = [
                'request_type' => $validated['request_type'],
                'user_id' => Auth::id(),
                'document_path' => $path,
                'description' => $validated['description'],
                'status' => DocumentRequest::STATUS_SIN_APROBAR,
            ];

            // 4. Procesar según el tipo de solicitud
            if ($validated['request_type'] === 'modify') {
                $existingDocument = DocumentRequest::findOrFail($validated['existing_document_id']);
                
                // Verificar que el documento existe y está publicado
                if (!$existingDocument || $existingDocument->status !== DocumentRequest::STATUS_PUBLICADO) {
                    throw new \Exception('El documento seleccionado no está disponible para modificación');
                }

                $documentData = array_merge($documentData, [
                    'document_type_id' => $existingDocument->document_type_id,
                    'document_name' => $existingDocument->document_name,
                    'reference_document_id' => $existingDocument->id,
                    'process_id' => Auth::user()->process_id // Agregar process_id si es necesario
                ]);
            } else {
                $documentData = array_merge($documentData, [
                    'document_type_id' => $validated['document_type_id'],
                    'document_name' => $validated['document_name'],
                    'process_id' => Auth::user()->process_id // Agregar process_id si es necesario
                ]);
            }

            // 5. Crear la solicitud
            $documentRequest = DocumentRequest::create($documentData);

            // 6. Confirmar la transacción
            DB::commit();

            // 7. Registrar el éxito en los logs
            Log::info('Solicitud de documento creada exitosamente', [
                'document_request_id' => $documentRequest->id,
                'type' => $validated['request_type'],
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('documents.requests.index')
                ->with('success', self::MESSAGE_SUCCESS_CREATE);

        } catch (\Exception $e) {
            DB::rollBack();

            // Si se subió un archivo, eliminarlo
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Error al crear solicitud de documento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
            $documentRequest->load(['user', 'documentType', 'responsible']);
            
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
                        'user' => [
                            'id' => $documentRequest->user->id,
                            'name' => $documentRequest->user->name
                        ],
                        'responsible' => [
                            'id' => $documentRequest->responsible->id,
                            'name' => $documentRequest->responsible->name
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
            $documentTypes = DocumentType::where('is_active', true)->get();
            $users = User::where('active', true)->get(); 
            return view('document-requests.edit', compact('documentRequest', 'documentTypes', 'users'));
        } catch (\Exception $e) {
            Log::error('Error en edit DocumentRequest', [
                'error' => $e->getMessage(),
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
            'document' => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx'
        ]);
     
        try {
            DB::beginTransaction();
     
            if ($request->hasFile('document')) {
                if ($documentRequest->document_path) {
                    Storage::disk('public')->delete($documentRequest->document_path);
                }
     
                $file = $request->file('document');
                $fileName = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $fileName, 'public');
                $validated['document_path'] = $path;
            }
     
            $documentRequest->update($validated);
     
            DB::commit();
            return redirect()->route('documents.requests.index')->with('success', self::MESSAGE_SUCCESS_UPDATE);
     
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
     
            Log::error('Error al actualizar DocumentRequest', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id
            ]);
     
            return redirect()->back()->withInput()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
     }

    public function destroy(DocumentRequest $documentRequest)
    {
        try {
            DB::beginTransaction();

            if ($documentRequest->document_path) {
                Storage::disk('public')->delete($documentRequest->document_path);
            }

            if ($documentRequest->final_document_path) {
                Storage::disk('public')->delete($documentRequest->final_document_path);
            }

            $documentRequest->delete();

            DB::commit();

            return redirect()
                ->route('documents.requests.index')
                ->with('success', self::MESSAGE_SUCCESS_DELETE);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar DocumentRequest', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id
            ]);

            return redirect()
                ->back()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function downloadDocument(DocumentRequest $documentRequest)
    {
        try {
            if (!$documentRequest->document_path || !Storage::disk('public')->exists($documentRequest->document_path)) {
                throw new \Exception(self::MESSAGE_ERROR_FILE);
            }

            return Storage::disk('public')->download(
                $documentRequest->document_path,
                $documentRequest->document_name . '.' . pathinfo($documentRequest->document_path, PATHINFO_EXTENSION)
            );

        } catch (\Exception $e) {
            Log::error('Error al descargar documento', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al descargar el documento: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, DocumentRequest $documentRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:sin_aprobar,en_elaboracion,revision,publicado,rechazado',
            'observations' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $documentRequest->update([
                'status' => $validated['status'],
                'observations' => $validated['observations']
            ]);

            DB::commit();

            Log::info('Estado de documento actualizado exitosamente', [
                'document_request_id' => $documentRequest->id,
                'old_status' => $documentRequest->getOriginal('status'),
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
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
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
            
            return response()->file($path, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'inline; filename="' . $documentRequest->document_name . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al previsualizar documento', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al previsualizar el documento: ' . $e->getMessage());
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
                'observations' => $validated['observations']
            ]);

            $documentRequest->status = DocumentRequest::STATUS_RECHAZADO;
            $documentRequest->observations = $validated['observations'];
            $documentRequest->save();

            DB::commit();

            Log::info('Solicitud rechazada exitosamente', [
                'request_id' => $documentRequest->id,
                'new_status' => $documentRequest->status,
                'observations' => $documentRequest->observations
            ]);

            return redirect()
                ->back()
                ->with('success', self::MESSAGE_SUCCESS_REJECT);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al rechazar solicitud', [
                'error' => $e->getMessage(),
                'request_id' => $documentRequest->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

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

            // Verify the assigned user has the correct role
            $assignedUser = User::whereId($validated['assigned_agent_id'])
                ->whereHas('roles', function($query) {
                    $query->whereIn('name', ['admin', 'agent']);
                })
                ->first();

            if (!$assignedUser) {
                throw new \Exception('El usuario asignado debe tener rol de administrador o agente.');
            }

            $documentRequest->update([
                'assigned_agent_id' => $validated['assigned_agent_id'],
                'status' => DocumentRequest::STATUS_EN_ELABORACION,
            ]);

            DB::commit();

            Log::info('Solicitud asignada exitosamente', [
                'request_id' => $documentRequest->id,
                'assigned_agent_id' => $validated['assigned_agent_id']
            ]);

            return redirect()
                ->back()
                ->with('success', 'Agente asignado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al asignar agente', [
                'error' => $e->getMessage(),
                'request_id' => $documentRequest->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: 'Error al asignar el agente.');
        }
    }

    public function attachFinalDocument(Request $request, DocumentRequest $documentRequest)
    {
        $validated = $request->validate([
            'final_document' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx',
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

            if ($request->hasFile('final_document')) {
                $file = $request->file('final_document');
                $fileName = 'final_' . Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents/final', $fileName, 'public');

                $documentRequest->update([
                    'final_document_path' => $path,
                    'status' => DocumentRequest::STATUS_REVISION,
                    'observations' => $validated['observations']
                ]);
            }

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
                'document_request_id' => $documentRequest->id
            ]);

            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?: self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function downloadFinalDocument(DocumentRequest $documentRequest)
   {
       try {
           if (!$documentRequest->final_document_path || 
               !Storage::disk('public')->exists($documentRequest->final_document_path)) {
               throw new \Exception(self::MESSAGE_ERROR_FILE);
           }

           return Storage::disk('public')->download(
               $documentRequest->final_document_path,
               'final_' . $documentRequest->document_name . '.' . 
               pathinfo($documentRequest->final_document_path, PATHINFO_EXTENSION)
           );

       } catch (\Exception $e) {
           Log::error('Error al descargar documento final', [
               'error' => $e->getMessage(),
               'documentRequest' => $documentRequest->id
           ]);

           return redirect()
               ->back()
               ->with('error', 'Error al descargar el documento: ' . $e->getMessage());
       }
   }

   public function previewFinalDocument(DocumentRequest $documentRequest)
   {
       try {
           if (!$documentRequest->final_document_path || 
               !Storage::disk('public')->exists($documentRequest->final_document_path)) {
               throw new \Exception(self::MESSAGE_ERROR_FILE);
           }

           $path = Storage::disk('public')->path($documentRequest->final_document_path);
           $contentType = mime_content_type($path);
           
           return response()->file($path, [
               'Content-Type' => $contentType,
               'Content-Disposition' => 'inline; filename="final_' . $documentRequest->document_name . '"'
           ]);

       } catch (\Exception $e) {
           Log::error('Error al previsualizar documento final', [
               'error' => $e->getMessage(),
               'documentRequest' => $documentRequest->id
           ]);

           return redirect()
               ->back()
               ->with('error', 'Error al previsualizar el documento: ' . $e->getMessage());
       }
   }

   private function handleFileStorage($file): string 
    {
        try {
            // Log inicial con información del archivo
            Log::info('Iniciando almacenamiento de archivo', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'user_id' => Auth::id()
            ]);

            // Validar el archivo antes de procesar
            if (!$file->isValid()) {
                throw new \Exception('El archivo no es válido o está corrupto');
            }

            // Generar nombre único para el archivo
            $fileName = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Crear estructura de directorios por año/mes
            $path = 'documents/' . date('Y/m');

            // Verificar y crear el directorio si no existe
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
                Log::info('Directorio creado', [
                    'path' => $path,
                    'user_id' => Auth::id()
                ]);
            }

            // Almacenar el archivo
            $filePath = $file->storeAs($path, $fileName, 'public');

            if (!$filePath) {
                throw new \Exception('Error al guardar el archivo');
            }

            // Verificar que el archivo se haya guardado correctamente
            if (!Storage::disk('public')->exists($filePath)) {
                throw new \Exception('El archivo no se guardó correctamente');
            }

            Log::info('Archivo almacenado exitosamente', [
                'path' => $filePath,
                'file_name' => $fileName,
                'user_id' => Auth::id(),
                'size' => $file->getSize()
            ]);

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

   private function deleteExistingFile($path): void
   {
       if ($path && Storage::disk('public')->exists($path)) {
           Storage::disk('public')->delete($path);
       }
   }

   public function inReview()
   {
       try {
           $documentRequests = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent'])
               ->where('status', DocumentRequest::STATUS_REVISION)
               ->latest()
               ->paginate(10);

           $users = User::where('active', true)->get();

           $statusClasses = [
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
               'error' => $e->getMessage()
           ]);

           return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
       }
   }

   public function published(Request $request)
    {
        try {
            $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent'])
                ->where('status', DocumentRequest::STATUS_PUBLICADO);

            // Filtrar por el proceso del usuario 
            if (Auth::user()->hasRole('user')) {
                $userProcessId = Auth::user()->process_id;
                $query->whereHas('user', function($q) use ($userProcessId) {
                    $q->where('process_id', $userProcessId);
                });
            }

            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('document_name', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%")
                        ->orWhereHas('documentType', function($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        })
                        ->orWhereHas('user', function($q) use ($searchTerm) {
                            $q->where('name', 'like', "%{$searchTerm}%");
                        });
                });
            }

            if ($request->has('document_type_id') && $request->document_type_id != 'all') {
                $query->where('document_type_id', $request->document_type_id);
            }

            $documentRequests = $query->latest()->paginate(10);
            $users = User::where('active', true)->get();
            $documentTypes = DocumentType::where('is_active', true)->get();

            $statusClasses = [
                DocumentRequest::STATUS_SIN_APROBAR => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                DocumentRequest::STATUS_EN_ELABORACION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                DocumentRequest::STATUS_REVISION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                DocumentRequest::STATUS_PUBLICADO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                DocumentRequest::STATUS_RECHAZADO => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            ];

            $statusLabels = DocumentRequest::getStatusOptions();

            return view('documents.published', compact(
                'documentRequests', 
                'statusClasses',
                'statusLabels',
                'users',
                'documentTypes'
            ));
        } catch (\Exception $e) {
            Log::error('Error en published DocumentRequest', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
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

           $documentRequest->update([
               'status' => DocumentRequest::STATUS_PUBLICADO,
               'observations' => $validated['observations'] ?? $documentRequest->observations,
           ]);

           DB::commit();

           Log::info('Documento aprobado exitosamente', [
               'document_request_id' => $documentRequest->id,
               'user_id' => Auth::id(),
           ]);

           return redirect()
               ->back()
               ->with('success', 'Documento aprobado exitosamente.');

       } catch (\Exception $e) {
           DB::rollBack();

           Log::error('Error al aprobar documento', [
               'error' => $e->getMessage(),
               'document_request_id' => $documentRequest->id
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

           $documentRequest->update([
               'status' => DocumentRequest::STATUS_EN_ELABORACION,
               'observations' => $validated['observations'],
           ]);

           DB::commit();

           Log::info('Documento devuelto a elaboración exitosamente', [
               'document_request_id' => $documentRequest->id,
               'user_id' => Auth::id(),
           ]);

           return redirect()
               ->back()
               ->with('success', 'Documento devuelto a elaboración exitosamente.');

       } catch (\Exception $e) {
           DB::rollBack();

           Log::error('Error al devolver documento a elaboración', [
               'error' => $e->getMessage(),
               'document_request_id' => $documentRequest->id
           ]);

           return redirect()
               ->back()
               ->with('error', self::MESSAGE_ERROR_GENERIC);
       }
   }

   public function search(Request $request)
   {
       try {
           $query = DocumentRequest::with(['user', 'documentType', 'responsible', 'assignedAgent']);

           // Aplicar filtros de búsqueda
           if ($request->filled('search')) {
               $searchTerm = $request->search;
               $query->where(function($q) use ($searchTerm) {
                   $q->where('document_name', 'like', "%{$searchTerm}%")
                     ->orWhere('description', 'like', "%{$searchTerm}%")
                     ->orWhereHas('documentType', function($q) use ($searchTerm) {
                         $q->where('name', 'like', "%{$searchTerm}%");
                     })
                     ->orWhereHas('user', function($q) use ($searchTerm) {
                         $q->where('name', 'like', "%{$searchTerm}%");
                     });
               });
           }

           // Filtrar por estado si se especifica
           if ($request->has('status') && $request->status != 'all') {
               $query->where('status', $request->status);
           }

           // Filtrar por tipo de documento si se especifica
           if ($request->has('document_type_id') && $request->document_type_id != 'all') {
               $query->where('document_type_id', $request->document_type_id);
           }

           // Filtrar por rango de fechas si se especifica
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
           $statusLabels = DocumentRequest::getStatusOptions();

           $statusClasses = [
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
               'statusLabels',
               'statusClasses'
           ));

       } catch (\Exception $e) {
           Log::error('Error en search DocumentRequest', [
               'error' => $e->getMessage()
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
           // Estadísticas por estado
           $statsByStatus = DocumentRequest::select('status', DB::raw('count(*) as total'))
               ->groupBy('status')
               ->get()
               ->pluck('total', 'status')
               ->toArray();

           // Estadísticas por tipo de documento
           $statsByType = DocumentRequest::select('document_type_id', DB::raw('count(*) as total'))
               ->groupBy('document_type_id')
               ->with('documentType')
               ->get()
               ->mapWithKeys(function ($item) {
                   return [$item->documentType->name => $item->total];
               })
               ->toArray();

           // Documentos por mes (últimos 12 meses)
           $documentsByMonth = DocumentRequest::select(
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

           $statusLabels = DocumentRequest::getStatusOptions();

           return view('documents.statistics', compact(
               'statsByStatus',
               'statsByType',
               'documentsByMonth',
               'statusLabels'
           ));

       } catch (\Exception $e) {
           Log::error('Error en statistics DocumentRequest', [
               'error' => $e->getMessage()
           ]);

           return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
       }
   }
   
}



