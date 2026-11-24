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
        Schema::create('observacoes', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('estagio_id');
            $table->integer('autor_id');
            $table->text('conteudo');
            $table->timestamp('created_at', 6)->default(DB::raw("(now() at time zone 'utc')"));

            $table->foreign('estagio_id', 'fk_observacoes_estagio')->references('id')->on('estagios');
            $table->foreign('autor_id', 'fk_observacoes_autor')->references('id')->on('users');
            $table->index('estagio_id', 'ix_observacoes_estagio');
            $table->index('autor_id', 'ix_observacoes_autor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observacoes');
    }
};
