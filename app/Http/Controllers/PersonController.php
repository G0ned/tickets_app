<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    private const SORTABLE_COLUMNS = ['name', 'surname', 'email', 'phone', 'type', 'brand'];

    public function index()
    {
        $sort = in_array(request('sort'), self::SORTABLE_COLUMNS, true) ? request('sort') : 'name';
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        $people = Person::with('portfolio')
        // Both checks are needed, not just one: allEditionRegistrations() is a
        // belongsToMany to Edition (SoftDeletes), so without withTrashed() here a
        // guest registration under a now-cancelled-but-not-reactivated edition is
        // silently dropped from the join (the bug this withTrashed() fixes).
        // editionRegistrationHistory() covers the other half - once that edition
        // is reactivated, the row moves out of attendee_edition into
        // attendee_edition_history entirely, so even withTrashed() above can't see
        // it anymore; only the archived table still can.
        ->withExists([
            'allEditionRegistrations as has_live_guest_registration' => fn ($query) => $query->withTrashed()->where('attendee_edition.is_guest', true),
            'editionRegistrationHistory as has_archived_guest_registration' => fn ($query) => $query->where('is_guest', true),
        ])
        ->when(request('type'), fn($query, $type) => $query->where('type', $type))
        ->when(request('brand'), fn($query, $brand) => $query->where('brand', 'like', "%{$brand}%"))
        ->orderBy($sort, $direction)
        ->when(in_array($sort, ['name', 'surname'], true), function ($query) use ($sort, $direction) {
            $query->orderBy($sort === 'name' ? 'surname' : 'name', $direction);
        })
        ->get()
        ->each(function (Person $person) {
            $person->has_been_guest = $person->has_live_guest_registration || $person->has_archived_guest_registration;
        });

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
