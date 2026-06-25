<x-layout>
    @section('title', $list->name)
    <x-slot:heading>{{ $list->name }}</x-slot:heading>

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- List info card --}}
        <div class="bg-gray-700 rounded-lg p-5">
            <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Portfolio</p>
            <p class="text-white font-semibold">{{ $list->clientPorfolio->name }}</p>
            <p class="text-gray-400 text-sm mt-1">
                {{ $portfolioPersons->count() }} personas en el portfolio
                &middot;
                <span class="text-teal-400 font-medium">{{ count($currentPersonIds) }} en esta lista</span>
            </p>
        </div>

        {{--
            Alpine component: identical logic to the create view, but `selected` is
            pre-populated with the IDs already on the list.
            On submit the controller calls sync(), which diffs the old and new sets
            automatically — no need to track additions and removals separately here.
        --}}
        <div x-data="{
            selected: @json($currentPersonIds),

            togglePerson(id) {
                const idx = this.selected.indexOf(id);
                idx === -1 ? this.selected.push(id) : this.selected.splice(idx, 1);
            },
            isSelected(id) { return this.selected.includes(id); },

            toggleAll(personIds) {
                const allChecked = personIds.every(id => this.selected.includes(id));
                if (allChecked) {
                    this.selected = this.selected.filter(id => !personIds.includes(id));
                } else {
                    personIds.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id); });
                }
            },
            allSelected(personIds) {
                return personIds.length > 0 && personIds.every(id => this.selected.includes(id));
            }
        }">
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
                                <li class="hover:bg-gray-600 transition-colors">
                                    {{--
                                        The label makes the whole row clickable.
                                        :checked / @change are used instead of x-model to keep
                                        the selected array as integers, avoiding type mismatches.
                                    --}}
                                    <label class="flex items-center gap-4 px-5 py-3 cursor-pointer w-full select-none">
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

                                        {{-- Shows only when this person is currently selected --}}
                                        <span x-show="isSelected({{ $person->id }})"
                                              x-cloak
                                              class="text-xs font-semibold text-teal-400 shrink-0">
                                            En lista
                                        </span>
                                    </label>
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
                        </p>

                        @error('persons')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="bg-teal-700 hover:bg-teal-600 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors duration-150 shrink-0">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>

    </div>
</x-layout>
