<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\UserDashboardRoute;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Marcar el correo como verificado y volver al panel del rol del usuario.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $user->loadMissing('role');

        $destino = route(
            UserDashboardRoute::nameFor($user),
            absolute: false
        ) . '?verified=1';

        return redirect()->intended($destino);
    }
}
