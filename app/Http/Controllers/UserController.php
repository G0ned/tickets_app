<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create()
    {
        $events = Event::orderBy('name')->get();
        return view('users.create')->with('events', $events);
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
            'is_organizer'  => ['nullable', 'boolean'],
            'is_doorman'    => ['nullable', 'boolean'],
            'event_id'      => [
                Rule::requiredIf(fn () => $request->boolean('is_organizer') || $request->boolean('is_doorman')),
                'nullable',
                'exists:events,id',
            ],
        ]);

        $user = User::create([
            'name'          => $validated['name'],
            'surname'       => $validated['surname'],
            'email'         => $validated['email'],
            'password'      => bcrypt($validated['password']),
            // boolean() returns false when the checkbox is absent (unchecked boxes are not submitted)
            'is_admin'      => $request->boolean('is_admin'),
            'is_supervisor' => $request->boolean('is_supervisor'),
        ]);

        if (!empty($validated['event_id']) && ($request->boolean('is_organizer') || $request->boolean('is_doorman'))) {
            $event = Event::findOrFail($validated['event_id']);
            $event->staff()->attach($user->id, [
                'is_organizer' => $request->boolean('is_organizer'),
                'is_doorman'   => $request->boolean('is_doorman'),
            ]);
        }

        return redirect()->route('user-create')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        return view('users.edit')->with('user', $user);
    }

    public function update(User $user)
    {
        $user_new_data = request()->validate([
            'name'      => ['required', 'string', 'max:255'],
            'surname'   => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'is_admin'      => ['nullable', 'boolean'],
            'is_supervisor' => ['nullable', 'boolean'],
        ]);

        if ($user_new_data['email'] === $user->email) {
            unset($user_new_data['email']);
        }

        $user_new_data['is_admin'] = request()->boolean('is_admin');
        $user_new_data['is_supervisor'] = request()->boolean('is_supervisor');

        try{
            $user->update($user_new_data);
            return redirect(route('user-list'))->with('success', 'Datos de usuario modificados correctamente');
        } catch(\Exception $e){
            return back()->with('error', 'Error al modificar los datos del usuario');
        }
    }

    public function index()
    {
        $users = User::all();
        return view('admin.user_list')->with('users', $users);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        try {
            $user->delete();
            return redirect()->route('user-list')->with('success', 'Usuario eliminado correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'No se ha podido eliminar el usuario porque tiene eventos o portfolios asociados.');
        }
    }
}
