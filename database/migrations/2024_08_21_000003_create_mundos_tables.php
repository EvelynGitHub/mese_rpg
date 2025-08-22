<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mundos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->foreignId('criado_por')->constrained('usuarios');
            $table->timestamp('criado_em')->useCurrent();
        });

        // Papel por mundo: admin, mestre, jogador
        DB::statement("CREATE TYPE papel_mundo AS ENUM ('admin', 'mestre', 'jogador')");

        Schema::create('usuarios_mundos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('mundo_id')->constrained('mundos')->onDelete('cascade');
            $table->string('papel'); // Vai armazenar o ENUM papel_mundo
            $table->unique(['usuario_id', 'mundo_id', 'papel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios_mundos');
        Schema::dropIfExists('mundos');
        DB::statement('DROP TYPE IF EXISTS papel_mundo');
    }
};
