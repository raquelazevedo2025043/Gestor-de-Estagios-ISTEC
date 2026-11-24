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
        Schema::create('avaliacoes_empresa', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('estagio_id')->unique('uq_avaliacoes_empresa_estagio');
            $table->integer('classificacao');
            $table->text('comentario')->nullable();
            $table->timestamp('created_at', 6)->default(DB::raw("(now() at time zone 'utc')"));

            $table->foreign('estagio_id', 'fk_avaliacoes_empresa_estagio')->references('id')->on('estagios');
        });

        DB::unprepared('ALTER TABLE avaliacoes_empresa ADD CONSTRAINT ck_avaliacao_classificacao CHECK (classificacao BETWEEN 1 AND 5);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes_empresa');
    }
};
