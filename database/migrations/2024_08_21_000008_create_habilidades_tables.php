<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('habilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('slug', 120);
            $table->string('nome', 255);
            $table->string('tipo_habilidade', 20); // ativa/passiva/utilitaria
            $table->text('descricao')->nullable();
            $table->integer('duracao')->nullable();
            $table->integer('qtd_uso_sessao')->nullable();
            $table->jsonb('bonus')->nullable(); // buffs e debuffs
            $table->boolean('ativa')->default(true);
            $table->unique(['mundo_id', 'slug']);
        });

        /*
        tipo_habilidade:
            - ativa: requer teste solicitado pelo mestre (define os valores) para ver se acerta a habilidade
            - passiva: fica ativa enquanto as limitações permitirem (duracao|qtd_uso_sessao)
            - utilitaria: requer teste, mas trava no alvo
        bonus:
        [
            {
                "tipo_efeito": "delta_atributo_base|delta_atributo_dado|efeito_instantaneo", // Delta de atibuto = afetar pontos base do atributo
                "atributo_chave": "forca|inteligencia|etc", // Atributo que sofrerá o efeito
                "delta": "", // valor em que o atributo sofrerá o efeito
                "alvo": "inimigos|aliados|jogador" // Em quem esse efeito vai ser aplicado
            }
        ]
         */

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
