<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    public function index()
    {
        $sort = in_array(request('sort'), Person::LIST_SORTABLE_COLUMNS, true) ? request('sort') : 'name';
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $people = Person::flagGuests(
            Person::forListing($sort, $direction, request('type'), request('brand'))->get()
        );

        return view('admin.contact_list')->with([
            'people'       => $people,
            'selectedType' => request('type'),
            'selectedBrand' => request('brand'),
            'sort'         => $sort,
            'direction'    => $direction,
        ]);
    }

    public function show(Person $person)
    {
        $person->load(['portfolio.user', 'allEditionRegistrations.event']);

        $registrations = $person->allEditionRegistrations->sortByDesc(fn ($edition) => $edition->date);

        return view('admin.contact_show', [
            'person'        => $person,
            'registrations' => $registrations,
            'attendedCount'   => $registrations->filter(fn ($e) => !$e->pivot->cancelled_at && $e->pivot->attendance)->count(),
            'cancelledCount'  => $registrations->filter(fn ($e) => $e->pivot->cancelled_at)->count(),
        ]);
    }

    public function edit(Person $person)
    {
        return view('admin.contact_edit', ['person' => $person]);
    }

    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'surname'  => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('person', 'email')->ignore($person->id)],
            'phone'    => ['required', 'string', 'max:20', Rule::unique('person', 'phone')->ignore($person->id)],
            'zip_code' => ['nullable', 'digits:5'],
        ], [
            'name.required'    => 'El nombre es obligatorio.',
            'surname.required' => 'Los apellidos son obligatorios.',
            'email.required'   => 'El e-mail es obligatorio.',
            'email.email'      => 'El e-mail no es válido.',
            'email.unique'     => 'Ya existe otro contacto con ese e-mail.',
            'phone.required'   => 'El teléfono es obligatorio.',
            'phone.max'        => 'El teléfono no puede tener más de :max caracteres.',
            'phone.unique'     => 'Ya existe otro contacto con ese teléfono.',
            'zip_code.digits'  => 'El código postal debe tener :digits dígitos.',
        ]);

        $person->update($validated);

        return redirect()->route('contacts-index')->with('success', 'Contacto actualizado correctamente');
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'person_ids' => ['required', 'array', 'min:1'],
            'person_ids.*' => ['integer', 'exists:person,id'],
        ]);

        Person::whereIn('id', $validated['person_ids'])->delete();

        return redirect()->route('contacts-index')->with('success', 'Contactos eliminados correctamente');
    }
}
