<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /**
     * Self-service password change: any authenticated user (including a
     * pure doorman, otherwise restricted to /checkin - see
     * RestrictDoorman::$allowedRoutes) can reach this to change their own
     * password. Distinct from SetPasswordController, which is for a brand
     * new account with no usable password yet and has no session/current
     * password to check against.
     */
    public function edit()
    {
        return view('auth.change_password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // The 'current_password' rule (built into Laravel's validator)
            // checks the value against the currently authenticated user's
            // password itself - nothing to wire up by hand.
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        $user = Auth::user();
        $user->forceFill(['password' => $validated['password']])->save();

        return back()->with('success', 'Tu contraseña se ha actualizado correctamente.');
    }
}
