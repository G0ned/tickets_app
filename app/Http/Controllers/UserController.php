<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Event;
use App\Mail\WelcomeSetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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
            // Never communicated to anyone - the account has no usable password
            // until the new user sets their own via the emailed link below.
            'password'      => bcrypt(Str::random(40)),
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

        $token = Password::createToken($user);
        Mail::to($user->email)->send(new WelcomeSetPasswordMail($user, $token));

        return redirect()->route('user-list')
            ->with('success', 'Usuario creado correctamente. Se le ha enviado un correo para que configure su contraseña.');
    }

    public function edit(User $user)
    {
        $events = Event::orderBy('name')->get();
        $user->load('organized_events');

        return view('users.edit')->with([
            'user'   => $user,
            'events' => $events,
        ]);
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

    public function assignOrganizer(Request $request, User $user)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $event->assignStaffRole($user->id, 'is_organizer');

        return redirect()->route('user-edit', $user->id)->with('success', 'Organizador asignado correctamente.');
    }

    private const SORTABLE_COLUMNS = ['name', 'surname', 'email', 'is_admin', 'is_supervisor'];

    public function index()
    {
        $sort = in_array(request('sort'), self::SORTABLE_COLUMNS, true) ? request('sort') : 'name';
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $users = User::orderBy($sort, $direction)
            // name/surname double as each other's tie-breaker, same as
            // PersonController::index() - see that method for the rationale.
            ->when(in_array($sort, ['name', 'surname'], true), function ($query) use ($sort, $direction) {
                $query->orderBy($sort === 'name' ? 'surname' : 'name', $direction);
            })
            ->get();

        return view('admin.user_list')->with([
            'users'     => $users,
            'sort'      => $sort,
            'direction' => $direction,
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();
        return redirect()->route('user-list')->with('success', 'Usuario eliminado correctamente');
    }
}
