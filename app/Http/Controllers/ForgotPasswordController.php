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
    /**
     * Public: the "¿Olvidaste tu contraseña?" flow from the login page
     * (home.blade.php). Uses its own password broker (config('auth.passwords.
     * forgot_password'), 5-minute expiry, own forgot_password_tokens table)
     * instead of the 'users' broker SetPasswordController uses for a brand
     * new account's 60-minute welcome link - see that broker's config entry
     * for why they're kept apart.
     */
    public function create()
    {
        return view('auth.forgot_password');
    }

    /**
     * Deliberately reveals whether the email exists (explicit requirement:
     * "if the email does not exist ... a message must be displayed that the
     * email does not exist") - a real trade-off against the usual practice of
     * staying silent either way to avoid confirming which emails have an
     * account, but this is an internal staff tool, not a public sign-up form,
     * and the request was explicit about wanting the confirmation.
     */
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

    /**
     * Same pattern as SetPasswordController::create() - validate the token
     * up front, an "unavailable" view (distinct wording: expired/used here
     * means "send a new request", not "ask an admin to recreate the
     * account") instead of a broken form for a missing/expired/already-used
     * token.
     */
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
