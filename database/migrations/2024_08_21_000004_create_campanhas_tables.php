<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->foreignId('criado_por')->constrained('usuarios');
            $table->timestamp('criado_em')->useCurrent();
        });

        Schema::create('sessoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade');
            $table->timestamp('data_hora');
            $table->text('resumo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessoes');
        Schema::dropIfExists('campanhas');
    }
};
