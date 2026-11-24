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
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('user_id');
            $table->string('tipo', 50);
            $table->string('mensagem');
            $table->timestamp('created_at', 6)->default(DB::raw("(now() at time zone 'utc')"));
            $table->boolean('lida')->default(false);

            $table->foreign('user_id', 'fk_notificacoes_user')->references('id')->on('users');
            $table->index(['user_id', 'lida'], 'ix_notificacoes_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
