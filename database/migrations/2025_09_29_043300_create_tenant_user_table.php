<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_user', function (Blueprint $table) {
            $table->id();

            // user FK normal
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // tenant_id como STRING + FK para tenants.id (string na v3)
            $table->string('tenant_id');
            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->cascadeOnDelete();

            // papel opcional no vínculo (se não usar Spatie teams, isso já resolve)
            $table->string('role')->nullable(); // 'owner','admin','manager','operator', etc.

            $table->timestamps();

            // Evita membership duplicado para o mesmo usuário no mesmo tenant
            $table->unique(['tenant_id', 'user_id']);

            // Índices úteis para queries
            $table->index('tenant_id');
            $table->index('user_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_user');
    }
};