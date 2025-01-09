<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id(); // Clave primaria autoincremental

            // Campos básicos
            $table->enum('request_type', [
                'create',
                'modify',
                'obsolete'
            ]);
            $table->string('document_name'); // Nombre del documento
            $table->text('description')->nullable(); // Descripción del documento
            $table->string('origin'); // Origen del documento
            $table->string('destination')->default('Calidad'); // Destino del documento

            // Nuevo campo para público/privado
            $table->boolean('is_public')->default(false); // false = privado, true = público

            // Rutas de archivos
            $table->string('document_path')->nullable(); // Ruta del documento original
            $table->string('final_document_path')->nullable(); // Ruta del documento final

            // Estado y versión
            $table->enum('status', [
                'pendiente_lider',    // Esperando aprobación del líder
                'rechazado_lider',    // Rechazado por el líder
                'sin_aprobar',        // Sin aprobar por calidad
                'en_elaboracion',     // En proceso de elaboración
                'revision',           // En revisión
                'publicado',          // Documento publicado
                'rechazado',          // Documento rechazado
                'obsoleto'
            ]);
            $table->integer('version')->default(1); // Versión del documento

            // Campos para observaciones
            $table->text('observations')->nullable(); // Observaciones generales
            $table->text('leader_observations')->nullable(); // Observaciones del líder
            $table->timestamp('leader_approval_date')->nullable(); // Fecha de aprobación del líder

            // Relaciones con otras tablas
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // Usuario que crea la solicitud

            $table->foreignId('document_type_id')
                ->constrained()
                ->onDelete('cascade'); // Tipo de documento

            $table->foreignId('assigned_agent_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Agente asignado

            $table->foreignId('responsible_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->where('email', 'LIKE', 'lider.calidad@%')
                ->onDelete('set null'); // Responsable de calidad

            $table->foreignId('process_id')
                ->constrained()
                ->onDelete('cascade'); // Proceso al que pertenece

            $table->foreignId('reference_document_id')
                ->nullable()
                ->constrained('document_requests')
                ->onDelete('set null'); // Referencia al documento original en caso de modificación u obsoletización

            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
