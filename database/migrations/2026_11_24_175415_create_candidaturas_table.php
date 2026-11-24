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
        Schema::create('candidaturas', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('aluno_id');
            $table->integer('oferta_id');
            $table->string('estado', 20)->default('submetida');
            $table->date('data_candidatura')->default(DB::raw("((now() at time zone 'Europe/Lisbon')::date)"));
            $table->timestamp('data_aceitacao_empresa', 6)->nullable();
            $table->timestamp('prazo_resposta', 6)->nullable()
                ->storedAs("data_aceitacao_empresa + interval '15 days'");
            $table->timestamp('data_resposta_aluno', 6)->nullable();

            $table->unique(['aluno_id', 'oferta_id'], 'uq_candidatura_aluno_oferta');
            $table->unique(['id', 'aluno_id'], 'uq_candidaturas_id_aluno');
            $table->foreign('aluno_id', 'fk_candidaturas_aluno')->references('user_id')->on('alunos');
            $table->foreign('oferta_id', 'fk_candidaturas_oferta')->references('id')->on('ofertas');

            $table->index('oferta_id', 'ix_candidaturas_oferta');
            $table->index(['estado', 'prazo_resposta'], 'ix_candidaturas_estado');
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE candidaturas
          ADD CONSTRAINT ck_candidaturas_estado CHECK (estado IN (
            'submetida','em_analise','aceite_empresa','aceite',
            'rejeitada_empresa','recusada_aluno','expirada','cancelada'
          )),
          ADD CONSTRAINT ck_candidaturas_aceitacao_empresa CHECK (
            estado NOT IN ('aceite_empresa','aceite','recusada_aluno','expirada')
            OR data_aceitacao_empresa IS NOT NULL
          ),
          ADD CONSTRAINT ck_candidaturas_resposta_aluno CHECK (
            (estado IN ('aceite','recusada_aluno') AND data_resposta_aluno IS NOT NULL)
            OR (estado NOT IN ('aceite','recusada_aluno') AND data_resposta_aluno IS NULL)
          ),
          ADD CONSTRAINT ck_candidaturas_prazo CHECK (
            data_resposta_aluno IS NULL
            OR data_resposta_aluno BETWEEN data_aceitacao_empresa
                                       AND data_aceitacao_empresa + INTERVAL '15 days'
          );
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidaturas');
    }
};
