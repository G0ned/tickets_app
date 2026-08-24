<x-layout>
    @section('title', 'Nueva cartera')
    <x-slot:heading>Nueva cartera de clientes</x-slot:heading>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <x-button href="{{ route('portfolios-index', $owner->id) }}">← Volver</x-button>
        </div>

        <form method="POST" action="{{ route('portfolios-store', $owner->id) }}" class="space-y-6"
              x-data="{
                  selected: [],
                  toggleAll(ids) {
                      const allChecked = ids.every(id => this.selected.includes(id));
                      this.selected = allChecked
                          ? this.selected.filter(id => !ids.includes(id))
                          : [...new Set([...this.selected, ...ids])];
                  },
                  allSelected(ids) { return ids.length > 0 && ids.every(id => this.selected.includes(id)); }
              }">
            @csrf

            {{-- ── Nombre de la cartera ────────────────────────────────────────── --}}
            <div class="bg-gray-700 rounded-lg p-5">
                <x-form-label for="name">Nombre de la cartera</x-form-label>
                <x-form-input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Nombre de la cartera"
                    value="{{ old('name') }}"
                    required />
                <x-form-error name="name" />
            </div>

            {{-- ── Personas a asignar ──────────────────────────────────────────── --}}
            @php
                $allPersonIds = $availablePersons->pluck('id')->values()->all();
            @endphp

            <div class="bg-gray-700 rounded-lg overflow-hidden">
                <div class="px-5 py-3 bg-gray-600 border-b border-gray-500 flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                            :checked="allSelected(@json($allPersonIds))"
                            @change="toggleAll(@json($allPersonIds))"
                            class="w-4 h-4 accent-teal-500">
                        <span class="text-sm text-gray-300 font-medium">Seleccionar todos</span>
                    </label>
                    <span class="text-sm text-gray-400">{{ $availablePersons->count() }} personas</span>
                </div>

                @if ($availablePersons->isEmpty())
                    <p class="px-5 py-8 text-gray-400 text-sm text-center">
                        No hay personas registradas todavía.
                    </p>
                @else
                    <ul class="divide-y divide-gray-600 max-h-96 overflow-y-auto">
                        @foreach ($availablePersons as $person)
                            <li class="hover:bg-gray-600 transition-colors flex items-center gap-4 px-5 py-3">
                                <label class="flex items-center gap-4 flex-1 min-w-0 cursor-pointer select-none">
                                    <input type="checkbox"
                                        name="person_ids[]"
                                        value="{{ $person->id }}"
                                        x-model.number="selected"
                                        class="w-4 h-4 accent-teal-500 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white text-sm font-medium">
                                            {{ $person->name }} {{ $person->surname }}
                                        </p>
                                        <p class="text-gray-400 text-xs truncate">
                                            {{ $person->email }} &middot; {{ $person->phone }}
                                        </p>
                                    </div>
                                </label>
                                <span class="text-xs text-gray-400 shrink-0" title="Cartera actual">
                                    {{ $person->portfolio->name ?? 'Sin cartera' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <p class="text-gray-400 text-xs -mt-3">
                Si una persona ya pertenece a otra cartera, seleccionarla la trasladará a esta.
            </p>

            <x-form-error name="person_ids" />

            <div class="max-w-sm mx-auto">
                <x-form-button>Crear cartera</x-form-button>
            </div>
        </form>
    </div>
</x-layout>