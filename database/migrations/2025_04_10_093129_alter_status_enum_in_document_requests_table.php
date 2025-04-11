<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ⚠️ Solo funciona si usas MySQL. Si usas PostgreSQL, el enfoque cambia.
        DB::statement("ALTER TABLE document_requests MODIFY status ENUM(
            'pendiente_lider',
            'pendiente_segundo_lider',
            'rechazado_lider',
            'sin_aprobar',
            'en_elaboracion',
            'revision',
            'publicado',
            'rechazado',
            'obsoleto'
        ) NOT NULL");
    }

    public function down(): void
    {
        // Revertir el cambio si es necesario
        DB::statement("ALTER TABLE document_requests MODIFY status ENUM(
            'pendiente_lider',
            'rechazado_lider',
            'sin_aprobar',
            'en_elaboracion',
            'revision',
            'publicado',
            'rechazado',
            'obsoleto'
        ) NOT NULL");
    }
};
