<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('request_type', ['create', 'modify']);
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('origin');
            $table->string('destination')->default('Calidad');
            $table->foreignId('document_type_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('document_name');
            $table->string('document_path')->nullable();
            $table->string('final_document_path')->nullable();
            $table->foreignId('assigned_agent_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('responsible_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->where('email', 'LIKE', 'lider.calidad@%')
                ->onDelete('set null');
            $table->enum('status', [
                'sin_aprobar',
                'en_elaboracion',
                'revision',
                'publicado',
                'rechazado'
            ])->default('sin_aprobar');
            $table->text('description')->nullable();
            $table->text('observations')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};