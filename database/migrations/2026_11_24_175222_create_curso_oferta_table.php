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
        Schema::create('curso_oferta', function (Blueprint $table) {
            $table->integer('oferta_id');
            $table->integer('curso_id');

            $table->primary(['oferta_id', 'curso_id']); // não repete o mesmo curso na oferta
            $table->foreign('oferta_id', 'fk_curso_oferta_oferta')
                ->references('id')->on('ofertas')->cascadeOnDelete();
            $table->foreign('curso_id', 'fk_curso_oferta_curso')->references('id')->on('cursos');
            $table->index('curso_id', 'ix_curso_oferta_curso');
        });

        DB::unprepared('ALTER TABLE curso_oferta ENABLE ROW LEVEL SECURITY;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_oferta');
    }
};
