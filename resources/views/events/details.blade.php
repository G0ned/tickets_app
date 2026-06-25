<x-layout>
    @section('title', 'Mostrar')
    <x-slot:heading>Detalles del Evento</x-slot:heading>

    {{-- Botón volver --}}
    <div class="mb-6">
        <x-button href="{{ route('events-index') }}">Volver a Eventos</x-button>
    </div>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Tarjeta principal: imagen + info --}}
        <div class="bg-gray-700 p-8 rounded-xl shadow-xl">
            <div class="flex gap-8">

                {{-- Imagen --}}
                <div class="w-48 shrink-0">
                    @if($event->poster_path)
                        <img
                            src="{{ Storage::url($event->poster_path) }}"
                            alt="Poster del evento {{ $event->name }}"
                            class="w-full h-48 object-cover rounded-lg shadow-md"
                        >
                    @else
                        <div class="w-full h-48 bg-gray-600 rounded-lg flex items-center justify-center">
                            <span class="text-gray-400 text-sm">Sin cartel</span>
                        </div>
                    @endif
                </div>

                {{-- Info del evento --}}
                <div class="flex-1 space-y-4">
                    <h2 class="text-2xl font-bold text-white">{{ $event->name }}</h2>

                    <div>
                        <span class="text-gray-400 text-sm uppercase tracking-wide">Descripción</span>
                        <p class="text-white mt-1">{{ $event->description }}</p>
                    </div>

                    <div class="flex gap-8">
                        <div>
                            <span class="text-gray-400 text-sm uppercase tracking-wide">Creado por</span>
                            <p class="text-white font-semibold mt-1">{{ $event->createdBy->name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400 text-sm uppercase tracking-wide">Tipo de evento</span>
                            <p class="mt-1">
                                @if($event->public)
                                    <span class="bg-green-500 text-white text-s text-center block w-fit mx-auto px-2 py-1 rounded-full">Público</span>
                                @else
                                    <span class="bg-red-500 text-white text-s text-center block w-fit mx-auto px-2 py-1 rounded-full">Privado</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <hr class="bg-white my-6">
                    <div class="grid grid-cols-2 gap-2">
                            <div class="mt-1">
                                <x-button href="{{route('events-edit', $event->id)}}">
                                    Editar evento
                                </x-button>
                            </div>


                            <form action="{{route('events-delete', $event->id)}}" method="POST" class="mt-1" onsubmit="return confirm('¿Seguro que quieres eliminar el evento &quot;{{ $event->name }}&quot;? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <x-delete-button type="submit">Eliminar evento</x-delete-button>
                            </form>

                            <div class="mt-1">
                                <x-button href="{{route('event-signup', $event->id)}}">
                                    Formulario de Registro
                                </x-button>
                            </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Tarjeta ediciones --}}
        <div class="bg-gray-700 p-8 rounded-xl shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white">Ediciones del Evento</h3>
                <x-button href="{{ route('editions-create', $event->id) }}">
                    + Nueva Edición
                </x-button>
            </div>

            @if($event->editions->isEmpty())
                <p class="text-gray-400 text-center py-8">No hay ediciones creadas aún.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-600">
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">#</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Fecha</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Hora</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Duración</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Ubicación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @foreach($event->editions as $edition)
                                <tr class="hover:bg-gray-600 transition-colors duration-150">
                                    <td class="px-4 py-3 text-white whitespace-nowrap">{{ $edition->id }}</td>
                                    <td class="px-4 py-3 text-white whitespace-nowrap">{{ $edition->date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-3 text-white whitespace-nowrap">{{ $edition->date->format('H:i') }}</td>
                                    <td class="px-4 py-3 text-white whitespace-nowrap">{{ $edition->duration }} min</td>
                                    <td class="px-4 py-3 text-white whitespace-nowrap">{{ $edition->location ?? '-' }}</td>
                                    <td class="px-4 py-3"><x-button href="{{route('editions-edit', $edition->id)}}">Editar</x-button></td>
                                    <td><form action="{{route('editions-delete', $edition->id)}}" method="POST"
                                            onsubmit="return confirm('¿Seguro que quieres eliminar esta edición?. Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <x-delete-button type="submit">Eliminar</x-delete-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-layout>