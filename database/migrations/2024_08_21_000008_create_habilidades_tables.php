<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('slug', 120);
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->jsonb('bonus')->nullable(); // buffs e debuffs
            $table->boolean('ativa')->default(true);
            $table->unique(['mundo_id', 'slug']);
        });

        // vínculos
        Schema::create('classes_habilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('habilidade_id')->constrained('habilidades')->onDelete('cascade');
            $table->unique(['classe_id', 'habilidade_id']);
        });

        Schema::create('origens_habilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origem_id')->constrained('origens')->onDelete('cascade');
            $table->foreignId('habilidade_id')->constrained('habilidades')->onDelete('cascade');
            $table->unique(['origem_id', 'habilidade_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('origens_habilidades');
        Schema::dropIfExists('classes_habilidades');
        Schema::dropIfExists('habilidades');
    }
};
