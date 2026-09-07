<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class SetPasswordController extends Controller
{
    /**
     * Public, token-authenticated: reached from the link in
     * WelcomeSetPasswordMail, sent when an admin creates a new account.
     * Same pattern as InvitationRegistrationController/AttendeeCancellationController
     * (validate the token up front, show an "unavailable" view rather than a
     * broken form if it's missing/expired/already used).
     */
    public function create(string $token, Request $request)
    {
        $email = $request->query('email');
        $user = $email ? User::where('email', $email)->first() : null;

        if ($user === null || !Password::tokenExists($user, $token)) {
            return view('auth.set-password-unavailable');
        }

        return view('auth.set_password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill(['password' => bcrypt($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withErrors(['email' => 'Este enlace no es válido o ha caducado. Pide a un administrador que te cree la cuenta de nuevo.'])
                ->withInput();
        }

        $user = User::where('email', $validated['email'])->first();
        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isDoorman() && !$user->is_admin) {
            return redirect()->route('checkin')->with('success', 'Contraseña configurada correctamente.');
        }

        return redirect()->route('dashboard')->with('success', 'Contraseña configurada correctamente.');
    }
}
