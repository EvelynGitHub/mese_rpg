<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atributos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('chave', 100);
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->boolean('exibir')->default(true);
            $table->unique(['mundo_id', 'chave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atributos');
    }
};
