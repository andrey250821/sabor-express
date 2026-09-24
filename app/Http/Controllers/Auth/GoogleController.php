<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirigir al usuario hacia Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Recibir la respuesta de Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Google debe proporcionar un correo electrónico.
            if (!$googleUser->getEmail()) {
                return redirect('/login')->withErrors([
                    'email' => 'Google no proporcionó un correo electrónico.',
                ]);
            }

            /*
             * Buscar primero por google_id.
             */
            $user = User::where(
                'google_id',
                $googleUser->getId()
            )->first();

            /*
             * Si no existe por google_id,
             * buscar por correo electrónico.
             */
            if (!$user) {
                $user = User::where(
                    'email',
                    $googleUser->getEmail()
                )->first();
            }

            /*
             * Si el usuario ya existe:
             * vinculamos su cuenta de Google.
             *
             * IMPORTANTE:
             * No modificamos su role_id.
             */
            if ($user) {

                $actualizaciones = [];

                if (!$user->google_id) {
                    $actualizaciones['google_id'] = $googleUser->getId();
                }

                if (!$user->foto_perfil && $googleUser->getAvatar()) {
                    $actualizaciones['foto_perfil'] = $googleUser->getAvatar();
                }

                if ($actualizaciones) {
                    $user->update($actualizaciones);
                }

            } else {

                /*
                 * Usuario nuevo:
                 * se registra automáticamente como Cliente.
                 *
                 * role_id = 2 corresponde a Cliente.
                 */
                $user = User::create([
                    'role_id' => 2,
                    'name' => $googleUser->getName()
                        ?: $googleUser->getNickname()
                        ?: 'Usuario Google',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'foto_perfil' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)),
                    'estado' => 'activo',
                ]);
            }

            /*
             * Comprobar que la cuenta esté activa.
             */
            if ($user->estado !== 'activo') {
                return redirect('/login')->withErrors([
                    'email' => 'Tu cuenta está inactiva. Contacta con el administrador.',
                ]);
            }

            /*
             * Iniciar sesión.
             */
            Auth::login($user);

            request()->session()->regenerate();

            /*
             * Redirigir según el rol.
             */
            if ($user->role && $user->role->nombre === 'Administrador') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role && $user->role->nombre === 'Cliente') {
                return redirect()->intended(
                    route('cliente.dashboard.index')
                );
            }

            if ($user->role && $user->role->nombre === 'Cocinero') {
                return redirect()->route('cocinero.dashboard');
            }

            if ($user->role && $user->role->nombre === 'Delivery') {
                return redirect()->route('delivery.dashboard');
            }

            /*
             * Si el usuario no tiene un rol válido,
             * cerrar sesión.
             */
            Auth::logout();

            return redirect('/login')->withErrors([
                'email' => 'El usuario no tiene un rol válido.',
            ]);

        } catch (\Exception $e) {

            return redirect('/login')->withErrors([
                'email' => 'No fue posible iniciar sesión con Google: '
                    . $e->getMessage(),
            ]);
        }
    }
}
