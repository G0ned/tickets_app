<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'surname'   => ['required', 'string', 'max:255'],
            // unique:users,email ensures no duplicate accounts
            'email'     => ['required', 'email', 'unique:users,email'],
            // confirmed checks that a matching password_confirmation field was submitted
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin'      => ['nullable', 'boolean'],
            'is_supervisor' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name'          => $validated['name'],
            'surname'       => $validated['surname'],
            'email'         => $validated['email'],
            'password'      => bcrypt($validated['password']),
            // boolean() returns false when the checkbox is absent (unchecked boxes are not submitted)
            'is_admin'      => $request->boolean('is_admin'),
            'is_supervisor' => $request->boolean('is_supervisor'),
        ]);

        return redirect()->route('events-index')
            ->with('success', 'Usuario creado correctamente.');
    }
}
