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
        // Projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'paused', 'completed', 'cancelled'])->default('planned');
            $table->timestamp('expected_start_at')->nullable();
            $table->timestamp('expected_end_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
        });

        // Stages table
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'paused', 'completed', 'cancelled'])->default('planned');
            $table->timestamp('expected_start_at')->nullable();
            $table->timestamp('expected_end_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('dependent_stage_id')->nullable();
            $table->unsignedBigInteger('responsible_id')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('project_id');
        });

        // Tasks table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('stage_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'paused', 'completed', 'cancelled'])->default('planned');
            $table->timestamp('expected_start_at')->nullable();
            $table->timestamp('expected_end_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('dependent_task_id')->nullable();
            $table->unsignedBigInteger('responsible_id')->nullable();
            $table->integer('order')->default(0);
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('stage_id');
        });

        // Task Times table (for time tracking)
        Schema::create('task_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->enum('type', ['work', 'pause'])->default('work');
            $table->timestamps();

            $table->index('task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_times');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('stages');
        Schema::dropIfExists('projects');
    }
};
