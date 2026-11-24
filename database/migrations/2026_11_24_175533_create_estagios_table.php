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
        // necessário para impedir estágios sobrepostos
        DB::unprepared(<<<'SQL'
        CREATE SCHEMA IF NOT EXISTS extensions;
        CREATE EXTENSION IF NOT EXISTS btree_gist WITH SCHEMA extensions;
        SQL);

        Schema::create('estagios', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('candidatura_id')->unique('uq_estagios_candidatura');
            $table->integer('aluno_id');
            $table->integer('professor_id')->nullable();
            $table->integer('professor_atribuido_por')->nullable();
            $table->timestamp('data_atribuicao_professor', 6)->nullable();
            $table->integer('monitor_id')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->string('estado', 20)->default('por_iniciar');

            $table->foreign(['candidatura_id', 'aluno_id'], 'fk_estagios_candidatura')
                ->references(['id', 'aluno_id'])->on('candidaturas');
            $table->foreign('aluno_id', 'fk_estagios_aluno')->references('user_id')->on('alunos');
            $table->foreign('professor_id', 'fk_estagios_professor')->references('user_id')->on('professores');
            $table->foreign('professor_atribuido_por', 'fk_estagios_atribuido_por')->references('id')->on('users');
            $table->foreign('monitor_id', 'fk_estagios_monitor')->references('user_id')->on('monitores');

            $table->index(['aluno_id', 'data_inicio'], 'ix_estagios_aluno');
            $table->index('professor_id', 'ix_estagios_professor');
            $table->index('professor_atribuido_por', 'ix_estagios_atribuido_por');
            $table->index('monitor_id', 'ix_estagios_monitor');
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE estagios
          ADD CONSTRAINT ck_estagios_estado CHECK (estado IN ('por_iniciar','em_curso','concluido','encerrado')),
          ADD CONSTRAINT ck_estagios_datas CHECK (
            data_fim IS NULL OR data_inicio IS NULL OR data_fim >= data_inicio
          ),
          ADD CONSTRAINT ck_estagios_datas_estado CHECK (
            (estado NOT IN ('em_curso','concluido') OR data_inicio IS NOT NULL)
            AND (estado <> 'concluido' OR data_fim IS NOT NULL)
          ),
          ADD CONSTRAINT ck_estagios_atribuicao_professor CHECK (
            (professor_id IS NULL AND professor_atribuido_por IS NULL AND data_atribuicao_professor IS NULL)
            OR (professor_id IS NOT NULL AND professor_atribuido_por IS NOT NULL AND data_atribuicao_professor IS NOT NULL)
          ),
          ADD CONSTRAINT ck_estagios_datas_orientador CHECK (
            (data_inicio IS NULL AND data_fim IS NULL) OR professor_id IS NOT NULL
          ),
          ADD CONSTRAINT ex_estagios_sobreposicao EXCLUDE USING gist (
            aluno_id extensions.gist_int4_ops WITH =,
            daterange(data_inicio, data_fim, '[]') WITH &&
          ) WHERE (estado <> 'encerrado' AND data_inicio IS NOT NULL);
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estagios');
    }
};
