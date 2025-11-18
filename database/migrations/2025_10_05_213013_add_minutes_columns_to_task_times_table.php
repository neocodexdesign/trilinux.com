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
        Schema::table('task_times', function (Blueprint $table) {
            $table->integer('work_minutes')->default(0)->after('ended_at');
            $table->integer('pause_minutes')->default(0)->after('work_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_times', function (Blueprint $table) {
            $table->dropColumn(['work_minutes', 'pause_minutes']);
        });
    }
};
