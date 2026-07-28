<x-layout>
    @section('title', 'Asistentes — ' . $edition->event->name)
    <x-slot:heading>Asistentes registrados</x-slot:heading>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-gray-700 rounded-lg p-5 flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-white font-semibold text-base">{{ $edition->event->name }}</p>
                <p class="text-gray-300 text-sm mt-1">
                    {{ $edition->date->format('d/m/Y') }}
                    · {{ $edition->date->format('H:i') }}
                    · {{ $edition->location }}
                </p>
            </div>
            <div class="flex items-center gap-6 text-sm">
                <div class="text-center">
                    <p class="text-gray-400 text-xs uppercase tracking-wide">Aforo</p>
                    <p class="text-white font-semibold text-lg">{{ $edition->capacity }}</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-400 text-xs uppercase tracking-wide">Registrados</p>
                    <p class="text-teal-400 font-semibold text-lg">{{ $edition->attendees->count() }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <x-button href="{{ url()->previous() }}">← Volver</x-button>
            <div class="flex items-center gap-2">
                <x-button href="{{ route('export-attendees', ['edition' => $edition->id, 'format' => 'csv']) }}">Exportar CSV</x-button>
                <x-button href="{{ route('export-attendees', ['edition' => $edition->id, 'format' => 'xlsx']) }}">Exportar Excel</x-button>
            </div>
        </div>

        {{-- Attendees table --}}
        @if($edition->attendees->isEmpty())
            <div class="bg-gray-700 rounded-lg p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-10 text-gray-400 mx-auto mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                <p class="text-gray-300 text-sm">No hay asistentes registrados en esta edición.</p>
            </div>
        @else
            <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-600 bg-gray-600">
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Nombre</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Apellidos</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Email</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Teléfono</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Pasaporte / ID</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Publicidad</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Comunicaciones</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Imagen</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Privacidad</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Asistencia</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Check-in</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @foreach($edition->attendees as $attendee)
                                <tr class="hover:bg-gray-600 transition-colors duration-150">
                                    <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $attendee->name }}</td>
                                    <td class="px-4 py-3 text-white whitespace-nowrap">{{ $attendee->surname }}</td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $attendee->email }}</td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $attendee->phone }}</td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap font-mono">{{ $attendee->passport ?? '-' }}</td>

                                    @php
                                        $yes = '<span class="inline-flex items-center justify-center w-6 h-6 bg-green-600 rounded-full text-white text-xs font-bold">✓</span>';
                                        $no  = '<span class="inline-flex items-center justify-center w-6 h-6 bg-red-700  rounded-full text-white text-xs font-bold">✗</span>';
                                    @endphp

                                    <td class="px-4 py-3 text-center">{!! $attendee->pivot->auth_for_ad     ? $yes : $no !!}</td>
                                    <td class="px-4 py-3 text-center">{!! $attendee->pivot->auth_for_comms  ? $yes : $no !!}</td>
                                    <td class="px-4 py-3 text-center">{!! $attendee->pivot->auth_image_rights ? $yes : $no !!}</td>
                                    <td class="px-4 py-3 text-center">{!! $attendee->pivot->privacy_policy  ? $yes : $no !!}</td>
                                    <td class="px-4 py-3 text-center">{!! $attendee->pivot->attendance      ? $yes : $no !!}</td>

                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">
                                        {{ $attendee->pivot->checked_in_at
                                            ? \Carbon\Carbon::parse($attendee->pivot->checked_in_at)->format('d/m/Y H:i')
                                            : '—' }}
                                    </td>

                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('ticket-download', ['edition' => $edition->id, 'attendee' => $attendee->id]) }}"
                                               title="Descargar ticket"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-teal-400 hover:bg-gray-500 transition-colors duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 12m0 0 4.5-4.5M12 12V3" />
                                                </svg>
                                            </a>
                                            @admin()
                                            <form action="{{route('cancel-attendee-edition', ['edition' => $edition->id, 'attendee' => $attendee->id])}}" method="POST" onsubmit="return confirm('¿Seguro que quieres cancelar de este asistente a la edición?. Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Eliminar asistente de la edición"
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-red-400 hover:bg-gray-500 transition-colors duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                            @endadmin()
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layout>
