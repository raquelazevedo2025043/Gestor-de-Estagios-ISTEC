<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Áreas = tipo de trabalho do estágio (o curso diz para quem é o estágio).
     */
    private array $areas = [
        'Desenvolvimento web (front-end)',
        'Desenvolvimento web (back-end)',
        'Desenvolvimento mobile',
        'Testes e qualidade (QA)',
        'Análise de dados / BI',
        'Inteligência artificial',
        'Cloud e DevOps',
        'Redes e infraestruturas',
        'Segurança (SOC, auditoria)',
        'Suporte técnico / helpdesk',
        'Sistemas de gestão (ERP)',
        'UX/UI',
        'Design gráfico',
        'Vídeo e animação',
        'Marketing digital',
        'Robótica e IoT',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->integer('id')->generatedAs()->primary();
            $table->string('nome', 100);
        });

        DB::unprepared(<<<'SQL'
        CREATE UNIQUE INDEX uq_areas_nome ON areas (lower(nome));
        SQL);

        DB::table('areas')->insert(array_map(fn(string $nome) => ['nome' => $nome], $this->areas));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
