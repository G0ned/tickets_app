<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SessionController extends Controller
{

    public function create(){
        return view('home');
    }

    public function store(){
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        if(Auth::attempt($credentials)){
            request()->session()->regenerate();
            $user = Auth::user();
            if ($user->isDoorman() && !$user->is_admin) {
                return redirect()->route('checkin');
            }
            return redirect(route('dashboard'));
        }
        else{
            return back()->withErrors([
                'email' => 'Inicio de sesión fallido. Compruebe sus credenciales.',
            ]);
        }
    }
}
