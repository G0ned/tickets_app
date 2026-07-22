<x-layout>
    @section('title', 'Crear usuario')
    <x-slot:heading>Crear nuevo usuario</x-slot:heading>

    <div class="max-w-xl mx-auto">
        <div class="bg-gray-700 rounded-xl shadow-xl p-8">
            <form method="POST" action="{{ route('user-store') }}" class="space-y-5">
                @csrf

                {{-- ── Personal data ───────────────────────────────────────────────── --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-form-label for="name">Nombre</x-form-label>
                        <x-form-input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
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
                            value="{{ old('surname') }}"
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
                        value="{{ old('email') }}"
                        placeholder="correo@ejemplo.com"
                        required />
                    <x-form-error name="email" />
                </div>

                {{-- ── Password ─────────────────────────────────────────────────────── --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-form-label for="password">Contraseña</x-form-label>
                        <x-form-input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            required />
                        <x-form-error name="password" />
                    </div>

                    <div>
                        {{-- The confirmed rule checks that this field matches password --}}
                        <x-form-label for="password_confirmation">Confirmar contraseña</x-form-label>
                        <x-form-input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repite la contraseña"
                            required />
                    </div>
                </div>

                {{-- ── Roles ────────────────────────────────────────────────────────── --}}
                <div class="border-t border-gray-600 pt-5 space-y-3">
                    <p class="text-sm font-medium text-gray-300">Roles</p>

                    {{--
                        Unchecked checkboxes are not submitted by the browser.
                        The controller uses $request->boolean() which returns false
                        when the key is absent, so no hidden input is needed.
                    --}}
                    <label class="flex items-center gap-3 cursor-pointer select-none
                                  bg-gray-600 hover:bg-gray-500 px-4 py-3 rounded-lg
                                  border border-gray-500 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_admin"
                               value="1"
                               {{ old('is_admin') ? 'checked' : '' }}
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
                               {{ old('is_supervisor') ? 'checked' : '' }}
                               class="w-4 h-4 accent-teal-500">
                        <div>
                            <p class="text-white text-sm font-semibold">Supervisor</p>
                            <p class="text-gray-400 text-xs">Puede supervisar ediciones y gestionar listas de invitados.</p>
                        </div>
                    </label>
                </div>

                {{-- ── Event roles ──────────────────────────────────────────────────── --}}
                <div class="border-t border-gray-600 pt-5 space-y-3">
                    <p class="text-sm font-medium text-gray-300">Asignar a un evento (opcional)</p>

                    <div>
                        <x-form-label for="event_id">Evento</x-form-label>
                        <select name="event_id" id="event_id"
                            class="mt-1 block w-full px-3 py-2 bg-gray-700 text-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— Selecciona un evento —</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                            @endforeach
                        </select>
                        <x-form-error name="event_id" />
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer select-none
                                  bg-gray-600 hover:bg-gray-500 px-4 py-3 rounded-lg
                                  border border-gray-500 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_organizer"
                               value="1"
                               {{ old('is_organizer') ? 'checked' : '' }}
                               class="w-4 h-4 accent-indigo-500">
                        <div>
                            <p class="text-white text-sm font-semibold">Organizador</p>
                            <p class="text-gray-400 text-xs">Puede editar el evento seleccionado y asignar porteros.</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer select-none
                                  bg-gray-600 hover:bg-gray-500 px-4 py-3 rounded-lg
                                  border border-gray-500 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_doorman"
                               value="1"
                               {{ old('is_doorman') ? 'checked' : '' }}
                               class="w-4 h-4 accent-emerald-500">
                        <div>
                            <p class="text-white text-sm font-semibold">Portero</p>
                            <p class="text-gray-400 text-xs">Solo tiene acceso al escáner de entradas del evento seleccionado.</p>
                        </div>
                    </label>
                </div>

                {{-- ── Submit ───────────────────────────────────────────────────────── --}}
                <div class="pt-2">
                    <x-form-button>Crear usuario</x-form-button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
