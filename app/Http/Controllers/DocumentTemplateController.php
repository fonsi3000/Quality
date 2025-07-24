<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $templates = DocumentTemplate::latest()
                ->get();

            return view('document-templates.index', compact('templates'));
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al cargar las plantillas. Por favor, intente nuevamente.'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('document-templates.create');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al cargar el formulario. Por favor, intente nuevamente.'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_templates', 'name')
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => [
                'required',
                'file',
                'max:102400', // 50MB máximo
            ],
        ]);

        try {
            DB::beginTransaction();

            // Manejo del archivo
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                
                // Generar nombre único para el archivo
                $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . 
                           '_' . 
                           time() . 
                           '.' . 
                           $extension;
                
                // Guardar archivo
                $path = $file->storeAs('templates', $fileName, 'public');
                
                // Crear registro en la base de datos
                DocumentTemplate::create([
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'file_path' => $path,
                    'is_active' => true,
                ]);

                DB::commit();

                return redirect()
                    ->route('document-templates.index')
                    ->with('success', 'Plantilla creada exitosamente.');
            }

            throw new \Exception('No se ha proporcionado ningún archivo.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Si se subió un archivo, eliminarlo
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear la plantilla: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);
            return view('document-templates.edit', compact('template'));
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al cargar el formulario de edición. Por favor, intente nuevamente.'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $template = DocumentTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_templates', 'name')->ignore($id)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => [
                'nullable',
                'file',
                'max:102400', // 50MB máximo
            ],
            'is_active' => ['boolean']
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'is_active' => $request->has('is_active'),
            ];

            // Manejo del archivo si se proporciona uno nuevo
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                
                // Generar nombre único para el archivo
                $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . 
                           '_' . 
                           time() . 
                           '.' . 
                           $extension;
                
                // Guardar nuevo archivo
                $path = $file->storeAs('templates', $fileName, 'public');
                
                // Eliminar archivo anterior si existe
                if ($template->file_path) {
                    Storage::disk('public')->delete($template->file_path);
                }

                $updateData['file_path'] = $path;
            }

            // Actualizar registro
            $template->update($updateData);

            DB::commit();

            return redirect()
                ->route('document-templates.index')
                ->with('success', 'Plantilla actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Si se subió un archivo nuevo, eliminarlo
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar la plantilla: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $template = DocumentTemplate::findOrFail($id);

            // Eliminar archivo físico
            if ($template->file_path) {
                Storage::disk('public')->delete($template->file_path);
            }

            // Eliminar registro
            $template->delete();

            DB::commit();

            return redirect()
                ->route('document-templates.index')
                ->with('success', 'Plantilla eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Error al eliminar la plantilla. Por favor, intente nuevamente.'
            ]);
        }
    }

    /**
     * Download the specified template file.
     */
    public function download(string $id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);
            
            if (!Storage::disk('public')->exists($template->file_path)) {
                throw new \Exception('El archivo no existe.');
            }

            return Storage::disk('public')->download(
                $template->file_path, 
                $template->name . '.' . pathinfo($template->file_path, PATHINFO_EXTENSION)
            );

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al descargar el archivo: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Preview the specified template file.
     */
    public function preview(string $id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);
            
            if (!Storage::disk('public')->exists($template->file_path)) {
                throw new \Exception('El archivo no existe.');
            }

            $file = Storage::disk('public')->get($template->file_path);
            $mimeType = Storage::disk('public')->mimeType($template->file_path);
            
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($template->file_path) . '"',
                'Cache-Control' => 'public, max-age=3600',
            ];

            // Para archivos que no son PDF o imágenes, forzar descarga
            if (!in_array($mimeType, [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/bmp',
                'image/svg+xml',
            ])) {
                $headers['Content-Disposition'] = 'attachment; filename="' . basename($template->file_path) . '"';
            }

            return response($file, 200, $headers);
            
        } catch (\Exception $e) {
            \Log::error('Error previewing template: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Error al abrir el archivo. Por favor, intente nuevamente.'
            ]);
        }
    }
}