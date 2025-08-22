<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Criar ENUM para tipos de efeito
        DB::statement("CREATE TYPE tipo_efeito_origem AS ENUM ('delta_atributo', 'conceder_item', 'conceder_habilidade', 'custom')");

        Schema::create('origens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('slug', 120);
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->unique(['mundo_id', 'slug']);
        });

        Schema::create('origens_efeitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origem_id')->constrained('origens')->onDelete('cascade');
            $table->string('tipo'); // Vai armazenar o ENUM tipo_efeito_origem
            $table->foreignId('atributo_id')->nullable()->constrained('atributos');
            $table->integer('delta')->nullable();
            $table->jsonb('notas')->nullable(); // para item/habilidade/custom
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('origens_efeitos');
        Schema::dropIfExists('origens');
        DB::statement('DROP TYPE IF EXISTS tipo_efeito_origem');
    }
};
