<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar la pantalla de registro.
     */
    public function create(): View
    {
        $configuracion = Configuracion::first();

        return view('auth.register', compact('configuracion'));
    }

    /**
     * Procesar el registro de un nuevo cliente.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        /*
         * Los registros realizados mediante este formulario
         * siempre corresponden al rol Cliente (ID 2).
         */
        $user = User::create([
            'role_id' => 2,
            'name' => $datos['name'],
            'email' => $datos['email'],
            'telefono' => $datos['telefono'],
            'password' => Hash::make($datos['password']),
            'estado' => 'activo',
        ]);

        event(new Registered($user));

        Auth::login($user);

        $user->loadMissing('role');

        if ($user->role && $user->role->nombre === 'Cliente') {
            return redirect()->intended(
                route('cliente.dashboard.index')
            );
        }

        if ($user->role && $user->role->nombre === 'Administrador') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role && $user->role->nombre === 'Delivery') {
            return redirect()->route('delivery.dashboard');
        }

        if ($user->role && $user->role->nombre === 'Cocinero') {
            return redirect()->route('cocinero.dashboard');
        }

        Auth::logout();

        return redirect('/login')->withErrors([
            'email' => 'El usuario no tiene un rol válido.',
        ]);
    }
}
