<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personagens', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->integer('idade')->nullable();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('campanha_id')->nullable()->constrained('campanhas')->nullOnDelete();
            $table->foreignId('classe_id')->constrained('classes');
            $table->foreignId('origem_id')->nullable()->constrained('origens');
            $table->integer('pontos_base')->default(0);
            $table->jsonb('pontos_base_map')->nullable(); // { atributo_id: valor }
            $table->jsonb('niveis_dado')->nullable(); // { atributo_id: nivel }
            $table->jsonb('atributos_override')->nullable(); // { atributo_id: valor }
            $table->jsonb('inventario')->nullable();
            $table->timestamp('criado_em')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personagens');
    }
};
