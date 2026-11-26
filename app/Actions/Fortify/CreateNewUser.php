<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\Empresa;
use App\Models\Monitor;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

/**
 * Registo de empresas (RF11).
 *
 * São criadas duas contas: a da empresa, que só consulta e gere os
 * monitores, e a do monitor responsável, que trata das ofertas. A empresa
 * fica pendente até ser aprovada pela coordenação. O responsável define a
 * palavra-passe pelo email que recebe quando a empresa é aprovada.
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Valida e cria a empresa, a conta da empresa e a do responsável.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        // o nif pode vir com espaços (ex.: 500 000 018)
        if (is_string($input['nif'] ?? null)) {
            $input['nif'] = preg_replace('/\s+/', '', $input['nif']);
        }

        // o Fortify só passa o campo email para minúsculas
        if (is_string($input['responsavel_email'] ?? null)) {
            $input['responsavel_email'] = Str::lower($input['responsavel_email']);
        }

        Validator::make($input, [
            'empresa_nome' => ['required', 'string', 'max:150'],
            'nif' => ['required', 'digits:9', 'unique:empresas,nif'],
            'morada' => ['required', 'string', 'max:255'],
            'area_atividade' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', $this->emailUnico()],
            'password' => $this->passwordRules(),
            'responsavel_nome' => ['required', 'string', 'max:150'],
            'responsavel_email' => ['required', 'string', 'email', 'max:150', 'different:email', $this->emailUnico()],
        ], $this->mensagens(), $this->atributos())->validate();

        return DB::transaction(function () use ($input): User {
            $conta = new User;
            $conta->forceFill([
                'nome' => $input['empresa_nome'],
                'email' => $input['email'],
                'password' => $input['password'],
                'roles' => ['empresa'],
            ])->save();

            $empresa = new Empresa([
                'nif' => $input['nif'],
                'nome' => $input['empresa_nome'],
                'morada' => $input['morada'],
                'area_atividade' => $input['area_atividade'],
            ]);
            $empresa->user_id = $conta->id;
            $empresa->save();

            // palavra-passe aleatória: o responsável define a sua depois da aprovação
            $responsavel = new User;
            $responsavel->forceFill([
                'nome' => $input['responsavel_nome'],
                'email' => $input['responsavel_email'],
                'password' => Str::password(32),
                'roles' => ['monitor'],
            ])->save();

            // o responsável tem de ser um monitor da própria empresa
            $monitor = new Monitor;
            $monitor->user_id = $responsavel->id;
            $monitor->empresa_id = $empresa->user_id;
            $monitor->save();

            $empresa->responsavel_id = $monitor->user_id;
            $empresa->save();

            return $conta;
        });
    }

    /**
     * O índice único da base de dados é sobre lower(email).
     */
    private function emailUnico(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $existe = User::query()
                ->whereRaw('lower(email) = ?', [mb_strtolower((string) $value)])
                ->exists();

            if ($existe) {
                $fail('Já existe uma conta com este email.');
            }
        };
    }

    /**
     * @return array<string, string>
     */
    private function mensagens(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'max' => 'O campo :attribute não pode ter mais de :max caracteres.',
            'email.email' => 'Indica um email válido.',
            'responsavel_email.email' => 'Indica um email válido.',
            'responsavel_email.different' => 'O email do responsável tem de ser diferente do email da empresa.',
            'nif.digits' => 'O NIF tem de ter 9 dígitos.',
            'nif.unique' => 'Já existe uma empresa registada com este NIF.',
            'password.confirmed' => 'As palavras-passe não coincidem.',
            'password.min' => 'A palavra-passe tem de ter pelo menos :min caracteres.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function atributos(): array
    {
        return [
            'empresa_nome' => 'nome da empresa',
            'nif' => 'NIF',
            'morada' => 'morada',
            'area_atividade' => 'área de atividade',
            'email' => 'email da empresa',
            'password' => 'palavra-passe',
            'responsavel_nome' => 'nome do responsável',
            'responsavel_email' => 'email do responsável',
        ];
    }
}
