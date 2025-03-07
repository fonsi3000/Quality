@extends('layouts.app')

@section('title', 'Tipos de Documentos')

@section('content')
<div class="flex flex-col">
  <!-- Main Container -->
  <div class="overflow-x-auto min-h-[631px]">
    <div class="min-w-full inline-block align-middle">
      <!-- DataTable Configuration -->
      <div data-hs-datatable='{
        "pageLength": 10,
        "pagingOptions": {
          "pageBtnClasses": "min-w-[40px] flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:focus:bg-neutral-700 dark:hover:bg-neutral-700"
        },
        "selecting": true,
        "rowSelectingOptions": {
          "selectAllSelector": "#hs-table-search-checkbox-all"
        },
        "language": {
          "zeroRecords": "<div class=\"py-10 px-5 flex flex-col justify-center items-center text-center\"><svg class=\"shrink-0 size-6 text-gray-500 dark:text-neutral-500\" xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"11\" cy=\"11\" r=\"8\"/><path d=\"m21 21-4.3-4.3\"/></svg><div class=\"max-w-sm mx-auto\"><p class=\"mt-2 text-sm text-gray-600 dark:text-neutral-400\">No se encontraron resultados</p></div></div>"
        }
      }'>
        <!-- Search and New Document Button -->
        <div class="py-3 flex justify-between items-center">
          <!-- Search Input -->
          <div class="relative max-w-xs">
            <label for="hs-table-input-search" class="sr-only">Buscar</label>
            <input type="text" 
                   name="hs-table-search" 
                   id="hs-table-input-search" 
                   class="py-2 px-3 ps-9 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" 
                   placeholder="Buscar tipos de documentos" 
                   data-hs-datatable-search="">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
              <svg class="size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
              </svg>
            </div>
          </div>
          
          <!-- New Document Type Button -->
          <a href="{{ route('document-types.create') }}" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
              <path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/>
              <line x1="12" y1="11" x2="12" y2="17"/>
              <line x1="9" y1="14" x2="15" y2="14"/>
            </svg>
            Nuevo Tipo de Documento
          </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table Container -->
        <div class="overflow-hidden min-h-[509px]">
          <table class="min-w-full">
            <!-- Table Header -->
            <thead class="border-y border-gray-200 dark:border-neutral-700">
              <tr>
                <!-- Checkbox Header -->
                <th scope="col" class="py-1 px-3 pe-0">
                  <div class="flex items-center h-5">
                    <input id="hs-table-search-checkbox-all" type="checkbox" class="border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                    <label for="hs-table-search-checkbox-all" class="sr-only">Checkbox</label>
                  </div>
                </th>

                <!-- Name Header -->
                <th scope="col" class="py-1 group text-start font-normal">
                  <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200 dark:text-neutral-500 dark:hover:border-neutral-700">
                    Nombre
                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path class="hs-datatable-ordering-asc:text-blue-600 dark:hs-datatable-ordering-asc:text-blue-500" d="m7 15 5 5 5-5"/>
                      <path class="hs-datatable-ordering-desc:text-blue-600 dark:hs-datatable-ordering-desc:text-blue-500" d="m7 9 5-5 5 5"/>
                    </svg>
                  </div>
                </th>

                <!-- Description Header -->
                <th scope="col" class="py-1 group text-start font-normal">
                  <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200 dark:text-neutral-500 dark:hover:border-neutral-700">
                    Descripción
                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path class="hs-datatable-ordering-asc:text-blue-600 dark:hs-datatable-ordering-asc:text-blue-500" d="m7 15 5 5 5-5"/>
                      <path class="hs-datatable-ordering-desc:text-blue-600 dark:hs-datatable-ordering-desc:text-blue-500" d="m7 9 5-5 5 5"/>
                    </svg>
                  </div>
                </th>

                <!-- Status Header -->
                <th scope="col" class="py-1 group text-start font-normal">
                  <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200 dark:text-neutral-500 dark:hover:border-neutral-700">
                    Estado
                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path class="hs-datatable-ordering-asc:text-blue-600 dark:hs-datatable-ordering-asc:text-blue-500" d="m7 15 5 5 5-5"/>
                      <path class="hs-datatable-ordering-desc:text-blue-600 dark:hs-datatable-ordering-desc:text-blue-500" d="m7 9 5-5 5 5"/>
                    </svg>
                  </div>
                </th>

                <!-- Actions Header -->
                <th scope="col" class="py-2 px-3 text-end font-normal text-sm text-gray-500 dark:text-neutral-500">Acciones</th>
              </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
              @foreach($documentTypes as $documentType)
                <tr>
                  <!-- Checkbox -->
                  <td class="py-3 ps-3">
                    <div class="flex items-center h-5">
                      <input id="hs-table-search-checkbox-{{ $documentType->id }}" type="checkbox" class="border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" data-hs-datatable-row-selecting-individual="">
                      <label for="hs-table-search-checkbox-{{ $documentType->id }}" class="sr-only">Checkbox</label>
                    </div>
                  </td>

                  <!-- Name -->
                  <td class="p-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                    {{ $documentType->name }}
                  </td>

                  <!-- Description -->
                  <td class="p-3 text-sm text-gray-800 dark:text-neutral-200">
                    {{ Str::limit($documentType->description, 50) }}
                  </td>

                  <!-- Status -->
                  <td class="p-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                    <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $documentType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                      @if($documentType->is_active)
                        <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/>
                          <path d="m9 12 2 2 4-4"/>
                        </svg>
                        Activo
                      @else
                        <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"/>
                          <line x1="15" x2="9" y1="9" y2="15"/>
                          <line x1="9" x2="15" y1="9" y2="15"/>
                        </svg>
                        Inactivo
                      @endif
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="p-3 whitespace-nowrap text-end text-sm font-medium">
                    <div class="flex items-center justify-end gap-2">
                      <!-- View Details -->
                      <a href="#" data-hs-overlay="#document-type-modal-{{ $documentType->id }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                          <circle cx="12" cy="12" r="3"/>
                        </svg>
                      </a>
                      
                      <!-- Edit -->
                      <a href="{{ route('document-types.edit', $documentType) }}" class="text-blue-500 hover:text-blue-700">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                          <path d="m15 5 4 4"/>
                        </svg>
                      </a>

                      <!-- Delete -->
                      <form action="{{ route('document-types.destroy', $documentType) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700" 
                        onclick="return confirm('¿Estás seguro de eliminar este tipo de documento?')">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 6h18"/>
                          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                          <line x1="10" x2="10" y1="11" y2="17"/>
                          <line x1="14" x2="14" y1="11" y2="17"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="py-1 px-4" data-hs-datatable-paging="">
        <nav class="flex items-center space-x-1">
          <button type="button" class="p-2.5 min-w-[40px] inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-hs-datatable-paging-prev="">
            <span aria-hidden="true">«</span>
            <span class="sr-only">Anterior</span>
          </button>
          <div class="flex items-center space-x-1 [&>.active]:bg-gray-100 dark:[&>.active]:bg-neutral-700" data-hs-datatable-paging-pages=""></div>
          <button type="button" class="p-2.5 min-w-[40px] inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" data-hs-datatable-paging-next="">
            <span class="sr-only">Siguiente</span>
            <span aria-hidden="true">»</span>
          </button>
        </nav>
      </div>
    </div>
  </div>
