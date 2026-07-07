<x-layout>
    @section('title', 'Formulario de registro')
    <x-slot:title>Registro de Asistentes</x-slot:title>
    <div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
                <div class="mb-8">
                    <x-slot:heading>Registro de Asistentes</x-slot:heading>
                </div>

                <diV class="max-w-2xl mx-auto px-2">
                    <div class="bg-gray-700 p-8 rounded-xl shadow-xl">
                    <div class="flex gap-8">
                    <div class="w-48 shrink-0">
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
                    <div class="flex-1 space-y-4">
                        <h2 class="text-2xl font-bold text-white">{{ $event->name }}</h2>

                        <div>
                            <span class="text-gray-400 text-sm uppercase tracking-wide">Descripción</span>
                            <p class="text-white mt-1">{{ $event->description }}</p>
                        </div>
                        <hr class="bg-white my-6">
                    </div>
            </div>
        </div>
                <form method="POST" action="{{route('event-signup-store', $event->id)}}" class="space-y-8 rounded-sm shadow-lg">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 p-4">
                    
                        <label for="editions">
                            <div class="fles items-center">
                            
                                <select id="editions" name="editions" value="{{ old('editions') }}" class="block rounded-md border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="" disabled selected>--- Seleccione la edición a la que dese asistir ---</option>
                                    @foreach($event->editions as $edition)
                                    <option value="{{$edition->id}}">Fecha: {{$edition->date->format('d-m-Y')}} - Hora {{$edition->date->format('H:i')}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </label>

                    </div>

                    @include('form._attendee-fields')
                </form>
            </div>
        </div>
    </div>
</x-layout>