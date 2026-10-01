<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot_password');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user === null) {
            return back()
                ->withErrors(['email' => 'No existe ninguna cuenta con ese email.'])
                ->withInput();
        }

        $token = Password::broker('forgot_password')->createToken($user);
        Mail::to($user->email)->queue(new ForgotPasswordMail($user, $token));

        return redirect()->route('home')
            ->with('success', 'Te hemos enviado un correo con un enlace para restablecer tu contraseña. Caduca en 5 minutos.');
    }

    public function edit(string $token, Request $request)
    {
        $email = $request->query('email');
        $user = $email ? User::where('email', $email)->first() : null;

        if ($user === null || !Password::broker('forgot_password')->tokenExists($user, $token)) {
            return view('auth.reset-password-unavailable');
        }

        return view('auth.reset_password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker('forgot_password')->reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill(['password' => bcrypt($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withErrors(['email' => 'Este enlace no es válido o ha caducado (5 minutos). Solicita uno nuevo.'])
                ->withInput();
        }

        $user = User::where('email', $validated['email'])->first();
        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isDoorman() && !$user->is_admin) {
            return redirect()->route('checkin')->with('success', 'Contraseña restablecida correctamente.');
        }

        return redirect()->route('dashboard')->with('success', 'Contraseña restablecida correctamente.');
    }
}
