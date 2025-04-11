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
        Schema::table('document_requests', function (Blueprint $table) {
            $table->text('second_leader_observations')->nullable()->after('leader_observations');
            $table->timestamp('second_leader_approval_date')->nullable()->after('leader_approval_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn('second_leader_observations');
            $table->dropColumn('second_leader_approval_date');
        });
    }
};
