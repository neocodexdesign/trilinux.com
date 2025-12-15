<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table
                ->foreignId('source_note_id')
                ->nullable()
                ->after('team_id')
                ->constrained('notes')
                ->nullOnDelete();

            $table->string('source')->nullable()->after('source_note_id')->index();
            $table->string('source_key')->nullable()->after('source')->index();

            $table->unique(['stage_id', 'source_note_id', 'source', 'source_key'], 'tasks_markdown_source_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropUnique('tasks_markdown_source_unique');
            $table->dropConstrainedForeignId('source_note_id');
            $table->dropColumn(['source', 'source_key']);
        });
    }
};

