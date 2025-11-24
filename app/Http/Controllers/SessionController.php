<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionController extends Controller
{

    public function create()
    {
        return view('auth.login');
    }

    public function store()
    {
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials))
            {
                request()->session()->regenerate();
                return redirect()->intended('/attendee/dashboard')->with('debug', [
                'auth_check' => Auth::check(),
                'user' => Auth::user()
                ]);
            }
        else{
            return back()->withErrors([
                'email' => 'Inicio de sesión fallido. Compruebe sus credenciales.',
            ]);
        }
        
    }

    public function destroy()
    {
        Auth::logout();

        return redirect('/login');
    }
}
