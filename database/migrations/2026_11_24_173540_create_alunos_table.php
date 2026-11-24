<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {
            $table->integer('user_id')->primary();
            $table->string('numero', 20);
            $table->integer('curso_id');
            $table->integer('ano');
            $table->string('curriculo')->nullable();

            $table->foreign('user_id', 'fk_alunos_user')->references('id')->on('users');
            $table->foreign('curso_id', 'fk_alunos_curso')->references('id')->on('cursos');
            $table->index('curso_id', 'ix_alunos_curso');
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE alunos ADD CONSTRAINT ck_alunos_ano CHECK (ano BETWEEN 1 AND 2);
        CREATE UNIQUE INDEX uq_alunos_numero ON alunos (lower(numero));
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
