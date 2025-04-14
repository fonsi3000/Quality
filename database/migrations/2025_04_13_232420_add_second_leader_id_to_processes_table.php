<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            if (!Schema::hasColumn('processes', 'second_leader_id')) {
                $table->foreignId('second_leader_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->after('leader_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('processes', function (Blueprint $table) {
            if (Schema::hasColumn('processes', 'second_leader_id')) {
                $table->dropForeign(['second_leader_id']);
                $table->dropColumn('second_leader_id');
            }
        });
    }
};
