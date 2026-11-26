import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

type Props = {
    passwordRules: string;
};

export default function Register({ passwordRules }: Props) {
    return (
        <>
            <Head title="Registo de empresa" />
            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="empresa_nome">
                                    Nome da empresa
                                </Label>
                                <Input
                                    id="empresa_nome"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="organization"
                                    name="empresa_nome"
                                    placeholder="Tecnoporto, Lda."
                                />
                                <InputError message={errors.empresa_nome} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="nif">NIF</Label>
                                <Input
                                    id="nif"
                                    type="text"
                                    required
                                    tabIndex={2}
                                    inputMode="numeric"
                                    name="nif"
                                    placeholder="5xx xxx xxx"
                                />
                                <InputError message={errors.nif} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="morada">Morada</Label>
                                <Input
                                    id="morada"
                                    type="text"
                                    required
                                    tabIndex={3}
                                    autoComplete="street-address"
                                    name="morada"
                                    placeholder="Rua de Exemplo 123, Porto"
                                />
                                <InputError message={errors.morada} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="area_atividade">
                                    Área de atividade
                                </Label>
                                <Input
                                    id="area_atividade"
                                    type="text"
                                    required
                                    tabIndex={4}
                                    name="area_atividade"
                                    placeholder="Desenvolvimento de software"
                                />
                                <InputError message={errors.area_atividade} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email da empresa</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={5}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="geral@tecnoporto.pt"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Palavra-passe</Label>
                                <PasswordInput
                                    id="password"
                                    required
                                    tabIndex={6}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Palavra-passe"
                                    passwordrules={passwordRules}
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Confirmar palavra-passe
                                </Label>
                                <PasswordInput
                                    id="password_confirmation"
                                    required
                                    tabIndex={7}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirmar palavra-passe"
                                    passwordrules={passwordRules}
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <div className="grid gap-1 border-t pt-6">
                                <h2 className="font-medium">
                                    Monitor responsável
                                </h2>
                                <p className="text-sm text-muted-foreground">
                                    É quem vai gerir as ofertas e acompanhar os
                                    estagiários.
                                </p>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="responsavel_nome">Nome</Label>
                                <Input
                                    id="responsavel_nome"
                                    type="text"
                                    required
                                    tabIndex={8}
                                    name="responsavel_nome"
                                    placeholder="Ana Silva"
                                />
                                <InputError message={errors.responsavel_nome} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="responsavel_email">Email</Label>
                                <Input
                                    id="responsavel_email"
                                    type="email"
                                    required
                                    tabIndex={9}
                                    name="responsavel_email"
                                    placeholder="ana.silva@tecnoporto.pt"
                                />
                                <InputError
                                    message={errors.responsavel_email}
                                />
                            </div>

                            <p className="rounded-md bg-muted p-3 text-sm text-muted-foreground">
                                Depois de submeteres, o registo fica a aguardar
                                aprovação da coordenação. Quando for aprovado, o
                                monitor responsável recebe um email para definir
                                a sua palavra-passe.
                            </p>

                            <Button
                                type="submit"
                                className="w-full"
                                tabIndex={10}
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}
                                Submeter registo
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Já tens conta?{' '}
                            <TextLink href={login()} tabIndex={11}>
                                Voltar ao login
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </>
    );
}

Register.layout = {
    title: 'Registo de empresa',
    description:
        'Preenche os dados da entidade de acolhimento para criar a conta.',
};
