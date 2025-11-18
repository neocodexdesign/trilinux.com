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
            $table->foreignId('user_id')->after('task_id')->constrained()->onDelete('cascade');
            $table->string('computer_id')->after('user_id')->nullable();
            $table->timestamp('ended_at')->after('resumed_at')->nullable();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_times', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'computer_id', 'ended_at']);
        });
    }
};
