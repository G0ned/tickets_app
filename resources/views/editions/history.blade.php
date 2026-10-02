<x-layout>
    @section('title', 'Historial de inscripciones — ' . $edition->event->name)
    <x-slot:heading>Historial de inscripciones canceladas</x-slot:heading>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-gray-700 rounded-lg p-5">
            <p class="text-white font-semibold text-base">{{ $edition->event->name }}</p>
            <p class="text-gray-300 text-sm mt-1">
                Edición #{{ $edition->id }}
                · {{ $edition->date->format('d/m/Y') }}
                · {{ $edition->date->format('H:i') }}
                · {{ $edition->location }}
            </p>
            <p class="text-gray-400 text-xs mt-3">
                Registro permanente de quién estaba inscrito en esta edición justo antes de cada reactivación —
                esas inscripciones se borran de la lista activa al reactivar (para que la edición empiece de cero),
                pero quedan conservadas aquí indefinidamente.
            </p>
        </div>

        <div>
            <x-button href="{{ route('events-show', $edition->event_id) }}">← Volver al evento</x-button>
        </div>

        @if($history->isEmpty())
            <div class="bg-gray-700 rounded-lg p-10 text-center">
                <p class="text-gray-300 text-sm">Esta edición no se ha reactivado nunca, así que no hay ningún histórico que mostrar.</p>
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
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Pasaporte / ID</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Asistencia</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Invitado</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Invitado por</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Registrado el</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Archivado el</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @foreach($history as $row)
                                @php
                                    $inviter = $row->is_guest ? $row->verificationCode?->person : null;
                                @endphp
                                <tr class="hover:bg-gray-600 transition-colors duration-150">
                                    <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $row->attendee?->name ?? '(contacto eliminado)' }}</td>
                                    <td class="px-4 py-3 text-white whitespace-nowrap">{{ $row->attendee?->surname }}</td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $row->attendee?->email }}</td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap font-mono">{{ $row->attendee?->passport ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row->attendance)
                                            <span class="inline-flex items-center justify-center w-6 h-6 bg-green-600 rounded-full text-white text-xs font-bold">✓</span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-6 h-6 bg-red-700 rounded-full text-white text-xs font-bold">✗</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row->is_guest)
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-amber-600 text-white text-xs font-bold whitespace-nowrap">Invitado</span>
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">
                                        {{ $inviter ? trim($inviter->name . ' ' . $inviter->surname) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">
                                        {{ $row->registered_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">
                                        {{ $row->archived_at->format('d/m/Y H:i') }}
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
