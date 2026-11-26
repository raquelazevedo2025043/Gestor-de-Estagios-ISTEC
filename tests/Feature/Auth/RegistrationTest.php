<?php

namespace Tests\Feature\Auth;

use App\Models\Empresa;
use App\Models\User;
use App\Notifications\DefinirPalavraPasse;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function dados(array $alteracoes = []): array
    {
        return array_merge([
            'empresa_nome' => 'Tecnoporto, Lda.',
            'nif' => '500 000 042',
            'morada' => 'Rua de Exemplo 123, Porto',
            'area_atividade' => 'Desenvolvimento de software',
            'email' => 'Geral@Tecnoporto.pt',
            'password' => 'password',
            'password_confirmation' => 'password',
            'responsavel_nome' => 'Ana Silva',
            'responsavel_email' => 'Ana.Silva@Tecnoporto.pt',
        ], $alteracoes);
    }

    /**
     * Regista a empresa e devolve [conta da empresa, monitor responsável].
     *
     * @return array{0: User, 1: User}
     */
    private function registar(): array
    {
        $this->post(route('register.store'), $this->dados());

        return [
            User::where('email', 'geral@tecnoporto.pt')->firstOrFail(),
            User::where('email', 'ana.silva@tecnoporto.pt')->firstOrFail(),
        ];
    }

    private function coordenacao(): User
    {
        return User::factory()->comRoles('professor', 'coordenacao')->create();
    }

    private function entrar(string $email, string $password = 'password'): TestResponse
    {
        return $this->post(route('login.store'), ['email' => $email, 'password' => $password]);
    }

    public function test_registration_screen_can_be_rendered()
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_registration_creates_the_empresa_and_the_responsible_monitor()
    {
        Notification::fake();

        $response = $this->post(route('register.store'), $this->dados());

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertGuest();

        [$conta, $responsavel] = [
            User::where('email', 'geral@tecnoporto.pt')->firstOrFail(),
            User::where('email', 'ana.silva@tecnoporto.pt')->firstOrFail(),
        ];

        $this->assertSame('Tecnoporto, Lda.', $conta->nome);
        $this->assertSame(['empresa'], $conta->roles);
        $this->assertSame('Ana Silva', $responsavel->nome);
        $this->assertSame(['monitor'], $responsavel->roles);

        $empresa = Empresa::findOrFail($conta->id);
        $this->assertSame('500000042', $empresa->nif);
        $this->assertSame('Desenvolvimento de software', $empresa->area_atividade);
        $this->assertSame('pendente', $empresa->estado_validacao);
        $this->assertSame($responsavel->id, $empresa->responsavel_id);
        $this->assertSame($conta->id, $responsavel->monitor->empresa_id);
        $this->assertTrue($responsavel->monitor->ativo);

        // ninguém recebe emails enquanto a empresa não for aprovada
        Notification::assertNothingSent();
    }

    public function test_pending_empresa_cannot_log_in()
    {
        $this->registar();

        $this->entrar('geral@tecnoporto.pt')->assertSessionHasErrors([
            'email' => 'A tua empresa ainda está a aguardar aprovação da coordenação.',
        ]);

        $this->assertGuest();
    }

    public function test_monitor_of_a_pending_empresa_cannot_log_in()
    {
        [, $responsavel] = $this->registar();
        $responsavel->forceFill(['password' => Hash::make('password')])->save();

        $this->entrar('ana.silva@tecnoporto.pt')->assertSessionHasErrors([
            'email' => 'A empresa ainda não foi aprovada pela coordenação.',
        ]);

        $this->assertGuest();
    }

    public function test_rejected_empresa_cannot_log_in()
    {
        [$conta] = $this->registar();

        Empresa::findOrFail($conta->id)->rejeitar($this->coordenacao(), 'NIF inválido.');

        $this->entrar('geral@tecnoporto.pt')->assertSessionHasErrors([
            'email' => 'O registo da empresa foi rejeitado pela coordenação: NIF inválido.',
        ]);

        $this->assertGuest();
    }

    public function test_approved_empresa_can_log_in_and_receives_verification_email()
    {
        Notification::fake();

        [$conta] = $this->registar();
        Empresa::findOrFail($conta->id)->aprovar($this->coordenacao());

        $this->entrar('geral@tecnoporto.pt')->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($conta);
        Notification::assertSentTo($conta, VerifyEmail::class);
    }

    public function test_responsible_monitor_sets_the_password_after_approval()
    {
        Notification::fake();

        [$conta, $responsavel] = $this->registar();
        Empresa::findOrFail($conta->id)->aprovar($this->coordenacao());

        $token = null;
        Notification::assertSentTo($responsavel, DefinirPalavraPasse::class, function (DefinirPalavraPasse $notificacao) use (&$token) {
            $token = $notificacao->token;

            return $notificacao->empresa === 'Tecnoporto, Lda.';
        });

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'ana.silva@tecnoporto.pt',
            'password' => 'nova-password',
            'password_confirmation' => 'nova-password',
        ])->assertSessionHasNoErrors();

        // o link veio por email, por isso o email fica confirmado
        $this->assertNotNull($responsavel->fresh()->email_verified_at);

        $this->entrar('ana.silva@tecnoporto.pt', 'nova-password')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($responsavel);
    }

    public function test_deactivated_user_cannot_log_in()
    {
        $user = User::factory()->desativado()->create();

        $this->entrar($user->email)->assertSessionHasErrors(['email' => 'Esta conta está desativada.']);

        $this->assertGuest();
    }

    public function test_wrong_password_does_not_reveal_the_empresa_state()
    {
        $this->registar();

        $this->entrar('geral@tecnoporto.pt', 'errada')
            ->assertSessionHasErrors(['email' => __('auth.failed')]);
    }

    public function test_nif_must_have_nine_digits()
    {
        $this->post(route('register.store'), $this->dados(['nif' => '12345']))
            ->assertSessionHasErrors('nif');

        $this->assertDatabaseMissing('empresas', ['nif' => '500000042']);
    }

    public function test_emails_must_be_unique_regardless_of_case()
    {
        User::factory()->create(['email' => 'ana.silva@tecnoporto.pt']);

        $this->post(route('register.store'), $this->dados())
            ->assertSessionHasErrors('responsavel_email');

        $this->assertDatabaseMissing('empresas', ['nif' => '500000042']);
    }

    public function test_responsible_email_must_be_different_from_the_empresa_email()
    {
        $this->post(route('register.store'), $this->dados(['responsavel_email' => 'geral@tecnoporto.pt']))
            ->assertSessionHasErrors('responsavel_email');

        $this->assertDatabaseMissing('empresas', ['nif' => '500000042']);
    }
}
