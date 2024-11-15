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
            $users = User::where('active', true)->get();

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

    public function create()
    {
        try {
            $documentTypes = DocumentType::where('is_active', true)->get();
            $users = User::where('active', true)->get();
            
            return view('document-requests.create', compact('documentTypes', 'users'));
        } catch (\Exception $e) {
            Log::error('Error en create DocumentRequest', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', self::MESSAGE_ERROR_GENERIC);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'request_type' => 'required|in:create,modify',
            'document_type_id' => 'required|exists:document_types,id',
            'document_name' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                $file = $request->file('document');
                $fileName = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $fileName, 'public');

                $documentRequest = DocumentRequest::create([
                    'request_type' => $validated['request_type'],
                    'user_id' => Auth::id(),
                    'origin' => Auth::user()->department ?? 'No especificado',
                    'destination' => 'Calidad',
                    'document_type_id' => $validated['document_type_id'],
                    'document_name' => $validated['document_name'],
                    'document_path' => $path,
                    'responsible_id' => Auth::id(),
                    'assigned_agent_id' => null,
                    'status' => DocumentRequest::STATUS_SIN_APROBAR,
                    'description' => $validated['description'],
                    'observations' => null,
                ]);

                DB::commit();

                Log::info('DocumentRequest creado exitosamente', [
                    'id' => $documentRequest->id,
                    'user_id' => Auth::id(),
                ]);

                return redirect()
                    ->route('documents.requests.index')
                    ->with('success', self::MESSAGE_SUCCESS_CREATE);
            }

            throw new \Exception('No se pudo procesar el archivo adjunto.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Error al crear DocumentRequest', [
                'error' => $e->getMessage(),
                'user' => Auth::id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
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
            'responsible_id' => 'required|exists:users,id',
            'status' => 'required|in:sin_aprobar,en_elaboracion,revision,publicado,rechazado',
            'description' => 'nullable|string',
            'observations' => 'nullable|string',
            'document' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
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

            return redirect()
                ->route('documents.requests.index')
                ->with('success', self::MESSAGE_SUCCESS_UPDATE);

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Error al actualizar DocumentRequest', [
                'error' => $e->getMessage(),
                'documentRequest' => $documentRequest->id
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', self::MESSAGE_ERROR_GENERIC);
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
            'final_document' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx',
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
       $fileName = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
       return $file->storeAs('documents', $fileName, 'public');
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