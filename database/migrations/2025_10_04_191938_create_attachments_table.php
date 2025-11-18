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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relationship - can belong to Project, Stage, or Task
            $table->morphs('attachable');

            $table->string('filename'); // Original filename
            $table->string('stored_filename'); // Unique stored filename
            $table->string('mime_type');
            $table->bigInteger('size'); // File size in bytes
            $table->string('extension', 10);
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
