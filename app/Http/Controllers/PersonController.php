<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::with('portfolio')->get();
        return view('admin.contact_list')->with('people', $people);
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