</div>
</div>

<!-- Document Type Detail Modals -->
@foreach($documentTypes as $documentType)
<div id="document-type-modal-{{ $documentType->id }}" class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto">
<div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)]">
  <div class="relative flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
    <!-- Modal Header -->
    <div class="py-3 px-4 border-b dark:border-neutral-700">
      <h3 class="font-bold text-gray-800 dark:text-white">
        Detalles del Tipo de Documento
      </h3>
    </div>

    <!-- Modal Body -->
    <div class="p-4 overflow-y-auto">
      <div class="space-y-4">
        <!-- Basic Information -->
        <div>
          <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-2">
            Información Básica
          </h4>
          <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700 space-y-3">
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Nombre:</span>
              <span class="text-sm text-gray-800 dark:text-gray-200">{{ $documentType->name }}</span>
            </div>
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Estado:</span>
              <span class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-xs font-medium {{ $documentType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                @if($documentType->is_active)
                  <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/>
                    <path d="m9 12 2 2 4-4"/>
                  </svg>
                  Activo
                @else
                  <svg class="size-2.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" x2="9" y1="9" y2="15"/>
                    <line x1="9" x2="15" y1="9" y2="15"/>
                  </svg>
                  Inactivo
                @endif
              </span>
            </div>
          </div>
        </div>

        <!-- Description Section -->
        <div>
          <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-2">
            Descripción
          </h4>
          <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700">
            <p class="text-sm text-gray-800 dark:text-gray-200">
              {{ $documentType->description ?: 'Sin descripción' }}
            </p>
          </div>
        </div>

        <!-- Additional Information -->
        <div>
          <h4 class="text-base font-semibold text-gray-800 dark:text-gray-200 mb-2">
            Información Adicional
          </h4>
          <div class="bg-gray-50 rounded-lg p-4 dark:bg-neutral-700 space-y-3">
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Creado:</span>
              <span class="text-sm text-gray-800 dark:text-gray-200">{{ $documentType->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex items-center">
              <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Actualizado:</span>
              <span class="text-sm text-gray-800 dark:text-gray-200">{{ $documentType->updated_at->format('d/m/Y H:i') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t dark:border-neutral-700">
      <button type="button" 
              class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
              data-hs-overlay="#document-type-modal-{{ $documentType->id }}">
        Cerrar
      </button>
    </div>
  </div>
</div>
</div>
@endforeach

@endsection