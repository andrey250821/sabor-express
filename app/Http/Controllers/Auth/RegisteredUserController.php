<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'role_id' => 2, // Cliente por defecto

            'name' => $request->name,

            'email' => $request->email,

            'password' => Hash::make($request->password),

            'estado' => 'activo',
        ]);
        event(new Registered($user));

        Auth::login($user);


        switch ($user->role_id) {


            case 1:
                // Administrador
                return redirect()
                    ->route('admin.dashboard');


            case 3:
                // Delivery
                return redirect()
                    ->route('delivery.dashboard');


            case 2:
            default:
                // Cliente
                return redirect()
                    ->route('cliente.productos');
        }
    }
}
