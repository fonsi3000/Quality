<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'second_process_id')) {
                $table->foreignId('second_process_id')
                    ->nullable()
                    ->constrained('processes')
                    ->nullOnDelete()
                    ->after('process_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'second_process_id')) {
                $table->dropForeign(['second_process_id']);
                $table->dropColumn('second_process_id');
            }
        });
    }
};
