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
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'roles' => ['aluno'],
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
}
