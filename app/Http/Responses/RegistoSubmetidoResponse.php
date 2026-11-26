<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse;

/**
 * O Fortify inicia sessão logo após o registo. Como a empresa tem de
 * esperar pela aprovação da coordenação, a sessão é terminada e a
 * pessoa volta ao login com uma mensagem.
 */
class RegistoSubmetidoResponse implements RegisterResponse
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): RedirectResponse
    {
        Auth::guard(config('fortify.guard'))->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'status',
            'Registo submetido. Vais poder entrar quando a coordenação aprovar a empresa.',
        );
    }
}
