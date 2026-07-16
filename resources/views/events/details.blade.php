<x-layout>
    @section('title', 'Mostrar')
    <x-slot:heading>Detalles del Evento</x-slot:heading>

    {{-- Botón volver --}}
    <div class="mb-6">
        <x-button href="{{ route('events-index') }}">Volver a Eventos</x-button>
    </div>

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Tarjeta principal: imagen + info --}}
        <div class="bg-gray-700 p-4 sm:p-8 rounded-xl shadow-xl">
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">

                {{-- Imagen --}}
                <div class="w-full sm:w-48 sm:shrink-0">
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
                <div class="flex-1 space-y-4 min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-white break-words">{{ $event->name }}</h2>

                    <div>
                        <span class="text-gray-400 text-sm uppercase tracking-wide">Descripción</span>
                        <p class="text-white mt-1 break-words">{{ $event->description }}</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">
                        <div>
                            <span class="text-gray-400 text-sm uppercase tracking-wide">Creado por</span>
                            <p class="text-white font-semibold mt-1">{{ $event->createdBy->name }}</p>
                        </div>
                        <div>
                            @foreach($event->organizers as $organizer)
                            {
                                <p class="text-white font-semibold mt-1">
                                    {{ $organizer->name }}
                                </p>
                            }
                            @endforeach
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div class="mt-1">
                                <x-button href="{{route('events-edit', $event->id)}}">
                                    Editar evento
                                </x-button>
                            </div>


                            @admin()
                            <form action="{{route('events-delete', $event->id)}}" method="POST" class="mt-1" onsubmit="return confirm('¿Seguro que quieres eliminar el evento &quot;{{ $event->name }}&quot;? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <x-delete-button type="submit">Eliminar evento</x-delete-button>
                            </form>
                            @endadmin

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
        <div class="bg-gray-700 p-4 sm:p-8 rounded-xl shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h3 class="text-lg font-bold text-white">Ediciones del Evento</h3>
                @admin()
                <x-button href="{{ route('editions-create', $event->id) }}">
                    + Nueva Edición
                </x-button>
                @endadmin
            </div>

            @if($event->editions->isEmpty())
                <p class="text-gray-400 text-center py-8">No hay ediciones creadas aún.</p>
            @else
                {{-- Vista de tarjetas apiladas (móvil) --}}
                <div class="grid grid-cols-1 gap-4 sm:hidden">
                    @foreach($event->editions as $edition)
                        <div class="bg-gray-800 rounded-lg p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-xs uppercase tracking-wide">Edición #{{ $edition->id }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs uppercase tracking-wide">Fecha</span>
                                <p class="text-white">{{ $edition->date->format('d-m-Y') }} - {{ $edition->date->format('H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs uppercase tracking-wide">Duración</span>
                                <p class="text-white">{{ $edition->duration }} min</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs uppercase tracking-wide">Ubicación</span>
                                <p class="text-white">{{ $edition->location ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs uppercase tracking-wide">Aforo</span>
                                <p class="text-white">{{ $edition->capacity }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-xs uppercase tracking-wide">Nº Registros</span>
                                <p class="text-white">{{ $edition->attendees->count() }}</p>
                            </div>
                            <div class="flex flex-col gap-2 pt-2">
                                <x-button href="{{route('editions-edit', $edition->id)}}" class="block w-full text-center">Editar</x-button>
                                @admin()
                                <x-button href="{{route('edition-attendees', $edition->id)}}" class="block w-full text-center">Ver asistentes</x-button>
                                @endadmin
                                @admin()
                                <form action="{{route('editions-delete', $edition->id)}}" method="POST"
                                    onsubmit="return confirm('¿Seguro que quieres eliminar esta edición?. Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <x-delete-button type="submit" class="w-full">Eliminar</x-delete-button>
                                </form>
                                @endadmin
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Vista de tabla (tablet y superior) --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-600">
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">#</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Fecha</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Hora</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Duración</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Ubicación</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Aforo</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Nº Registros</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Acciones</th>
                                @admin()
                                <th class="px-4 py-3 text-left text-gray-400 text-sm uppercase tracking-wide whitespace-nowrap">Asistentes</th>
                                @endadmin
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @foreach($event->editions as $edition)
                                <tr class="hover:bg-gray-600 transition-colors duration-150">
                                    <td class="px-2 py-3 text-white whitespace-nowrap">{{ $edition->id }}</td>
                                    <td class="px-2 py-3 text-white whitespace-nowrap">{{ $edition->date->format('d-m-Y') }}</td>
                                    <td class="px-2 py-3 text-white whitespace-nowrap">{{ $edition->date->format('H:i') }}</td>
                                    <td class="px-2 py-3 text-white whitespace-nowrap">{{ $edition->duration }} min</td>
                                    <td class="px-2 py-3 text-white whitespace-nowrap">{{ $edition->location ?? '-' }}</td>
                                    <td class="px-2 py-3 text-white whitespace-nowrap">{{ $edition->capacity }}</td>
                                    <td class="px-2 py-3 text-white whitespace-nowrap">{{ $edition->attendees->count() }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{route('editions-edit', $edition->id)}}"
                                               title="Editar"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-teal-400 hover:bg-gray-500 transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </a>
                                            @admin()
                                            <form action="{{route('editions-delete', $edition->id)}}" method="POST"
                                                onsubmit="return confirm('¿Seguro que quieres eliminar esta edición?. Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Eliminar"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-red-400 hover:bg-gray-500 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endadmin
                                        </div>
                                    </td>
                                    @admin()
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{route('edition-attendees', $edition->id)}}"
                                           title="Ver asistentes"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-teal-400 hover:bg-gray-500 transition-colors duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                            </svg>
                                        </a>
                                    </td>
                                    @endadmin
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-layout>