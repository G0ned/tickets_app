<x-layout>
    @section('title', $person->name . ' ' . $person->surname)
    <x-slot:heading>Perfil de contacto</x-slot:heading>

    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <x-button href="{{ route('contacts-index') }}">← Volver</x-button>
        </div>

        {{-- ── Cabecera: avatar + nombre + cartera/gestor ─────────────────────── --}}
        <div class="bg-gray-700 rounded-xl shadow-xl p-5 sm:p-6">
            <div class="flex items-center gap-4">
                <div class="shrink-0 w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-linear-to-br from-teal-500 to-indigo-600 flex items-center justify-center">
                    <span class="text-white text-xl sm:text-2xl font-bold">{{ mb_substr($person->name, 0, 1) }}{{ mb_substr($person->surname, 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <h1 class="text-white text-xl sm:text-2xl font-bold truncate">{{ $person->name }} {{ $person->surname }}</h1>
                    <p class="text-gray-400 text-sm truncate">{{ $person->email }}</p>
                </div>
            </div>

            <div class="mt-5 pt-5 border-t border-gray-600 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Cartera</p>
                    <p class="text-white font-medium">{{ $person->portfolio->name ?? 'Sin cartera' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Gestor de la cartera</p>
                    <p class="text-white font-medium">
                        @if ($person->portfolio && $person->portfolio->user)
                            {{ $person->portfolio->user->name }} {{ $person->portfolio->user->surname }}
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Resumen rápido ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-gray-700 rounded-xl p-4 text-center">
                <p class="text-2xl sm:text-3xl font-bold text-white">{{ $registrations->count() }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wide mt-1">Inscripciones</p>
            </div>
            <div class="bg-gray-700 rounded-xl p-4 text-center">
                <p class="text-2xl sm:text-3xl font-bold text-emerald-400">{{ $attendedCount }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wide mt-1">Asistidas</p>
            </div>
            <div class="bg-gray-700 rounded-xl p-4 text-center">
                <p class="text-2xl sm:text-3xl font-bold text-red-400">{{ $cancelledCount }}</p>
                <p class="text-xs text-gray-400 uppercase tracking-wide mt-1">Canceladas</p>
            </div>
        </div>

        {{-- ── Ediciones ────────────────────────────────────────────────────── --}}
        <div>
            <h2 class="text-white font-semibold text-base mb-3">Ediciones a las que se ha inscrito</h2>

            @if ($registrations->isEmpty())
                <div class="bg-gray-700 rounded-lg p-8 text-center">
                    <p class="text-gray-300 text-sm">Esta persona no se ha inscrito a ninguna edición todavía.</p>
                </div>
            @else
                {{-- Tabla: solo en pantallas medianas o más grandes --}}
                <div class="hidden md:block bg-gray-700 rounded-xl shadow-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-600 bg-gray-600">
                                    <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Evento</th>
                                    <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Fecha</th>
                                    <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Lugar</th>
                                    <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-600">
                                @foreach ($registrations as $edition)
                                    <tr class="hover:bg-gray-600 transition-colors duration-150">
                                        <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $edition->event->name }}</td>
                                        <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $edition->date->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $edition->location }}</td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @include('admin.partials.registration-status-badge', ['edition' => $edition])
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tarjetas: solo en móvil --}}
                <div class="md:hidden space-y-3">
                    @foreach ($registrations as $edition)
                        <div class="bg-gray-700 rounded-xl shadow-lg p-4">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-white font-semibold text-sm min-w-0 truncate">{{ $edition->event->name }}</p>
                                @include('admin.partials.registration-status-badge', ['edition' => $edition])
                            </div>
                            <div class="mt-2 text-xs text-gray-400 space-y-0.5">
                                <p>{{ $edition->date->format('d/m/Y H:i') }}</p>
                                <p>{{ $edition->location }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layout>
