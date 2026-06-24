<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create(){

        return view('auth.dashboard', [
            'user' => Auth::user()
        ]);
    }

    public function destroy(){
        Auth::logout();
        return redirect(route('home'));
    }
}
