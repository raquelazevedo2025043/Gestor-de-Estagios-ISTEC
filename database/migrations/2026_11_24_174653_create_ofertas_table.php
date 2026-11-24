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
        Schema::create('ofertas', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('empresa_id');
            $table->integer('monitor_id')->nullable();
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->text('requisitos')->nullable();
            $table->integer('duracao_semanas')->nullable();
            $table->string('localizacao', 150)->nullable();
            $table->integer('vagas')->default(1);
            $table->string('estado', 20)->default('rascunho');
            $table->timestamp('created_at', 6)->default(DB::raw("(now() at time zone 'utc')"));
            $table->timestamp('updated_at', 6)->nullable();

            $table->foreign('empresa_id', 'fk_ofertas_empresa')->references('user_id')->on('empresas');
            $table->foreign(['monitor_id', 'empresa_id'], 'fk_ofertas_monitor')
                ->references(['user_id', 'empresa_id'])->on('monitores');

            $table->index('empresa_id', 'ix_ofertas_empresa');
            $table->index('monitor_id', 'ix_ofertas_monitor');
            $table->index('estado', 'ix_ofertas_estado');
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE ofertas
          ADD CONSTRAINT ck_ofertas_duracao CHECK (duracao_semanas > 0),
          ADD CONSTRAINT ck_ofertas_vagas CHECK (vagas > 0),
          ADD CONSTRAINT ck_ofertas_estado CHECK (estado IN ('rascunho','publicada','encerrada','cancelada'));
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofertas');
    }
};
