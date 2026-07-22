<x-layout>
     @php
        $type_labels = [
            'employee' => 'Empleado',
            'client' => 'Cliente',
            'outsider' => 'Externo',
        ];
    @endphp
    @section('title', 'Lista de contactos')
    <x-slot:heading>Contactos registrados</x-slot:heading>
    @if ($people->isEmpty())
            <div class="bg-gray-700 rounded-lg p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-10 text-gray-400 mx-auto mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <p class="text-gray-300 text-sm">No hay contactos registrados.</p>
            </div>
    @else
        <form method="GET" action="{{ route('contacts-index') }}" class="mb-4 flex flex-wrap items-center gap-3">
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
        <form method="POST"
              action="{{ route('contacts-delete') }}"
              x-data="{ selected: [], ids: [{{ $people->pluck('id')->implode(',') }}] }"
              onsubmit="return selected.length > 0 && confirm('¿Seguro que quieres eliminar los ' + selected.length + ' contacto(s) seleccionado(s)? Esta acción no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <div class="flex justify-end mb-3">
                <x-delete-button type="submit" x-bind:disabled="selected.length === 0" class="disabled:opacity-40 disabled:cursor-not-allowed">
                    Eliminar seleccionados
                </x-delete-button>
            </div>
            <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-600 bg-gray-600">
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">
                                    <input type="checkbox"
                                           x-bind:checked="selected.length === ids.length"
                                           @change="selected = $event.target.checked ? [...ids] : []">
                                </th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Nombre</th>
                                <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Apellidos</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">e-mail</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Telefono</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Tipo</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Marca</th>
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Portfolio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @foreach ($people as $person)
                                <tr class="hover:bg-gray-600 transition-colors duration-150">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="person_ids[]" value="{{ $person->id }}" x-model="selected">
                                    </td>
                                    <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $person->name }}</td>
                                    <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $person->surname }}</td>
                                    <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">{{ $person->email }}</td>
                                    <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">{{ $person->phone }}</td>
                                    <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">
                                        {{ $person->type ? $type_labels[$person->type->value] : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">
                                        {{ $person->brand ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">
                                        {{ $person->portfolio->name ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    @endif
</x-layout>
