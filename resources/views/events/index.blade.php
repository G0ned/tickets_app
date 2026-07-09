<x-layout>
    @section('title', 'Eventos')
    <x-slot:heading>Eventos Activos</x-slot:heading>

    <div class="max-w-7xl mx-auto space-y-6">
        @admin()
            <div>
                <x-button href="{{ route('events-create') }}">Crear Evento</x-button>
            </div>
        @endadmin

        @if ($events->isEmpty())
            <div class="bg-gray-700 rounded-lg p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-10 text-gray-400 mx-auto mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <p class="text-gray-300 text-sm">No hay eventos para mostrar.</p>
            </div>
        @else
            <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-600 bg-gray-600">
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Nombre</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Creador</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Visibilidad</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Detalles</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @foreach ($events as $event)
                                <tr class="hover:bg-gray-600 transition-colors duration-150">
                                    <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $event->name }}</td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $event->createdBy->name }}</td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        @if ($event->public)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-700 text-teal-100">Público</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-500 text-gray-100">Privado</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <a href="{{ route('events-show', $event->id) }}"
                                           title="Ver detalles"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-teal-400 hover:bg-gray-500 transition-colors duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                 stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                            </svg>
                                        </a>
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
