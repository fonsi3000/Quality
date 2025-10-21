<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('third_process_id')
                ->nullable()
                ->constrained('processes')
                ->nullOnDelete()
                ->after('second_process_id');
            $table->foreignId('fourth_process_id')
                ->nullable()
                ->constrained('processes')
                ->nullOnDelete()
                ->after('third_process_id');
            $table->foreignId('fifth_process_id')
                ->nullable()
                ->constrained('processes')
                ->nullOnDelete()
                ->after('fourth_process_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['third_process_id']);
            $table->dropColumn('third_process_id');
            $table->dropForeign(['fourth_process_id']);
            $table->dropColumn('fourth_process_id');
            $table->dropForeign(['fifth_process_id']);
            $table->dropColumn('fifth_process_id');
        });
    }
};
