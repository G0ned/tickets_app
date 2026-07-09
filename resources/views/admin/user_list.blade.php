<x-layout>
    @section('title', 'Lista de usuarios')
    <x-slot:heading>Administracion de usuarios</x-slot:heading>
    @if ($users->isEmpty())
            <div class="bg-gray-700 rounded-lg p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-10 text-gray-400 mx-auto mb-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <p class="text-gray-300 text-sm">No hay usuarios registrados.</p>
            </div>
    @else
        <div class="bg-gray-700 rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-600 bg-gray-600">
                            <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Nombre</th>
                            <th class="px-4 py-3 text-left text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Apellidos</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">e-mail</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Admin</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Supervisor</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Editar</th>
                            <th class="px-4 py-3 text-center text-gray-400 text-xs uppercase tracking-wide whitespace-nowrap">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-600 transition-colors duration-150">
                                <td class="px-4 py-3 text-white whitespace-nowrap font-medium">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-300 whitespace-nowrap">{{ $user->surname }}</td>
                                <td class="px-4 py-3 text-gray-300 text-center whitespace-nowrap">{{ $user->email }}</td>
                                    @php
                                    $yes = '<span class="inline-flex items-center justify-center w-6 h-6 bg-green-600 rounded-full text-white text-xs font-bold">✓</span>';
                                    $no  = '<span class="inline-flex items-center justify-center w-6 h-6 bg-red-700  rounded-full text-white text-xs font-bold">✗</span>';
                                    @endphp
                                <td class="px-4 py-3 text-center">{!! $user->is_admin     ? $yes : $no !!}</td>
                                <td class="px-4 py-3 text-center">{!! $user->is_supervisor  ? $yes : $no !!}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <a href="{{ route('user-edit', $user->id) }}" class="text-center text-white px-4 py-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto hover:bg-teal-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    <form method="POST" action="{{ route('user-delete', $user->id) }}" class="inline"
                                          onsubmit="return confirm('¿Seguro que quieres eliminar al usuario &quot;{{ $user->name }} {{ $user->surname }}&quot;? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-center text-white px-4 py-3 hover:text-red-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mx-auto hover:bg-red-700">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layout>