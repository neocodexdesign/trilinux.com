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
        // Adicionar team_id em projects
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('created_by')->constrained()->onDelete('set null');
        });

        // Adicionar team_id em stages
        Schema::table('stages', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('responsible_id')->constrained()->onDelete('set null');
        });

        // Adicionar team_id em tasks
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('responsible_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });

        Schema::table('stages', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
