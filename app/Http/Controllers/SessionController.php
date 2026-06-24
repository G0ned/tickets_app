<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SessionController extends Controller
{

    public function create(){
        $n_users = DB::table('users')->count();
        return view('home')->with('number', $n_users);
    }

    public function store(){
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        if(Auth::attempt($credentials)){
            request()->session()->regenerate();
            return redirect(route('dashboard'));
        }
        else{
            return back()->withErrors([
                'email' => 'Inicio de sesión fallido. Compruebe sus credenciales.',
            ]);
        }
    }
}
