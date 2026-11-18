<x-layout>
    @php
        $type_labels = [
            'employee' => 'Empleado',
            'client' => 'Cliente',
            'outsider' => 'Externo',
        ];
        $isAdmin = auth()->user()?->isAdmin();
    @endphp
    @section('title', $portfolio->name)
    <x-slot:heading>{{ $portfolio->name }}</x-slot:heading>

    <div class="mb-6 bg-gray-600 rounded-lg p-5">
        <p class="text-sm text-gray-300">Propietario</p>
        <p class="text-lg font-semibold text-white">
            {{ $portfolio->user ? trim($portfolio->user->name . ' ' . $portfolio->user->surname) : 'Sin gestor asignado' }}
        </p>
    </div>

    <h2 class="text-white font-semibold text-base mb-3">Clientes de la cartera ({{ $totalPeople }})</h2>

    @if ($totalPeople === 0)
        <div class="bg-gray-700 rounded-lg p-10 text-center">
            <p class="text-gray-300 text-sm">Este portfolio no tiene personas asociadas.</p>
        </div>
    @else
        <form method="GET" action="{{ route('portfolios-show', $portfolio->id) }}" class="mb-4 flex flex-wrap items-center gap-3">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
            <select name="type" onchange="this.form.submit()"
                    class="px-3 py-2 bg-gray-600 border border-gray-500 rounded-md shadow-sm text-sm text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los tipos</option>
                @foreach($type_labels as $value => $label)
                    <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="brand" value="{{ $selectedBrand }}" placeholder="Buscar por marca..."
                   class="px-3 py-2 bg-gray-600 border border-gray-500 rounded-md shadow-sm text-sm text-white placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit"
                    class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Buscar
            </button>
        </form>

        <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-600 bg-gray-600">
                            <x-sortable-th column="name" :sort="$sort" :direction="$direction">Nombre</x-sortable-th>
                            <x-sortable-th column="surname" :sort="$sort" :direction="$direction">Apellidos</x-sortable-th>
                            <x-sortable-th column="email" :sort="$sort" :direction="$direction" align="center">e-mail</x-sortable-th>
                            <x-sortable-th column="phone" :sort="$sort" :direction="$direction" align="center">Telefono</x-sortable-th>
                            <x-sortable-th column="type" :sort="$sort" :direction="$direction" align="center">Tipo</x-sortable-th>
                            <x-sortable-th column="brand" :sort="$sort" :direction="$direction" align="center">Marca</x-sortable-th>
                            <th class="px-3 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Invitado</th>
                            @if ($isAdmin)
                                <th class="px-3 py-3"><span class="sr-only">Editar</span></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @forelse ($people as $person)
                            <tr class="hover:bg-gray-600 transition-colors duration-150 {{ $isAdmin ? 'cursor-pointer' : '' }}"
                                @if ($isAdmin) onclick="window.location='{{ route('contacts-show', $person->id) }}'" @endif>
                                <td class="px-3 py-3 text-white whitespace-nowrap font-medium">{{ $person->name }}</td>
                                <td class="px-3 py-3 text-gray-300 whitespace-nowrap">{{ $person->surname }}</td>
                                <td class="px-3 py-3 text-gray-300 text-center whitespace-nowrap">{{ $person->email }}</td>
                                <td class="px-3 py-3 text-gray-300 text-center whitespace-nowrap">{{ $person->phone }}</td>
                                <td class="px-3 py-3 text-gray-300 text-center whitespace-nowrap">
                                    {{ $person->type ? $type_labels[$person->type->value] : '-' }}
                                </td>
                                <td class="px-3 py-3 text-gray-300 text-center whitespace-nowrap">{{ $person->brand ?? '-' }}</td>
                                <td class="px-3 py-3 text-center">
                                    @if($person->has_been_guest)
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-amber-600 text-white text-xs font-bold whitespace-nowrap">Invitado</span>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                                @if ($isAdmin)
                                    <td class="px-3 py-3 text-center" onclick="event.stopPropagation()">
                                        <a href="{{ route('contacts-edit', $person->id) }}" title="Editar contacto"
                                           aria-label="Editar {{ $person->name }} {{ $person->surname }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-white hover:bg-gray-500 transition-colors duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-400">
                                    Ningún cliente de esta cartera coincide con los filtros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layout>
