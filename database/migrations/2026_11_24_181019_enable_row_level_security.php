<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    private array $tabelas = [
        'cursos',
        'areas',
        'users',
        'alunos',
        'professores',
        'empresas',
        'monitores',
        'ofertas',
        'candidaturas',
        'estagios',
        'estagio_monitores',
        'observacoes',
        'avaliacoes_empresa',
        'avaliacoes_finais',
        'mensagens',
        'notificacoes',
        // tabelas do laravel
        'migrations',
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tabelas as $tabela) {
            DB::unprepared("ALTER TABLE {$tabela} ENABLE ROW LEVEL SECURITY;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tabelas as $tabela) {
            DB::unprepared("ALTER TABLE {$tabela} DISABLE ROW LEVEL SECURITY;");
        }
    }
};
