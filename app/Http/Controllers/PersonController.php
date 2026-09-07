<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::with('portfolio')
        ->when(request('type'), fn($query, $type) => $query->where('type', $type))
        ->when(request('brand'), fn($query, $brand) => $query->where('brand', 'like', "%{$brand}%"))
        ->get();
        return view('admin.contact_list')->with('people', $people)->with('selectedType', request('type'))->with('selectedBrand', request('brand'));
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
