<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use App\Rules\ValidateId;
use App\Models\User;
use App\Models\Atendee;

class UserController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        return view('attendee.create');
    }

    public function store()
    {
        $userData = request()->validate([
            'identification' => ['required', 'max:9', new ValidateId(request()->id_type)],
            'firstname' => ['required', 'max:255'],
            'surname' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $userData['role'] = 'attendee';

        $info = request()->validate([
            'phone' => ['required', 'max:9'],
            'zip_code' => ['required', 'max:5'],
            'privacy_policy' => ['required', 'boolean'],
            'img_rights_ads' => ['required', 'boolean'],
            'img_rights_web' => ['required', 'boolean'],
            'img_rights_rss' => ['required', 'boolean'],
        ]);

        DB::transaction (function () use ($userData, $info){
            $user = User::create($userData);
            if (!$user){
                throw new \Exception('No se ha podido registrar al asistente...');
            }
            $createdUser = User::find($userData['identification']);
            $createdUser->attendee()->create($info);
        });

        return redirect('/login')->with('success', 'Registro completado satisfactoriamente.');
    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
