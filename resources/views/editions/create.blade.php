<x-layout>
    @section('title', 'Create Edition')
    <x-slot:heading>Crear Edición</x-slot:heading>
    <div class="container px-5 py-10 mx-auto">
        <div class="max-w-3xl mx-auto bg-gray-700 p-8 rounded-xl shadow-xl">
            <form method="POST" action={{ route('editions-store', ['event' => $event]) }} class="space-y-6"
                  x-data="{
                      occurrences: {{ Illuminate\Support\Js::from(old('occurrences', [['date' => '', 'time' => '']])) }},
                      addOccurrence() { this.occurrences.push({ date: '', time: '' }); },
                      removeOccurrence(index) { if (this.occurrences.length > 1) this.occurrences.splice(index, 1); }
                  }">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-900/40 border border-red-700 rounded-lg p-4">
                        <ul class="list-disc list-inside text-sm text-red-300 space-y-1">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <x-form-label for="location">Ubicación</x-form-label>
                        <x-form-input
                            type="text"
                            id="location"
                            name="location"
                            placeholder="Ubicación"
                            value="{{ old('location') }}"
                            required />
                        <x-form-error name="location" />
                    </div>
                    <div>
                        <x-form-label for="duration">Duración</x-form-label>
                        <x-form-input
                            type="number"
                            id="duration"
                            name="duration"
                            value="{{old('duration')}}"
                            required />
                        <x-form-error name="duration" />
                    </div>
                    <div>
                        <x-form-label for="capacity">Aforo</x-form-label>
                        <x-form-input
                            type="number"
                            id="capacity"
                            name="capacity"
                            value="{{old('capacity')}}"
                            required />
                        <x-form-error name="capacity" />
                    </div>
                </div>

                {{-- ── Fechas y horas ──────────────────────────────────────────────── --}}
                <div>
                    <x-form-label>Fechas</x-form-label>
                    <p class="text-gray-400 text-xs mb-3">
                        Añade tantas fechas como ediciones quieras crear — todas compartirán la ubicación, duración y aforo de arriba.
                    </p>

                    <div class="space-y-3">
                        <template x-for="(occurrence, index) in occurrences" :key="index">
                            <div class="flex items-end gap-3 bg-gray-600 rounded-lg p-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Fecha</label>
                                    <input type="date" x-model="occurrence.date" :name="`occurrences[${index}][date]`" required
                                        class="block w-full px-3 py-2 bg-gray-700 text-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Hora</label>
                                    <input type="time" x-model="occurrence.time" :name="`occurrences[${index}][time]`" required
                                        class="block w-full px-3 py-2 bg-gray-700 text-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <button type="button" @click="removeOccurrence(index)" x-show="occurrences.length > 1"
                                    title="Quitar esta fecha"
                                    class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-300 hover:text-red-400 hover:bg-gray-500 transition-colors duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addOccurrence()"
                        class="mt-3 text-sm text-indigo-400 hover:text-indigo-300 font-medium">
                        + Añadir otra fecha
                    </button>
                </div>

                <div class="col-span-2 max-w-sm mx-auto">
                    <x-form-button>
                        Guardar Edición
                    </x-form-button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
