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
        Schema::create('avaliacoes_finais', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('estagio_id');
            $table->integer('avaliador_id');
            $table->string('tipo_avaliador', 20);
            $table->decimal('nota', 4, 2);
            $table->text('comentario')->nullable();
            $table->timestamp('created_at', 6)->default(DB::raw("(now() at time zone 'utc')"));

            $table->unique(['estagio_id', 'tipo_avaliador'], 'uq_avaliacao_estagio_tipo');
            $table->foreign('estagio_id', 'fk_avaliacoes_finais_estagio')->references('id')->on('estagios');
            $table->foreign('avaliador_id', 'fk_avaliacoes_finais_avaliador')->references('id')->on('users');
            $table->index('avaliador_id', 'ix_avaliacoes_finais_aval');
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE avaliacoes_finais
          ADD CONSTRAINT ck_avaliacoes_finais_tipo CHECK (tipo_avaliador IN ('professor','monitor')),
          ADD CONSTRAINT ck_avaliacao_nota CHECK (nota BETWEEN 0 AND 20);
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes_finais');
    }
};
