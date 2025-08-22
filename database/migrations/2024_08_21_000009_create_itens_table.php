<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Criar ENUM para tipos de item
        DB::statement("CREATE TYPE tipo_item AS ENUM ('arma', 'armadura', 'consumivel', 'acessorio', 'outro')");

        Schema::create('itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('slug', 120);
            $table->string('nome', 255);
            $table->string('tipo'); // Vai armazenar o ENUM tipo_item
            $table->text('descricao')->nullable();
            $table->string('dados_dano', 20)->nullable(); // ex.: '1d6', '2d8'
            $table->jsonb('propriedades')->nullable(); // alcance, critico, etc.
            $table->unique(['mundo_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens');
        DB::statement('DROP TYPE IF EXISTS tipo_item');
    }
};
