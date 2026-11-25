<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Password partilhada por todos os utilizadores criados.
     */
    protected static ?string $password;

    /**
     * Valores por defeito de um utilizador.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->userName() . '@my.istec.pt',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'roles' => ['aluno'],
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ];
    }

    /**
     * Define os papéis do utilizador.
     */
    public function comRoles(string ...$roles): static
    {
        return $this->state(fn(array $attributes) => [
            'roles' => $roles,
        ]);
    }

    /**
     * Cria o utilizador com a conta desativada.
     */
    public function desativado(): static
    {
        return $this->state(fn(array $attributes) => [
            'desativado_em' => now(),
        ]);
    }

    /**
     * Cria o utilizador com o email por verificar.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Cria o utilizador com a autenticação de dois fatores ativa.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn(array $attributes) => [
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
