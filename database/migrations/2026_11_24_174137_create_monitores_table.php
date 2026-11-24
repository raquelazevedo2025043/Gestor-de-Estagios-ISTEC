<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monitores', function (Blueprint $table) {
            $table->integer('user_id')->primary();
            $table->integer('empresa_id');
            $table->string('cargo', 100)->nullable();
            $table->boolean('ativo')->default(true);

            $table->unique(['user_id', 'empresa_id'], 'uq_monitores_user_empresa');
            $table->foreign('user_id', 'fk_monitores_user')->references('id')->on('users');
            $table->foreign('empresa_id', 'fk_monitores_empresa')->references('user_id')->on('empresas');
            $table->index('empresa_id', 'ix_monitores_empresa');
        });

        // o responsável tem de ser um monitor da empresa
        Schema::table('empresas', function (Blueprint $table) {
            $table->foreign(['responsavel_id', 'user_id'], 'fk_empresas_responsavel')
                ->references(['user_id', 'empresa_id'])->on('monitores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign('fk_empresas_responsavel');
        });

        Schema::dropIfExists('monitores');
    }
};
