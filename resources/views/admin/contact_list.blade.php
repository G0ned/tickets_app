<x-layout>
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
                                <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Portfolio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            @php
                                $type_labels = [
                                    'employee' => 'Empleado',
                                    'client' => 'Cliente',
                                    'outsider' => 'Externo',
                                ];
                            @endphp
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
