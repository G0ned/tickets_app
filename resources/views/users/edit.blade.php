<x-layout>
    @section('title', 'Editar usuario')
    <x-slot:heading>Editar usuario</x-slot:heading>

    <div class="max-w-xl mx-auto">
        <div class="bg-gray-700 rounded-xl shadow-xl p-8">
            <form method="POST" action="{{ route('user-update', $user->id) }}" class="space-y-5">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-form-label for="name">Nombre</x-form-label>
                        <x-form-input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ $user->name }}"
                            placeholder="Nombre"
                            required />
                        <x-form-error name="name" />
                    </div>

                    <div>
                        <x-form-label for="surname">Apellidos</x-form-label>
                        <x-form-input
                            type="text"
                            id="surname"
                            name="surname"
                            value="{{ $user->surname }}"
                            placeholder="Apellidos"
                            required />
                        <x-form-error name="surname" />
                    </div>
                </div>

                <div>
                    <x-form-label for="email">Correo electrónico</x-form-label>
                    <x-form-input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ $user->email }}"
                        placeholder="correo@ejemplo.com"
                        required />
                    <x-form-error name="email" />
                </div>

                <div class="border-t border-gray-600 pt-5 space-y-3">
                    <p class="text-sm font-medium text-gray-300">Roles</p>

                    <label class="flex items-center gap-3 cursor-pointer select-none
                                  bg-gray-600 hover:bg-gray-500 px-4 py-3 rounded-lg
                                  border border-gray-500 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_admin"
                               value="1"
                               {{ $user->is_admin ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-500">
                        <div>
                            <p class="text-white text-sm font-semibold">Administrador</p>
                            <p class="text-gray-400 text-xs">Acceso completo: crear eventos, gestionar usuarios y ediciones.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer select-none
                                  bg-gray-600 hover:bg-gray-500 px-4 py-3 rounded-lg
                                  border border-gray-500 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_supervisor"
                               value="1"
                               {{ $user->is_supervisor ? 'checked' : '' }}
                               class="w-4 h-4 accent-teal-500">
                        <div>
                            <p class="text-white text-sm font-semibold">Supervisor</p>
                            <p class="text-gray-400 text-xs">Puede supervisar ediciones y gestionar listas de invitados.</p>
                        </div>
                    </label>
                </div>

                <div class="pt-2">
                    <x-form-button>Guardar cambios</x-form-button>
                </div>
            </form>

            <div class="mt-8 border-t border-gray-600 pt-6">
                <p class="text-sm font-medium text-gray-300 mb-3">Eventos organizados</p>
                @if ($user->organized_events->isEmpty())
                    <p class="text-gray-400 text-sm mb-6">No organiza ningún evento todavía.</p>
                @else
                    <ul class="divide-y divide-gray-600 rounded-lg overflow-hidden mb-6 border border-gray-500">
                        @foreach ($user->organized_events as $organizedEvent)
                            <li class="flex items-center justify-between bg-gray-600 px-4 py-3">
                                <span class="text-white font-medium text-sm">{{ $organizedEvent->name }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="bg-indigo-600 text-white text-xs font-semibold px-2 py-1 rounded-full">Organizador</span>
                                    <form method="POST"
                                          action="{{ route('remove-organizer', [$organizedEvent->id, $user->id]) }}"
                                          onsubmit="return confirm('¿Quitar a {{ $user->name }} {{ $user->surname }} como organizador de {{ $organizedEvent->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Quitar organizador"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-300 hover:text-red-400 hover:bg-gray-500 transition-colors duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="bg-gray-600 rounded-xl p-6">
                    <h4 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Asignar como organizador</h4>
                    <form method="POST" action="{{ route('user-assign-organizer', $user->id) }}" class="space-y-5">
                        @csrf
                        <div class="mb-2">
                            <x-form-label for="event_id">Evento</x-form-label>
                            <select name="event_id" id="event_id" required
                                class="mt-1 block w-full px-3 py-2 bg-gray-700 text-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">— Selecciona un evento —</option>
                                @foreach ($events as $eventOption)
                                    <option value="{{ $eventOption->id }}">{{ $eventOption->name }}</option>
                                @endforeach
                            </select>
                            <x-form-error name="event_id" />
                        </div>
                        <div class="col-span-2 max-w-sm mx-auto">
                            <x-form-button>Asignar organizador</x-form-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
