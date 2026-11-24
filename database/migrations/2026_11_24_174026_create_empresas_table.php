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
        Schema::create('empresas', function (Blueprint $table) {
            $table->integer('user_id')->primary();
            $table->char('nif', 9)->unique('uq_empresas_nif');
            $table->string('nome', 150);
            $table->string('morada')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('estado_validacao', 20)->default('pendente');
            $table->text('justificacao_rejeicao')->nullable();
            $table->integer('responsavel_id')->nullable(); // a fk é criada na migração de monitores
            $table->integer('validado_por')->nullable();
            $table->timestamp('data_validacao', 6)->nullable();

            $table->foreign('user_id', 'fk_empresas_user')->references('id')->on('users');
            $table->foreign('validado_por', 'fk_empresas_validado_por')->references('id')->on('users');
            $table->index('validado_por', 'ix_empresas_validado_por');
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE empresas
          ADD CONSTRAINT ck_empresas_nif CHECK (nif ~ '^[0-9]{9}$'),
          ADD CONSTRAINT ck_empresas_estado CHECK (estado_validacao IN ('pendente','aprovada','rejeitada')),
          ADD CONSTRAINT ck_empresas_justificacao CHECK (
            estado_validacao <> 'rejeitada' OR justificacao_rejeicao IS NOT NULL
          ),
          ADD CONSTRAINT ck_empresas_validacao CHECK (
            estado_validacao = 'pendente'
            OR (validado_por IS NOT NULL AND data_validacao IS NOT NULL)
          );
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
