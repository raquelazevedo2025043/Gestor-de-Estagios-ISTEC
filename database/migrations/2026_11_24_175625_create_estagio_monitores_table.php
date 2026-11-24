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
        Schema::create('estagio_monitores', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->integer('estagio_id');
            $table->integer('monitor_id');
            $table->timestamp('data_inicio', 6)->default(DB::raw("(clock_timestamp() at time zone 'utc')"));
            $table->timestamp('data_fim', 6)->nullable();

            $table->foreign('estagio_id', 'fk_estagio_monitores_estagio')->references('id')->on('estagios');
            $table->foreign('monitor_id', 'fk_estagio_monitores_monitor')->references('user_id')->on('monitores');
            $table->index('estagio_id', 'ix_estagio_monitores_estagio');
            $table->index('monitor_id', 'ix_estagio_monitores_monitor');
        });

        DB::unprepared(<<<'SQL'
        ALTER TABLE estagio_monitores
          ADD CONSTRAINT ck_estagio_monitores_datas CHECK (data_fim IS NULL OR data_fim >= data_inicio);
        CREATE UNIQUE INDEX ux_estagio_monitores_atual ON estagio_monitores (estagio_id) WHERE data_fim IS NULL;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estagio_monitores');
    }
};
