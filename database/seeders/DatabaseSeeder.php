<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Area;
use App\Models\Candidatura;
use App\Models\Curso;
use App\Models\Empresa;
use App\Models\Estagio;
use App\Models\Monitor;
use App\Models\Oferta;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Dados de demonstração.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Os dados de demonstração não podem ser criados em produção.');

            return;
        }

        if (User::query()->exists()) {
            $this->command?->warn('A base de dados já tem utilizadores: nada foi criado.');

            return;
        }

        // se algo falhar, não fica nada a meio
        DB::transaction(function (): void {
            $this->criarDados();
        });
    }

    private function criarDados(): void
    {
        // cursos
        $software = Curso::create(['nome' => 'Desenvolvimento de Software']);
        $ciberseguranca = Curso::create(['nome' => 'Cibersegurança']);
        $moveis = Curso::create(['nome' => 'Desenvolvimento para Dispositivos Móveis']);
        $multimedia = Curso::create(['nome' => 'Desenvolvimento de Produtos Multimédia']);
        $robotica = Curso::create(['nome' => 'Robótica e Inteligência Artificial']);
        $gestao = Curso::create(['nome' => 'Informática de Gestão']);
        $design = Curso::create(['nome' => 'Design e Multimédia']);

        // coordenação e professores
        $helena = $this->criarProfessor('Helena Duarte', 'demo.coordenacao@my.istec.pt', 'Informática', 'professor', 'coordenacao');
        $rui = $this->criarProfessor('Rui Martins', 'demo.rui.martins@my.istec.pt', 'Informática', 'professor');
        $carla = $this->criarProfessor('Carla Nunes', 'demo.carla.nunes@my.istec.pt', 'Multimédia', 'professor');

        // empresas
        $techNova = $this->criarEmpresa('TechNova', 'technova@example.com', '500000018', 'Lisboa');
        $redeSegura = $this->criarEmpresa('Rede Segura', 'redesegura@example.com', '500000026', 'Porto');
        $this->criarEmpresa('Pixel Studio', 'pixelstudio@example.com', '500000034', 'Lisboa'); // fica pendente

        $this->aprovarEmpresa($techNova, $helena->user);
        $this->aprovarEmpresa($redeSegura, $helena->user);

        // monitores (só depois de existirem é que a empresa pode ter responsável)
        $joao = $this->criarMonitor($techNova, 'João Ferreira', 'joao.ferreira@example.com', 'Programador Sénior');
        $ines = $this->criarMonitor($redeSegura, 'Inês Lopes', 'ines.lopes@example.com', 'Administradora de Sistemas');

        $techNova->responsavel_id = $joao->user_id;
        $techNova->save();

        $redeSegura->responsavel_id = $ines->user_id;
        $redeSegura->save();

        // alunos
        $ana = $this->criarAluno('Ana Silva', 'demo.ana.silva@my.istec.pt', '20240001', $software, 2);
        $bruno = $this->criarAluno('Bruno Costa', 'demo.bruno.costa@my.istec.pt', '20240002', $software, 2);
        $catarina = $this->criarAluno('Catarina Reis', 'demo.catarina.reis@my.istec.pt', '20240003', $ciberseguranca, 2);
        $diogo = $this->criarAluno('Diogo Alves', 'demo.diogo.alves@my.istec.pt', '20240004', $multimedia, 2);
        $this->criarAluno('Eva Santos', 'demo.eva.santos@my.istec.pt', '20250001', $robotica, 1);

        // ofertas (sem cursos = para qualquer curso)
        $web = $this->criarOferta($techNova, $joao, 'Programador Web Laravel', 'Desenvolvimento web (back-end)', 'hibrido', 'publicada', [$software, $moveis, $multimedia, $design]);
        $testes = $this->criarOferta($techNova, $joao, 'Testes de Software', 'Testes e qualidade (QA)', 'presencial', 'publicada', [$software, $gestao]);
        $sistemas = $this->criarOferta($redeSegura, $ines, 'Administração de Redes', 'Redes e infraestruturas', 'presencial', 'publicada', [$ciberseguranca, $software]);
        $suporte = $this->criarOferta($redeSegura, $ines, 'Suporte Técnico', 'Suporte técnico / helpdesk', 'presencial', 'publicada', []);
        $this->criarOferta($redeSegura, $ines, 'Análise de Segurança', 'Segurança (SOC, auditoria)', 'remoto', 'rascunho', [$ciberseguranca]);

        // ana: estágio em curso, ao aceitar, o trigger cancela a outra candidatura ativa dela
        $this->criarCandidatura($ana, $suporte, 'submetida', 30);
        /** @var Estagio $estagio */
        $estagio = $this->criarCandidatura($ana, $web, 'aceite', 30, 25, 20)->estagio()->firstOrFail();
        $estagio->forceFill([
            'professor_id' => $rui->user_id,
            'professor_atribuido_por' => $helena->user_id,
            'data_atribuicao_professor' => now()->subDays(18),
            'data_inicio' => now()->subDays(14),
            'data_fim' => now()->addWeeks(10),
            'estado' => 'em_curso',
        ])->save();

        $observacao = $estagio->observacoes()->make(['conteudo' => 'Boa integração na equipa e boa evolução técnica.']);
        $observacao->autor_id = $joao->user_id;
        $observacao->save();

        // bruno: estágio concluído e avaliado
        /** @var Estagio $estagio */
        $estagio = $this->criarCandidatura($bruno, $testes, 'aceite', 240, 235, 230)->estagio()->firstOrFail();
        $estagio->forceFill([
            'professor_id' => $carla->user_id,
            'professor_atribuido_por' => $helena->user_id,
            'data_atribuicao_professor' => now()->subDays(225),
            'data_inicio' => now()->subDays(210),
            'data_fim' => now()->subDays(120),
            'estado' => 'concluido',
        ])->save();

        $avaliacoes = [
            ['professor', $carla->user_id, 17, 'Relatório completo e bem estruturado.'],
            ['monitor', $joao->user_id, 18, 'Muito autónomo e responsável.'],
        ];

        foreach ($avaliacoes as [$tipo, $avaliadorId, $nota, $comentario]) {
            $avaliacao = $estagio->avaliacoesFinais()->make(['nota' => $nota, 'comentario' => $comentario]);
            $avaliacao->tipo_avaliador = $tipo;
            $avaliacao->avaliador_id = $avaliadorId;
            $avaliacao->save();
        }

        $estagio->avaliacaoEmpresa()->create(['classificacao' => 5, 'comentario' => 'Aprendi bastante.']);

        // candidaturas noutros estados
        $this->criarCandidatura($catarina, $sistemas, 'aceite_empresa', 6, 2);
        $this->criarCandidatura($catarina, $suporte, 'em_analise', 4);
        $this->criarCandidatura($diogo, $web, 'rejeitada_empresa', 12);
        $this->criarCandidatura($diogo, $suporte, 'submetida', 1);

        // comunicação
        $ana->user->mensagensEnviadas()->create([
            'destinatario_id' => $rui->user_id,
            'conteudo' => 'Boa tarde, professor. Podemos marcar uma reunião?',
        ]);

        $catarina->user->notificacoes()->create([
            'tipo' => 'candidatura_aceite_empresa',
            'mensagem' => 'A empresa aceitou a tua candidatura. Tens 15 dias para responder.',
        ]);
    }

    private function criarUser(string $nome, string $email, string ...$roles): User
    {
        return User::factory()->comRoles(...$roles)->create(['nome' => $nome, 'email' => $email]);
    }

    private function criarProfessor(string $nome, string $email, string $departamento, string ...$roles): Professor
    {
        return $this->criarUser($nome, $email, ...$roles)
            ->professor()->create(['departamento' => $departamento]);
    }

    private function criarAluno(string $nome, string $email, string $numero, Curso $curso, int $ano): Aluno
    {
        return $this->criarUser($nome, $email, 'aluno')
            ->aluno()->create(['numero' => $numero, 'curso_id' => $curso->id, 'ano' => $ano]);
    }

    private function criarEmpresa(string $nome, string $email, string $nif, string $morada): Empresa
    {
        return $this->criarUser($nome, $email, 'empresa')
            ->empresa()->create(['nif' => $nif, 'nome' => $nome, 'morada' => $morada, 'telefone' => '210000000']);
    }

    private function aprovarEmpresa(Empresa $empresa, User $coordenador): void
    {
        $empresa->forceFill([
            'estado_validacao' => 'aprovada',
            'validado_por' => $coordenador->id,
            'data_validacao' => now()->subDays(250),
        ])->save();
    }

    private function criarMonitor(Empresa $empresa, string $nome, string $email, string $cargo): Monitor
    {
        $monitor = $this->criarUser($nome, $email, 'monitor')->monitor()->make(['cargo' => $cargo]);
        $monitor->empresa_id = $empresa->user_id;
        $monitor->save();

        return $monitor;
    }

    /**
     * @param  array<int, Curso>  $cursos
     */
    private function criarOferta(Empresa $empresa, Monitor $monitor, string $titulo, string $area, string $regime, string $estado, array $cursos): Oferta
    {
        $oferta = $empresa->ofertas()->make([
            'monitor_id' => $monitor->user_id,
            'area_id' => Area::where('nome', $area)->value('id'),
            'titulo' => $titulo,
            'descricao' => "Estágio curricular: {$titulo}.",
            'requisitos' => 'Vontade de aprender e trabalho em equipa.',
            'duracao_semanas' => 12,
            'localizacao' => $empresa->morada,
            'regime' => $regime,
            'vagas' => 2,
        ]);
        $oferta->estado = $estado;
        $oferta->save();

        $oferta->cursos()->sync(array_map(fn(Curso $curso) => $curso->id, $cursos));

        return $oferta;
    }

    /**
     * Os últimos três valores são "há quantos dias" cada coisa aconteceu.
     */
    private function criarCandidatura(
        Aluno $aluno,
        Oferta $oferta,
        string $estado,
        int $diasCandidatura,
        ?int $diasAceitacaoEmpresa = null,
        ?int $diasRespostaAluno = null,
    ): Candidatura {
        $candidatura = $aluno->candidaturas()->make(['oferta_id' => $oferta->id]);
        $candidatura->estado = $estado;
        $candidatura->data_candidatura = now()->subDays($diasCandidatura);
        $candidatura->data_aceitacao_empresa = $diasAceitacaoEmpresa === null ? null : now()->subDays($diasAceitacaoEmpresa);
        $candidatura->data_resposta_aluno = $diasRespostaAluno === null ? null : now()->subDays($diasRespostaAluno);
        $candidatura->save();

        return $candidatura;
    }
}
