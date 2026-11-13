<x-layout>
    @section('title', 'Códigos enviados — ' . ($edition->event->name ?? 'Edición'))
    <x-slot:heading>Códigos de invitación enviados</x-slot:heading>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-gray-700 rounded-lg p-5">
            <p class="text-white font-semibold text-base">{{ $edition->event->name ?? '(evento eliminado)' }}</p>
            <p class="text-gray-300 text-sm mt-1">
                Edición #{{ $edition->id }}
                · {{ $edition->date->format('d/m/Y') }}
                · {{ $edition->date->format('H:i') }}
                · {{ $edition->location }}
                @if ($edition->trashed())
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full bg-red-900/60 text-red-300 text-xs font-semibold">Cancelada</span>
                @endif
            </p>
            <p class="text-gray-400 text-xs mt-3">
                @if ($isAdmin)
                    Clientes que han recibido invitación para esta edición, de todos los comerciales, con los códigos enviados a cada uno.
                @else
                    Clientes de tus carteras que han recibido invitación para esta edición, con los códigos enviados a cada uno.
                @endif
            </p>
        </div>

        @if ($rows->isEmpty())
            <div class="bg-gray-700 rounded-lg p-10 text-center">
                <p class="text-gray-300 text-sm">Todavía no se ha enviado ninguna invitación para esta edición.</p>
            </div>
        @else
            <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-600 bg-gray-600">
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Cliente</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Email</th>
                                @if ($isAdmin)
                                    <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Comercial</th>
                                @endif
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Lista</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Enviada el</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide">Códigos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @foreach ($rows as $row)
                                <tr class="hover:bg-gray-600 transition-colors duration-150 align-top">
                                    <td class="px-4 py-3 text-white whitespace-nowrap font-medium">
                                        {{ $row['person']->name }} {{ $row['person']->surname }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $row['person']->email }}</td>
                                    @if ($isAdmin)
                                        <td class="px-4 py-3 text-gray-300 whitespace-nowrap">
                                            {{ $row['salesrep'] ? trim($row['salesrep']->name . ' ' . $row['salesrep']->surname) : '—' }}
                                        </td>
                                    @endif
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">
                                        <a href="{{ route('invitation-list-show', $row['list']->id) }}" class="hover:text-indigo-300">{{ $row['list']->name }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $row['list']->sent_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        @include('invitation_list._codes', ['codes' => $row['codes']])
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
