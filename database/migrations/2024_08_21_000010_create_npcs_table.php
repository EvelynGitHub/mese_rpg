<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->foreignId('classe_id')->nullable()->constrained('classes');
            $table->foreignId('origem_id')->nullable()->constrained('origens');
            $table->jsonb('atributos')->nullable(); // opcional, chave->valor
            $table->jsonb('inventario')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npcs');
    }
};
