<x-layout>
    @section('title', $list->name)
    <x-slot:heading>{{ $list->name }}</x-slot:heading>

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Summary table --}}
        <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-600 bg-gray-600">
                            <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Nombre</th>
                            <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Evento</th>
                            <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Edición</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Personas invitadas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        <tr class="hover:bg-gray-600 transition-colors duration-150">
                            <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $list->name }}</td>
                            <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $list->edition?->event?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-300 whitespace-nowrap">
                                {{ $list->edition ? $list->edition->date->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-teal-400 font-semibold whitespace-nowrap">{{ count($currentPersonIds) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- List info card --}}
        <div class="bg-gray-700 rounded-lg p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Cartera</p>
            <p class="text-white font-semibold">{{ $list->clientPorfolio->name }}</p>
            <p class="text-gray-400 text-sm mt-1">
                {{ $portfolioPersons->count() }} personas en la cartera
                &middot;
                <span class="text-teal-400 font-medium">{{ count($currentPersonIds) }} en esta lista</span>
            </p>
            @if ($list->invitationsCapacity() !== null)
                <p class="mt-2 inline-flex items-center gap-1.5 text-sm text-yellow-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Capacidad de invitaciones asignada: <strong>{{ $list->invitationsCapacity() }}</strong>
                </p>
            @endif
            @if ($list->isSent())
                <p class="mt-2 inline-flex items-center gap-1.5 text-sm text-teal-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Invitaciones enviadas el <strong>{{ $list->sent_at->format('d/m/Y H:i') }}</strong>
                </p>
            @endif
        </div>

        @if ($list->isSent())
            {{-- Once sent, the list and its per-person register distribution are frozen. --}}
            <div class="bg-gray-700 rounded-lg overflow-hidden">
                <div class="px-5 py-3 bg-gray-600 border-b border-gray-500">
                    <span class="text-sm text-gray-300 font-medium">Personas invitadas</span>
                </div>
                <ul class="divide-y divide-gray-600">
                    @foreach ($list->persons as $person)
                        <li class="flex items-center gap-4 px-5 py-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-sm font-medium">
                                    {{ $person->name }} {{ $person->surname }}
                                </p>
                                <p class="text-gray-400 text-xs truncate">
                                    {{ $person->email }} &middot; {{ $person->phone }}
                                </p>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">
                                {{ $person->pivot->registrations_used }} / {{ $person->pivot->allowed_registrations }} registros usados
                            </span>
                            <form method="POST"
                                  action="{{ route('invitation-list-resend-code', ['list' => $list->id, 'person' => $person->id]) }}"
                                  class="shrink-0">
                                @csrf
                                <button type="submit"
                                        title="Reenviar código de verificación"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-teal-400 hover:bg-gray-500 transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                         stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
        {{--
            Alpine component: identical logic to the create view, but `selected` is
            pre-populated with the IDs already on the list.
            On submit the controller calls sync(), which diffs the old and new sets
            automatically — no need to track additions and removals separately here.
        --}}
        <div x-data='{
            selected: @json($currentPersonIds),
            registrations: @json($currentRegistrations),
            capacity: {{ $list->invitationsCapacity() ?? 'null' }},
            get totalRegistrations() {
                return this.selected.reduce((sum, id) => sum + (parseInt(this.registrations[id]) || 0), 0);
            },
            get overCapacity() { return this.capacity !== null && this.totalRegistrations > this.capacity; },
            get canSubmit()    { return !this.overCapacity; },

            togglePerson(id) {
                const idx = this.selected.indexOf(id);
                if (idx === -1) {
                    this.selected.push(id);
                    if (this.registrations[id] === undefined) this.registrations[id] = 1;
                } else {
                    this.selected.splice(idx, 1);
                }
            },
            isSelected(id) { return this.selected.includes(id); },

            toggleAll(personIds) {
                const allChecked = personIds.every(id => this.selected.includes(id));
                if (allChecked) {
                    this.selected = this.selected.filter(id => !personIds.includes(id));
                } else {
                    personIds.forEach(id => {
                        if (!this.selected.includes(id)) this.selected.push(id);
                        if (this.registrations[id] === undefined) this.registrations[id] = 1;
                    });
                }
            },
            allSelected(personIds) {
                return personIds.length > 0 && personIds.every(id => this.selected.includes(id));
            }
        }'>
            <form method="POST" action="{{ route('invitation-list-update', $list->id) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- ── List name ───────────────────────────────────────────────────────── --}}
                <div class="bg-gray-700 rounded-lg p-5">
                    <x-form-label for="name">Nombre de la lista</x-form-label>
                    <x-form-input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $list->name) }}"
                        required />
                    <x-form-error name="name" />
                </div>

                {{-- ── Person list ─────────────────────────────────────────────────────── --}}
                @php
                    $allPersonIds = $portfolioPersons->pluck('id')->values()->all();
                @endphp

                <div class="bg-gray-700 rounded-lg overflow-hidden">

                    {{-- Select-all header --}}
                    <div class="px-5 py-3 bg-gray-600 border-b border-gray-500 flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                :checked="allSelected(@json($allPersonIds))"
                                @change="toggleAll(@json($allPersonIds))"
                                class="w-4 h-4 accent-teal-500">
                            <span class="text-sm text-gray-300 font-medium">Seleccionar todos</span>
                        </label>
                        <span class="text-sm text-gray-400">{{ $portfolioPersons->count() }} personas</span>
                    </div>

                    @if ($portfolioPersons->isEmpty())
                        <p class="px-5 py-8 text-gray-400 text-sm text-center">
                            Este portfolio no tiene personas asociadas.
                        </p>
                    @else
                        <ul class="divide-y divide-gray-600">
                            @foreach ($portfolioPersons as $person)
                                <li class="hover:bg-gray-600 transition-colors flex items-center gap-4 px-5 py-3">
                                    {{--
                                        The label makes the checkbox + person info clickable.
                                        :checked / @change are used instead of x-model to keep
                                        the selected array as integers, avoiding type mismatches.
                                        The registrations input below is a SIBLING of this label
                                        (not nested inside it) — a nested <label> would be invalid
                                        HTML and some browsers forward its clicks to the checkbox,
                                        silently deselecting the person while editing their count.
                                    --}}
                                    <label class="flex items-center gap-4 flex-1 min-w-0 cursor-pointer select-none">
                                        <input type="checkbox"
                                            name="persons[]"
                                            value="{{ $person->id }}"
                                            :checked="isSelected({{ $person->id }})"
                                            @change="togglePerson({{ $person->id }})"
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

                                    {{-- Shows only when this person is currently selected --}}
                                    <div x-show="isSelected({{ $person->id }})" x-cloak
                                         class="flex items-center gap-1.5 shrink-0">
                                        <span class="text-xs text-gray-400 whitespace-nowrap">Registros</span>
                                        <input type="number"
                                            name="registrations[{{ $person->id }}]"
                                            x-model.number="registrations[{{ $person->id }}]"
                                            :disabled="!isSelected({{ $person->id }})"
                                            min="0"
                                            class="w-16 bg-gray-800 border border-gray-500 rounded px-2 py-1 text-white text-sm text-center">
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                {{-- ── Footer: live counter + submit ──────────────────────────────────── --}}
                <div class="bg-gray-700 rounded-lg p-5 flex items-center justify-between gap-4 flex-wrap">
                    <div class="space-y-1 text-sm">
                        <p class="text-gray-300">
                            <span class="text-white font-semibold" x-text="selected.length"></span>
                            <span x-text="selected.length === 1 ? ' persona en la lista' : ' personas en la lista'"></span>
                            @if ($list->invitationsCapacity() !== null)
                                <span class="mx-1 text-gray-500">&middot;</span>
                                <span class="text-gray-400">
                                    <span x-text="totalRegistrations"></span> registros asignados
                                </span>
                                <span class="mx-1 text-gray-500">&middot;</span>
                                <span :class="overCapacity ? 'text-red-400 font-semibold' : 'text-gray-400'">
                                    <span x-text="capacity - totalRegistrations"></span> restantes
                                </span>
                            @endif
                        </p>
                        <p x-show="overCapacity" x-cloak class="text-red-400 text-xs">
                            Has superado tu límite de invitaciones para esta edición.
                        </p>

                        @error('persons')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        :disabled="!canSubmit"
                        :class="canSubmit
                            ? 'bg-teal-700 hover:bg-teal-600 cursor-pointer'
                            : 'bg-gray-500 cursor-not-allowed opacity-60'"
                        class="text-white font-semibold px-6 py-2.5 rounded-lg transition-colors duration-150 shrink-0">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('invitation-list-send', $list->id) }}">
            @csrf
            <button type="submit"
                    class="w-full bg-teal-700 hover:bg-teal-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors duration-150">
                Enviar invitaciones
            </button>
        </form>
        @endif

    </div>
</x-layout>
