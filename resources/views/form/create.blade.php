<x-layout>
    @section('title', 'Formulario de registro')
    <x-slot:title>Registro de Asistentes</x-slot:title>
    <div>
        <div class="max-w-4xl mx-auto px-0 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-2 sm:p-8">
                <div class="mb-8">
                    <x-slot:heading>Registro de Asistentes</x-slot:heading>
                </div>

                <div class="max-w-2xl mx-auto px-0 sm:px-2">
                    <div class="bg-gray-700 p-3 sm:p-8 rounded-xl shadow-xl">
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">
                    <div class="w-full sm:w-48 sm:shrink-0">
                        @if($event->poster_path)
                            <img
                                src="{{ Storage::url($event->poster_path) }}"
                                alt="Poster del evento {{ $event->name }}"
                                class="w-full h-48 object-cover rounded-lg text-white shadow-md"
                            >
                        @else
                            <div class="w-full h-48 bg-gray-600 rounded-lg flex items-center justify-center">
                                <span class="text-gray-400 text-sm">Sin cartel</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-4 min-w-0">
                        <h2 class="text-xl sm:text-2xl font-bold text-white wrap-break-words">{{ $event->name }}</h2>

                        <div>
                            <span class="text-gray-400 text-sm uppercase tracking-wide">Descripción</span>
                            <p class="text-white mt-1 wrap-break-words">{{ $event->description }}</p>
                        </div>
                        <hr class="border-white my-6">
                    </div>
            </div>
        </div>
                <form method="POST" action="{{route('event-signup-store', $event->id)}}" class="space-y-8 rounded-sm shadow-lg">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 p-2 sm:p-4">

                        <label for="editions">
                            <div class="flex items-center">

                                <select id="editions" name="editions" class="block w-full rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                    <option value="" disabled {{ old('editions') === null ? 'selected' : '' }}>--- Seleccione la edición a la que dese asistir ---</option>
                                    @foreach($event->editions as $edition)
                                    <option value="{{$edition->id}}" {{ old('editions') == $edition->id ? 'selected' : '' }}>Fecha: {{$edition->date->format('d-m-Y')}} - Hora {{$edition->date->format('H:i')}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </label>
                        <x-form-error name="editions" />

                    </div>

                    @include('form._attendee-fields')
                </form>
            </div>
        </div>
    </div>
</x-layout>