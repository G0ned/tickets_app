<x-layout>
    @section('title', 'Nueva lista de invitaciones')
    <x-slot:heading>Lista de invitaciones — {{ $edition->event->name }}</x-slot:heading>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-gray-700 rounded-lg p-5">
            <p class="text-white font-semibold text-base">{{ $edition->event->name }}</p>
            <p class="text-gray-300 text-sm mt-1">
                {{ $edition->date->format('d/m/Y') }}
                · {{ $edition->date->format('H:i') }}
                · {{ $edition->location }}
            </p>
            @if ($managerPivot?->invitations_capacity !== null)
                <p class="mt-2 inline-flex items-center gap-1.5 text-sm text-yellow-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Capacidad de invitaciones asignada: <strong>{{ $managerPivot->invitations_capacity }}</strong>
                </p>
            @endif
        </div>

        @if ($portfolios->isEmpty())
            <div class="bg-gray-700 rounded-lg p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-10 text-gray-400 mx-auto mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                <p class="text-gray-300 text-sm">No tienes ningún portfolio de clientes asignado.</p>
                <p class="text-gray-400 text-xs mt-1">Contacta con un administrador para que te asigne uno antes de crear listas.</p>
            </div>
        @else

        <div x-data="{
            portfolioId: {{ $portfolios->first()->id }},
            selected: [],
            registrations: {},
            capacity: {{ $managerPivot?->invitations_capacity ?? 'null' }},
            get totalRegistrations() {
                return this.selected.reduce((sum, id) => sum + (parseInt(this.registrations[id]) || 0), 0);
            },
            get overCapacity() { return this.capacity !== null && this.totalRegistrations > this.capacity; },
            get canSubmit()    { return this.selected.length > 0 && !this.overCapacity; },

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
                    // Deselect only the persons of the current portfolio
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
            },

            switchPortfolio(id) {
                this.portfolioId = id;
                this.selected = []; // clear selection to keep list scoped to one portfolio
            }
        }">
            <form method="POST" action="{{ route('invitation-list-store', $edition->id) }}" class="space-y-6">
                @csrf
                <input type="hidden" name="portfolio_id" :value="portfolioId">
                <div class="bg-gray-700 rounded-lg p-5 space-y-4">
                    <div>
                        <x-form-label for="name">Nombre de la lista</x-form-label>
                        <x-form-input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Ej. VIP Empresa X"
                            value="{{ old('name') }}"
                            required />
                        <x-form-error name="name" />
                    </div>
                    @if ($portfolios->count() > 1)
                        <div>
                            <p class="text-sm font-medium text-gray-300 mb-2">Portfolio</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($portfolios as $portfolio)
                                    <button type="button"
                                        @click="switchPortfolio({{ $portfolio->id }})"
                                        :class="portfolioId === {{ $portfolio->id }}
                                            ? 'bg-teal-700 text-white border-teal-500'
                                            : 'bg-gray-600 text-gray-300 border-gray-500 hover:bg-gray-500'"
                                        class="px-4 py-2 rounded-lg border text-sm font-medium transition-colors duration-150">
                                        {{ $portfolio->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                @foreach ($portfolios as $portfolio)
                    @php
                        $personIds = $portfolio->persons->pluck('id')->values()->all();
                    @endphp
                    <div x-show="portfolioId === {{ $portfolio->id }}"
                         class="bg-gray-700 rounded-lg overflow-hidden">
                        <div class="px-5 py-3 bg-gray-600 border-b border-gray-500 flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                    :checked="allSelected(@json($personIds))"
                                    @change="toggleAll(@json($personIds))"
                                    class="w-4 h-4 accent-teal-500">
                                <span class="text-sm text-gray-300 font-medium">Seleccionar todos</span>
                            </label>
                            <span class="text-sm text-gray-400">{{ $portfolio->persons->count() }} personas</span>
                        </div>

                        @if ($portfolio->persons->isEmpty())
                            <p class="px-5 py-8 text-gray-400 text-sm text-center">Este portfolio no tiene personas asociadas.</p>
                        @else
                            <ul class="divide-y divide-gray-600">
                                @foreach ($portfolio->persons as $person)
                                    <li class="hover:bg-gray-600 transition-colors flex items-center gap-4 px-5 py-3">
                                        {{--
                                            The label makes the checkbox + person info clickable.
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
                @endforeach
                <div class="bg-gray-700 rounded-lg p-5 flex items-center justify-between gap-4 flex-wrap">
                    <div class="space-y-1 text-sm">
                        <p class="text-gray-300">
                            <span class="text-white font-semibold" x-text="selected.length"></span>
                            <span x-text="selected.length === 1 ? ' persona seleccionada' : ' personas seleccionadas'"></span>
                            @if ($managerPivot?->invitations_capacity !== null)
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
                        Crear lista
                    </button>
                </div>

            </form>
        </div>
        @endif

    </div>
</x-layout>
