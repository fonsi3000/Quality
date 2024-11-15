<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id(); // Identificador único de la solicitud de documento
            $table->enum('request_type', ['create', 'modify']); // Campo de enumeración para el tipo de solicitud
            $table->foreignId('user_id') // Clave foránea que referencia la tabla 'users'
                ->constrained() // Agrega la restricción de clave foránea
                ->onDelete('cascade'); // Elimina las solicitudes asociadas si se elimina el usuario
            $table->string('origin'); // Campo de texto para el origen de la solicitud
            $table->string('destination')->default('Calidad'); // Campo de texto para el destino, con el valor predeterminado 'Calidad'
            $table->foreignId('document_type_id') // Clave foránea que referencia la tabla 'document_types'
                ->constrained() // Agrega la restricción de clave foránea
                ->onDelete('cascade'); // Elimina las solicitudes asociadas si se elimina el tipo de documento
            $table->string('document_name'); // Campo de texto para el nombre del documento
            $table->string('document_path')->nullable(); // Campo de texto nullable para la ruta del documento inicial
            $table->string('final_document_path')->nullable(); // Campo de texto nullable para la ruta del documento final
            $table->foreignId('assigned_agent_id') // Clave foránea que referencia la tabla 'users'
                ->nullable() // Permite que el campo pueda ser nulo
                ->constrained('users') // Agrega la restricción de clave foránea a la tabla 'users'
                ->onDelete('set null'); // Establece el campo en nulo si se elimina el agente asignado
            $table->foreignId('responsible_id') // Clave foránea que referencia la tabla 'users'
                ->nullable() // Permite que el campo pueda ser nulo
                ->constrained('users') // Agrega la restricción de clave foránea a la tabla 'users'
                ->onDelete('set null'); // Establece el campo en nulo si se elimina el usuario responsable
            $table->enum('status', [ // Campo de enumeración para el estado de la solicitud
                'sin_aprobar',
                'en_elaboracion',
                'revision',
                'publicado',
                'rechazado'
            ])->default('sin_aprobar'); // Establece el estado predeterminado como 'sin_aprobar'
            $table->text('description')->nullable(); // Campo de texto nullable para la descripción de la solicitud
            $table->text('observations')->nullable(); // Campo de texto nullable para observaciones adicionales
            $table->integer('version')->default(1); // Campo de entero para la versión del documento, con valor predeterminado 1
            $table->timestamps(); // Agrega automáticamente los campos 'created_at' y 'updated_at'
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};