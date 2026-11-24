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
        Schema::create('mensagens', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('remetente_id');
            $table->integer('destinatario_id');
            $table->text('conteudo');
            $table->timestamp('data_envio', 6)->default(DB::raw("(now() at time zone 'utc')"));
            $table->boolean('lida')->default(false);

            $table->foreign('remetente_id', 'fk_mensagens_remetente')->references('id')->on('users');
            $table->foreign('destinatario_id', 'fk_mensagens_destinatario')->references('id')->on('users');
            $table->index(['destinatario_id', 'lida'], 'ix_mensagens_destinatario');
            $table->index('remetente_id', 'ix_mensagens_remetente');
        });

        DB::unprepared('ALTER TABLE mensagens ADD CONSTRAINT ck_mensagens_destinatario CHECK (remetente_id <> destinatario_id);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensagens');
    }
};
