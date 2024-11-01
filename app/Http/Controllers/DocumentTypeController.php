<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::latest()->paginate(10);
        return view('document-types.index', compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('document-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:document_types',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            DocumentType::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true
            ]);

            DB::commit();

            return redirect()
                ->route('document-types.index')
                ->with('success', 'Tipo de documento creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear el tipo de documento. Por favor, intente nuevamente.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $documentType = DocumentType::findOrFail($id);
        return view('document-types.edit', compact('documentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $documentType = DocumentType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $documentType->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true
            ]);

            DB::commit();

            return redirect()
                ->route('document-types.index')
                ->with('success', 'Tipo de documento actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el tipo de documento. Por favor, intente nuevamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $documentType = DocumentType::findOrFail($id);
            $documentType->delete();

            return redirect()
                ->route('document-types.index')
                ->with('success', 'Tipo de documento eliminado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el tipo de documento.');
        }
    }
}