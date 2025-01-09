<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Añadir leader_id a processes
        Schema::table('processes', function (Blueprint $table) {
            $table->foreignId('leader_id')
                  ->nullable()
                  ->after('name')
                  ->constrained('users')
                  ->onDelete('set null');
            
            $table->index('leader_id');
        });

        // Añadir relaciones a users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('unit_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignId('process_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignId('position_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropColumn('leader_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['process_id']);
            $table->dropForeign(['position_id']);
            $table->dropColumn(['unit_id', 'process_id', 'position_id']);
        });
    }
};