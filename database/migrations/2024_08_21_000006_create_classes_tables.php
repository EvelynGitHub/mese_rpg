<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('slug', 120);
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->unique(['mundo_id', 'slug']);
        });

        Schema::create('classes_atributos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('atributo_id')->constrained('atributos')->onDelete('cascade');
            $table->foreignId('tipo_dado_id')->nullable()->constrained('tipos_dado');
            $table->integer('base_fixa')->default(0);
            $table->integer('limite_base_fixa')->nullable();
            $table->foreignId('limite_tipo_dado_id')->nullable()->constrained('tipos_dado');
            $table->boolean('imutavel')->default(true);
            $table->unique(['classe_id', 'atributo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes_atributos');
        Schema::dropIfExists('classes');
    }
};
