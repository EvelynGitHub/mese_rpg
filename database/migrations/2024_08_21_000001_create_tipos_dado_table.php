<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_dado', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->integer('faces');
            $table->timestamps();
        });

        // Inserir dados iniciais
        DB::table('tipos_dado')->insert([
            ['codigo' => 'd4', 'faces' => 4],
            ['codigo' => 'd6', 'faces' => 6],
            ['codigo' => 'd8', 'faces' => 8],
            ['codigo' => 'd10', 'faces' => 10],
            ['codigo' => 'd12', 'faces' => 12],
            ['codigo' => 'd20', 'faces' => 20],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_dado');
    }
};
