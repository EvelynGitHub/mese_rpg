<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mundos_regras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->integer('pontos_base_por_personagem')->default(0); // X
            $table->integer('niveis_dado_por_personagem')->default(0); // Y
            // $table->json('sequencia_dados')->default('[4,6,8,10,12,20]'); // faces ordenadas
            $table->json('sequencia_dados')->nullable(); // Remova o default
            $table->foreignId('limite_max_tipo_dado_id')->nullable()->constrained('tipos_dado');
            $table->boolean('permite_pvp')->default(false);
            $table->boolean('permite_pve')->default(true);
            $table->integer('limite_inicial_habilidades')->default(1); // O personagem começa com 1 habilidade
            $table->integer('limite_final_habilidades'); //
            $table->unique('mundo_id');
        });

        // Adicione esta instrução para definir o valor padrão após a criação da tabela
        // DB::statement("ALTER TABLE mundos_regras ALTER COLUMN sequencia_dados SET DEFAULT '[4,6,8,10,12,20]'");

        // Tabela de auditoria para trilha de alterações
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->string('evento');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('mundo_id')->constrained('mundos');
            $table->jsonb('payload_before')->nullable();
            $table->jsonb('payload_after')->nullable();
            $table->timestamp('criado_em')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
        Schema::dropIfExists('mundos_regras');
    }
};
